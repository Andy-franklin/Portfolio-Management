<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class ParseCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:companies {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse storage/data/companies-{date}.json into the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws
     */
    public function handle(): ?int
    {
        $fileContent = file_get_contents('storage/data/companies-' . $this->argument('date') . '.json');

        $data = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);

        $newCount = 0;
        $existingCount = 0;
        foreach ($data as $company) {
            $search = array_merge([
                'ric' => null,
                'isin' => null
            ], $company);
            $existingCompany = Company::where([
                'ric' => $search['ric'],
                'isin' => $search['isin'],
                'ticker_212' => $search['ticker']
            ])->first();
            if (null === $existingCompany) {
                $company['ticker_212'] = $company['ticker'];
                unset($company['ticker']);
                (new Company($company))->save();
                $newCount++;
            } else {
                $existingCount++;
            }
        }

        $this->line('Added ' . $newCount . ' companies.');
        $this->line('Skipped ' . $existingCount . ' companies.');

        return null;
    }
}
