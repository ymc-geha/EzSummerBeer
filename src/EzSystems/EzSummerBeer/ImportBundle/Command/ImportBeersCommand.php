<?php
/**
 * This file is part of the eZ Publish Legacy package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributd with this source code.
 * @version //autogentag//
 */
namespace EzSystems\EzSummerBeer\ImportBundle\Command;

use Ddeboer\DataImport\Filter\OffsetFilter;
use EzSystems\EzSummerBeer\ImportBundle\Filter\ExistingContentFilter;
use EzSystems\EzSummerBeer\ImportBundle\Writers;
use EzSystems\EzSummerBeer\ImportBundle\Reader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportBeersCommand extends ImportCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('ezbeer:import:beers');
        $this->addOption('offset', null, InputOption::VALUE_REQUIRED, 'Offset number to start from.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getRepository();
        $beerWriter = new Writers\BeerWriter($this->getRootLocationId(), $repository);
        $reader = new Reader\BeersReader($input->getArgument('source'), (int)$input->getOption('offset'));
        $workflow = new \Ddeboer\DataImport\Workflow($reader);
        $workflow
            ->addWriter(new \Ddeboer\DataImport\Writer\ConsoleProgressWriter($output, $reader))
            ->addWriter($beerWriter)
            ->addFilter(new ExistingContentFilter($repository->getContentService()))
            ->setSkipItemOnFailure(true)
            ->process();
    }
}
