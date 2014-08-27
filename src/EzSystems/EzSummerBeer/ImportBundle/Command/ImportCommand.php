<?php
/**
 * This file is part of the eZ Publish Legacy package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributd with this source code.
 * @version //autogentag//
 */
namespace EzSystems\EzSummerBeer\ImportBundle\Command;

use eZ\Publish\API\Repository\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

abstract class ImportCommand extends Command
{
    /** @var mixed */
    private $rootLocationId;

    /** @var Repository */
    private $repository;

    public function __construct(Repository $repository, $rootLocationId)
    {
        $this->rootLocationId = $rootLocationId;
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'Source JSON file');
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    protected function getRootLocationId()
    {
        return $this->rootLocationId;
    }
}
