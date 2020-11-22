<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Exchange;

class ExchangeDetails extends BaseCrawlerCommand
{
    protected $signature = 'exchange:fetch';

    protected $description = 'get exchange information from iotafinance.com';

    private const BASEURL = 'https://www.iotafinance.com/en/Detail-view-MIC-code-';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mics = Company::query()
            ->select('exchange')
            ->whereNotNull('exchange')
            ->distinct()
            ->get()
            ->pluck('exchange');

        $existingExchanges = Exchange::query()->whereIn('mic', $mics)->pluck('mic');

        foreach ($mics as $mic) {
            if (! $existingExchanges->contains($mic)) {
                $crawler = $this->getCrawler(self::BASEURL . $mic . '.html');

                $columns = $crawler->filter('section')->first()->filter('.columns');
                $dataCols = [];
                foreach ($columns as $key => $column) {
                    if ($key % 2 === 0) {
                        $columnName = str_replace(["\n", "\r", "\t", ':'], '', $column->nodeValue);
                    } else {
                        $dataCols[$columnName] = str_replace(["\n", "\r", "\t"], '', $column->nodeValue);
                        unset($columnName);
                    }
                }

                if (isset($dataCols['MIC'])) {
                    $exchange = new Exchange([
                        'mic' => $dataCols['MIC'],
                        'name' => $dataCols['Market'],
                        'operating_mic' => $dataCols['Operating MIC'],
                        'acronym' => $dataCols['Acronym'],
                        'creation_date' => $dataCols['Creation date'],
                        'city' => $dataCols['City'],
                        'country' => $dataCols['Country'],
                        'status' => $dataCols['Status'],
                        'comment' => $dataCols['Comment'],
                        'website' => $dataCols['Web site'],
                    ]);

                    $exchange->save();

                    Company::query()->where('exchange', $exchange->mic)->update(['exchange_id' => $exchange->id]);
                }
            }
        }

        return 0;
    }
}
