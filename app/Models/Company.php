<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JeroenG\Explorer\Application\Explored;
use Laravel\Scout\Searchable;

class Company extends Model implements Explored
{
    use HasFactory, Searchable;
    protected $guarded = [];

    public function handelsregisterbekanntmachungen() {
        return $this->hasMany(Handelsregisterbekanntmachung::class);
    }

    protected array $stopWords = ['GmbH & Co\. KG', 'AG', 'UG \(haftungsbeschränkt\)', 'UG', 'gGmbH', 'GmbH', 'mbH', 'eG', 'GbR', 'OHG', 'KG', 'e\.K\.', 'eV', 'KGaA', 'VVaG'];
    // source: https://www.unternehmensregister.de/ureg/pdf/D063_Rechtsformen.pdf

    public function articles() {
        return $this->belongsToMany(Article::class, 'article_company', 'company_id', 'article_id');
    }

    public function getSanitizedName() {
        $name = strtolower($this->name);
        foreach($this->stopWords as $word) {
            $name = preg_replace('/ ' . strtolower($word) . '$/', '', $name);
        }
        return $name;
    }

    public function clearNameFromCommonWords(array $listOfCommonWords) : string {
        $nameTokens = explode(' ', $this->getSanitizedName());
        return implode(' ', array_diff($nameTokens, $listOfCommonWords));
    }

    public static function getMostCommonWords(int $minAppearances = 100) : array {
        return Cache::remember('common-words', 500, function() use ($minAppearances) {
            $words = []; // wort -> anzahl
            $companies = Company::all();
            foreach ($companies as $company) {
                $tokens = explode(' ', $company->getSanitizedName());
                foreach($tokens as $token) {
                    array_key_exists($token, $words) ? $words[$token] = $words[$token] + 1 : $words[$token] = 1;
                }
            }
            $words = collect($words)->sortDesc()->filter(function ($value) use ($minAppearances) {
                return $value >= $minAppearances;
            })->keys()->toArray();
            return $words;
        });
    }

    public function getArticles() : ?Collection {
        $cleanedCompanyName = $this->clearNameFromCommonWords(Company::getMostCommonWords());
        $articles = collect(\App\Models\Article::search($cleanedCompanyName)->must(new \JeroenG\Explorer\Domain\Syntax\MultiMatch($cleanedCompanyName, ['title', 'content'], 0))->take(10000)->raw()->hits());
        if(count($articles) >= 1) {
            $minScore = $articles->first()['_score'] / 3;
            $articles = $articles->filter(function ($item, $key) use ($minScore) {
                return $item['_score'] > $minScore;
            })->pluck('_id');
            return Article::findMany($articles);
        }
        return null;
    }

    public function mappableAs(): array
    {
        return [
            'name' => 'text',
            'zip' => 'text',
        ];
    }

}
