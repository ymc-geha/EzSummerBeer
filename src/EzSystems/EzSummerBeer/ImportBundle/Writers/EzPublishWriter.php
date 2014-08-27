<?php
/**
 * This file is part of the eZ Publish Legacy package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\EzSummerBeer\ImportBundle\Writers;

use Ddeboer\DataImport\Writer\WriterInterface;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;

class EzPublishWriter implements WriterInterface
{
    /** @var string */
    private $contentTypeIdentifier;

    /** @var Repository */
    private $repository;

    private $lastId;

    public function __construct($contentTypeIdentifier, Repository $repository)
    {
        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->repository = $repository;
    }

    public function prepare()
    {
        $this->repository->setCurrentUser( $this->getUserService()->loadUserByLogin('admin') );
    }

    public function writeItem(array $item)
    {
        $createStruct = $this->getCreateStruct();
        $this->mapItemToStruct($item, $createStruct);
        $content = $this->getContentService()->createContent(
            $createStruct,
            array($this->getLocationService()->newLocationCreateStruct($this->getParentLocationId()))
        );
        $this->getContentService()->publishVersion($content->versionInfo);
        $this->lastId = $content->id;
        return $this;
    }

    public function finish()
    {
        // TODO: Implement finish() method.
    }

    protected function getParentLocationId()
    {
        return 2;
    }

    private function mapItemToStruct(array $item, ContentCreateStruct $createStruct)
    {
        foreach($item as $key => $value){
            switch($key)
            {
                case '_remoteId':
                    $createStruct->remoteId = $value;
                    break;
                case '_creationDate':
                    $createStruct->modificationDate = $value;
                    break;
                default:
                    $createStruct->setField($key, $value);
            }
        }
    }

    /**
     * @return ContentCreateStruct
     */
    private function getCreateStruct()
    {
        return $this->getContentService()->newContentCreateStruct(
            $this->getContentTypeService()->loadContentTypeByIdentifier($this->contentTypeIdentifier),
            'eng-GB'
        );
    }

    /**
     * @return \eZ\Publish\API\Repository\ContentTypeService
     */
    protected function getContentTypeService()
    {
        return $this->repository->getContentTypeService();
    }

    /**
     * @return \eZ\Publish\API\Repository\ContentService
     */
    protected function getContentService()
    {
        return $this->repository->getContentService();
    }

    /**
     * @return \eZ\Publish\API\Repository\LocationService
     */
    protected function getLocationService()
    {
        return $this->repository->getLocationService();
    }

    /**
     * @return UserService
     */
    private function getUserService()
    {
        return $this->repository->getUserService();
    }

    /**
     * Returns the ID of the last written element
     * @return mixed
     */
    protected function getLastId()
    {
        return $this->lastId;
    }
}
