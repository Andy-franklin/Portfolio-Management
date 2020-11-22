<?php

namespace App\Console\Commands;

use App\Console\Commands\Exceptions\InvalidSourceException;
use App\Http\Helper\ExchangeNameHelper;
use App\Models\Company;
use App\Models\CompanyProfile;
use App\Models\Industry;
use App\Models\Sector;
use Symfony\Component\DomCrawler\Crawler;

class CompanyProfiles extends BaseCrawlerCommand
{
    protected $signature = 'company-profiles:fetch';

    protected $description = 'get company profile information from multiple data sources';

    protected $sources = [
        'yahoo' => [
            'urlStructures' => [
                'https://uk.finance.yahoo.com/quote/{ticker}.{fix42}/profile',
                'https://uk.finance.yahoo.com/quote/{ric}/profile',
            ],
            'name' => 'Yahoo Finance',
        ],
//        'cnbc' => [
//            'urlStructures' => [
//                'https://www.cnbc.com/quotes/?symbol={ticker}',
//            ],
//            'name' => 'CNBC',
//        ],
//        'globe' => [
//            'urlStructures' => [
//                'https://www.theglobeandmail.com/investing/markets/stocks/{ticker}/',
//            ],
//            'name' => 'The Globe and Mail',
//        ],

//    https://www.stockwatch.com (free 30 day trial)
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = Company::all();
        $this->line('Fetching company profiles from sources');
        $this->output->progressStart($companies->count());
        foreach ($companies as $company) {
            $companyProfiles = $this->getFromValidProfileSources($company);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();


        return 0;
    }

    private function getFromValidProfileSources(Company $company)
    {
        $companyProfiles = [];
        foreach ($this->sources as $sourceName => $source) {
            foreach ($source['urlStructures'] as $urlStructure) {
                $placeholders = $this->extractPlaceholders($urlStructure);
                $values = [];
                foreach ($placeholders as $searchString => $placeholder) {
                    $value = $this->mapPlaceholder($placeholder, $company);
                    if (null !== $value) {
                        $values[$searchString] = $value;
                    }
                }

                if (\count($values) === \count($placeholders)) {
                    try {
                        $companyProfiles[] = $this->getCompanyProfile($sourceName, $urlStructure, $company, $values);
                    } catch (InvalidSourceException $invalidSourceException) {
                    }
                }
            }
        }

        return $companyProfiles;
    }

    private function getCompanyProfile(string $source, string $urlStructure, Company $company, array $placeholders)
    {
        $url = $urlStructure;
        foreach ($placeholders as $placeholder => $placeholderValue) {
            $url = str_replace($placeholder, $placeholderValue, $url);
        }

        $crawler = $this->getCrawler($url);

        switch ($source) {
            case 'yahoo':
                $name = $crawler->filter('h1')->text();

                $profileContainer = $crawler->filter('.asset-profile-container');
                if ($profileContainer->count() > 0) {
                    //If we can have the nicer name lets get that.
                    $name = $profileContainer->filter('h3')->text();
                    $sectorsContainer = $profileContainer->filter('p')->getNode(1);
                    $sectorsHtml = '';
                    foreach ($sectorsContainer->childNodes as $child) {
                        if ($child->hasAttributes()) {
                            foreach ($child->attributes as $key => $value) {
                                $child->removeAttribute($key);
                            }
                        }
                        $sectorsHtml .= $child->ownerDocument->saveXML($child);
                    }

                    [$sectorsString, $industryString, $employeesString] = explode('<br/>', strip_tags($sectorsHtml, '<br>'));

                    //Explode on character encoded NBSP as ltrim didn't work with it.
                    $sectorsString = explode(chr(0xC2) . chr(0xA0), $sectorsString)[1];
                    $industryString = explode(chr(0xC2) . chr(0xA0), $industryString)[1];
                    $employeesString = explode(chr(0xC2) . chr(0xA0), $employeesString)[1];
                    $employeesString = str_replace(',', '', $employeesString);

                    $sector = Sector::query()->firstOrCreate([
                        'name' => $sectorsString,
                        'primary_choice' => $source === 'yahoo',
                        'source' => $source,
                    ]);

                    $industry = Industry::query()->firstOrCreate([
                        'name' => $industryString,
                        'primary_choice' => $source === 'yahoo',
                        'source' => $source,
                    ]);

                    $descriptionContainer = $crawler->filter('.quote-sub-section');
                    if ($descriptionContainer->count() > 0) {
                        $description = $descriptionContainer->filter('p')->text();
                    }
                }

                /** @var CompanyProfile $companyProfile */
                $companyProfile = CompanyProfile::query()->firstOrCreate([
                    'company_id' => $company->id,
                    'source' => $source,
                ]);

                if (isset($employeesString) && $employeesString === '') {
                    $employeesString = null;
                }

                if ($name === 'Yahoo') {
                    //Invalid
                    return;
                }

                $companyProfile->update([
                    'name' => $name,
                    'description' => $description ?? null,
                    'employees' => $employeesString ?? null,
                ]);

                if (isset($sector)) {
                    $companyProfile->sectors()->attach($sector);
                }

                if (isset($industry)) {
                    $companyProfile->industries()->attach($industry);
                }

                break;
            case 'cnbc':
            case 'globe':
        }
    }

    private function extractPlaceholders($string)
    {
        $return = [];
        preg_match_all('/{(.*?)}/', $string, $matches);
        foreach ($matches[1] as &$match) {
            if (strpos($match, '{') !== 0) {
                $return ['{' . $match . '}'] = $match;
            }
        }

        return $return;
    }

    private function mapPlaceholder($placeholder, Company $company)
    {
        switch ($placeholder) {
            case 'fix42':
                if (($exchange = $company->exchange()->first()) !== null) {
                    return ExchangeNameHelper::MIC_TO_OLD_FIX_42_VALUE[$exchange->mic] ?? null;
                }
                return null;
            case 'ticker':
                return $company->ticker;
            case 'ric':
                return $company->ric;
        }

        return null;
    }
}
