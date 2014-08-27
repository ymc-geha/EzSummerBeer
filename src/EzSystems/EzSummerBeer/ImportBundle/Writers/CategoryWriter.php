<?php
/**
 * This file is part of the eZ Publish Legacy package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributd with this source code.
 * @version //autogentag//
 */
namespace EzSystems\EzSummerBeer\ImportBundle\Writers;

use eZ\Publish\API\Repository\Repository;

class CategoryWriter extends EzPublishWriter
{
    /**
     * @var array remote id => id
     */
    private $knownCategories;

    /** @var mixed*/
    private $rootLocationId;

    public function __construct($rootLocationId, Repository $repository)
    {
        parent::__construct('beer_category', $repository);
        $this->rootLocationId = $rootLocationId;
    }

    protected function getParentLocationId()
    {
        return $this->rootLocationId;
    }


}
