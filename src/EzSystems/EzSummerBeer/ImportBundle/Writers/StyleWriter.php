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

class StyleWriter extends EzPublishWriter
{
    public function __construct(Repository $repository, CategoryWriter $categoryWriter)
    {
        parent::__construct('beer_style', $repository);
    }

    protected function buildRemoteId( $arg )
    {
        return "beerstyle-$arg";
    }
}
