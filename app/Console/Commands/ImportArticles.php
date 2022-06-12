<?php

namespace App\Console\Commands;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:articles';

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
        $this->info('Import Articles now...');

        $articles = File::get(storage_path('app/public/articles.json'));

        $data = json_decode($articles, true);

        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        Article::withoutSyncingToSearch(function () use ($data, $bar) {
            $counter = 0;
            $toImport = [];

            foreach ($data as $article) {
                $toImport[] = array(
                    'title' => $article['title'],
                    'content' => $article['content'],
                    'url' => $article['url'],
                    'source' => $article['source'],
                    'date' => Carbon::parse($article['date']),
                );
                $counter++;

                if($counter >= 1000) {
                    Article::insertOrIgnore($toImport);
                    $counter = 0;
                    $toImport = [];
                }
                $bar->advance();
            }
            Article::insertOrIgnore($toImport);
        });

        $bar->finish();
    }
}
