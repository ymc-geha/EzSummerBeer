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
        if (!$item['_styleId']) {
            throw new ImportException( "Beer has no style, booooo !" );
        }
        $this->setParentLocationId($item);
        $this->setGlass($item);
        $this->setImage($item);
        parent::writeItem($item);
        if (isset($item['label'])) {
            unlink($item['label']);
        }
        return $this;
    }

    protected function getParentLocationId()
    {
        return $this->parentLocationId;
    }

    protected function setParentLocationId(array &$item)
    {
        $styleContent = $this->getContentService()->loadContentByRemoteId($item['_styleId']);
        $this->parentLocationId = $styleContent->contentInfo->mainLocationId;
        unset($item['_styleId']);
    }

    /**
     * Sets the glass field to the glass content id
     * @param array $item
     */
    protected function setGlass(array &$item)
    {
        $item['glass'] = $this->getContentService()->loadContentByRemoteId($item['_glassId'])->id;
    }

    /**
     * Downloads the label image locally, and sets the field to this file's path
     * @param array $item
     */
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
