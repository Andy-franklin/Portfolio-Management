<?php

namespace App\Console\Commands;

use App\Http\Helper\ExchangeNameHelper;
use App\Models\Company;

class IsinToExchangeAndTicker extends BaseCrawlerCommand
{
    protected $signature = 'isin:fetch';

    protected $description = 'convert isin to exchange and ticker from morningstar.com';

    protected const MORNING_STAR_QUERY_URL = 'https://www.morningstar.com/search?query=';

    protected const REUTERS_URL = 'https://www.reuters.com/companies/'; // SHOP.K

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = Company::query()->whereNull('ticker')->whereNotNull('isin')->whereNull('ric')->get();
        $this->line('Getting from Morning Star using ISIN');
        $this->output->progressStart($companies->count());
        foreach ($companies as $company) {
            try {
                $crawler = $this->getCrawler(self::MORNING_STAR_QUERY_URL . $company->isin);
                $this->output->progressAdvance();

                $companyTickerAndExchange = $crawler->filter('.mdc-security-module__metadata')->first();
                $exchange = $companyTickerAndExchange->filter('.mdc-security-module__exchange')->first()->text();
                $ticker = $companyTickerAndExchange->filter('.mdc-security-module__ticker')->first()->text();

                $company->exchange = $exchange;
                $company->ticker = $ticker;

                $company->save();
            } catch (\Exception $e) {}
        }
        $this->output->progressFinish();

        $companies = Company::query()->whereNull('ticker')->whereNotNull('ric')->whereNull('isin')->get();
        $this->line('Getting from Reuters using RIC');
        $this->output->progressStart($companies->count());

        return null;
    }
}
