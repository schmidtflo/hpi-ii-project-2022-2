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
        $companies = Company::doesntHave('articles')->limit(10000)->get();
        $bar = $this->output->createProgressBar(count($companies));
        $bar->start();
        foreach($companies as $company) {
            $articles = Article::search($company->getSanitizedName())->paginate(20);
            $company->articles()->attach($articles->pluck('id'));
            $bar->advance();
        }
        $bar->finish();

//        $company = Company::find(21340);
//        dump($company->name);
//        dd($company->articles->pluck('title'));
    }
}
