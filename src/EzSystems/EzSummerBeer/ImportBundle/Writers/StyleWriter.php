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
    /** @var CategoryWriter */
    private $categoryWriter;

    private $categoryLocationId;

    public function __construct(Repository $repository, CategoryWriter $categoryWriter)
    {
        parent::__construct('beer_style', $repository);
        $this->categoryWriter = $categoryWriter;
    }

    public function writeItem( array $item )
    {
        if (isset($item['category'])) {
            if (!$categoryLocationId = $this->getCategoryLocationId($item['category']['_remoteId'])) {
                $this->categoryWriter->writeItem($item['category']);
                $categoryLocationId = $this->getCategoryLocationId($item['category']['_remoteId']);
            }
            $this->categoryLocationId = $categoryLocationId;
        }
        parent::writeItem( $item['style'] );
    }

    private function getCategoryLocationId($remoteId)
    {
        try {
            return $this->getContentService()->loadContentByRemoteId( $remoteId )->contentInfo->mainLocationId;
        } catch (\eZ\Publish\API\Repository\Exceptions\NotFoundException $e) {
            return false;
        }
    }

    protected function getParentLocationId()
    {
        return $this->categoryLocationId;
    }


}
