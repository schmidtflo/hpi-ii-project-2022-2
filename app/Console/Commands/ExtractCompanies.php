<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Handelsregisterbekanntmachung;
use Illuminate\Console\Command;

class ExtractCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected array $stopWords = ['AG', 'UG', 'UG (haftungsbeschränkt)', 'GmbH', 'mbH', 'eG', 'GbR', 'OHG', 'KG', 'e.K.', 'GmbH & Co. KG', 'eV', 'KGaA', 'VVaG'];
    // source: https://www.unternehmensregister.de/ureg/pdf/D063_Rechtsformen.pdf


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hrbs = Handelsregisterbekanntmachung::where('company_id', null)->limit(100000)->get();

        $bar = $this->output->createProgressBar(count($hrbs));
        $bar->start();

        foreach ($hrbs as $hrb) {
            try {

                $name = preg_replace('/^\w{2,4} \d+( B)?:/', '', $hrb->information);
                $name = explode(',', $name);
                $name = trim($name[0]);

                $zip = null;
                preg_match_all('/, (\d{5}) /', $hrb->information, $zip);
                $zip = $zip[1][0];

                $company = Company::firstOrCreate([
                    'name' => $name,
                    'zip' => $zip,
                ]);

                $hrb->company_id = $company->id;
                $hrb->save();
            } catch (\Exception $exception)
            {

            }

            $bar->advance();
        }
        $bar->finish();
    }
}
