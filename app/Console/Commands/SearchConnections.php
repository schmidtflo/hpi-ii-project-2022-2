<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Article;
use Illuminate\Console\Command;

class SearchConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Calculating Most Common Words in Company Names');
        $commonWords = Company::getMostCommonWords();
        $this->info('Found ' . count($commonWords) . ' Common Words in Company Names');

        $this->info('Retrieving Companies');
        $companies = Company::doesntHave('articles')->limit(100000)->get();
        $this->info('Work through all companies to find matching articles now...');
//        $bar = $this->output->createProgressBar(count($companies));
//        $bar->start();
        foreach($companies as $company) {
            $cleanedCompanyName = $company->clearNameFromCommonWords($commonWords);
            $articles = collect(\App\Models\Article::search($cleanedCompanyName)->must(new \JeroenG\Explorer\Domain\Syntax\MultiMatch($cleanedCompanyName, ['title', 'content'], 0))->take(10000)->raw()->hits());
            if(count($articles) >= 1) {
                $minScore = $articles->first()['_score'] / 3;
                $articles = $articles->filter(function ($item, $key) use ($minScore) {
                    return $item['_score'] > $minScore;
                })->pluck('_id');
                $company->articles()->sync($articles);

                $this->info('For Company ' . $company->name . ' (sanitized: ' . $cleanedCompanyName . ' ): Found ' . count($articles) . ' Articles, highest Score: ' . $minScore*3);

            }

//            $bar->advance();
        }
//        $bar->finish();
    }
}
