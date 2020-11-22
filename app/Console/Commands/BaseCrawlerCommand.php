<?php

namespace App\Console\Commands;

use App\Console\Commands\Exceptions\InvalidSourceException;
use App\Models\Company;
use App\Models\InvalidSource;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseCrawlerCommand extends Command
{
    private const USER_AGENTS = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0',
        'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
        'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)',
        'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
        'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0;  Trident/5.0)',
        'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0; MDDCJS)',
        'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
        'Mozilla/5.0 (Linux; Android 7.0; HTC 10 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.83 Mobile Safari/537.36'
    ];

    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client();
        parent::__construct();
    }

    protected function getCrawler($url)
    {
        if (InvalidSource::where(['url' => $url])->count() !== 0) {
            throw new InvalidSourceException($url);
        }

        $userAgent = self::USER_AGENTS[array_rand(self::USER_AGENTS)];

        $response = Cache::remember($url, 3600 * 24, function () use ($url, $userAgent) {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => $userAgent,
                ],
                'allow_redirects' => false
            ]);

            if ($response->getStatusCode() !== 200) {
                InvalidSource::create(['url' => $url]);
                throw new InvalidSourceException($url);
            }

            try {
                return $response->getBody()->getContents();
            } catch (\Exception $e) {
                throw new InvalidSourceException($url);
            }
        });

        return new Crawler($response);
    }
}
