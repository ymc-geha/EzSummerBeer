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

class BeerWriter extends EzPublishWriter
{
    /** @var mixed*/
    private $parentLocationId;

    public function __construct($rootLocationId, Repository $repository)
    {
        parent::__construct('beer', $repository);
    }

    public function writeItem(array $item)
    {
        $this->setParentLocationId($item);
        $this->setGlass($item);
        $this->setImage($item);
        parent::writeItem($item);
        if (isset($item['label'])) {
            unlink($item['label']);
        }
        return $this;
    }


    protected function setParentLocationId(array &$item)
    {
        $styleContent = $this->getContentService()->loadContentByRemoteId('style-'.$item['_styleId']);
        $this->parentLocationId = $styleContent->contentInfo->mainLocationId;
        unset($item['_styleId']);
    }

    protected function setGlass(array &$item)
    {
        $item['glass'] = $this->getContentService()->loadContentByRemoteId('glass-'.$item['glass']['id'])->id;
    }

    protected function setImage(array &$item)
    {
        if (!isset($item['label'])) {
            return;
        }


        $tmpName = tempnam(sys_get_temp_dir(), 'beerlabel').'.'.pathinfo($item['label'], PATHINFO_EXTENSION);
        copy($item['label'], $tmpName);
        $item['label'] = $tmpName;
    }
}
