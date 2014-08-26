<?php

namespace EzSystems\EzSummerBeer\ImportBundle\Commands;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Console command to download data to import using BreweryDB API.
 */
class DataDownloadCommand extends Command
{
    /**
     * Directory where data will be stored.
     *
     * @var string
     */
    private $dataFilePaths;

    /**
     * BreweryDB API key.
     *
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiUrl;

    public function __construct(array $dataFilePaths, $apiUrl, $apiKey)
    {
        $this->dataFilePaths = $dataFilePaths;
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ezbeer:data:download')
            ->setDescription('Download beer data to import using BreweryDB API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $httpClient = new Client();

        $output->writeln('<info>Now downloading glassware data</info>');
        $glasswareResponse = $httpClient->get("$this->apiUrl/glassware?key=$this->apiKey");
        $fs->dumpFile($this->dataFilePaths['glassware'], $glasswareResponse->getBody());

        $output->writeln('<info>Now downloading beer styles data</info>');
        $fs->dumpFile(
           $this->dataFilePaths['styles'],
           $httpClient->get("$this->apiUrl/styles?key=$this->apiKey")->getBody()
        );

        $output->writeln('<info>Now downloading beers data</info>');
        $beerData = [];
        foreach ($glasswareResponse->json()['data'] as $item) {
            $response = $httpClient->get(
                "$this->apiUrl/beers",
                ['query' => ['key' => $this->apiKey, 'glasswareId' => $item['id']]]
            );
            $beerData = array_merge($beerData, $response->json()['data']);
        }
        $fs->dumpFile($this->dataFilePaths['beers'], json_encode($beerData));
    }
}
 