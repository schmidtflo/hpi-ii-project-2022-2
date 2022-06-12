<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Handelsregisterbekanntmachung;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportHRB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:hrb';

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
        $this->info('Import HRB now...');

        $file = fopen(storage_path('app/public/corporate-events-dump'), "r");
        $counter = 0;
        $overallCounter = 0;
        $toImport = [];

        while(!feof($file)) {
            $content = json_decode(fgets($file));
            if($content) {
                $toImport[] = [
                    'rb_id' => $content->_source->rb_id,
                    'state' => $content->_source->state,
                    'reference_id' => $content->_source->reference_id,
                    'event_date' => $content->_source->event_date,
                    'event_type' => $content->_source->event_type,
                    'status' => $content->_source->status,
                    'information' => $content->_source->information,
                ];
                $counter++;
                $overallCounter++;

                if($counter >= 1000) {
                    Handelsregisterbekanntmachung::insertOrIgnore($toImport);
                    $counter = 0;
                    $this->info('Line ' . $overallCounter);
                    $toImport = [];
                }
            }

        }
        Handelsregisterbekanntmachung::insertOrIgnore($toImport);

        fclose($file);
    }
}
