<?php
/**
 * This file is part of the eZ Publish Legacy package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributd with this source code.
 * @version //autogentag//
 */
namespace EzSystems\EzSummerBeer\ImportBundle\Command;

use EzSystems\EzSummerBeer\ImportBundle\Writers;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BDTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName( 'ezbeer:bdtest');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('ezpublish.api.repository');

        $categoryWriter = new Writers\CategoryWriter($repository);
        // $styleWriter = new StyleWriter($repository, $categoryWriter);

        $reader = new \Ddeboer\DataImport\Reader\ArrayReader(
            array(
                array('name'=>'Category 1', '_remoteId'=>1),
                array('name'=>'Category 2', '_remoteId'=>2),
                array('name'=>'Category 3', '_remoteId'=>3),
                array('name'=>'Category 4', '_remoteId'=>4),
            )
        );
        $workflow = new \Ddeboer\DataImport\Workflow($reader);
        $workflow->addWriter($categoryWriter);

        $workflow->process();
    }
}
