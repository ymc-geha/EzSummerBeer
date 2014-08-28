<?php
/**
 * File containing the ExistingContentFilter class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\EzSummerBeer\ImportBundle\Filter;


use Ddeboer\DataImport\Filter\FilterInterface;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

class ExistingContentFilter implements FilterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Filter input
     *
     * @param array $item Input
     *
     * @return boolean If false is returned, the workflow will skip the input
     */
    public function filter(array $item)
    {
        if (isset($item['_remoteId'])) {
            return true;
        }

        try {
            $this->contentService->loadContentInfoByRemoteId($item['_remoteId']);
        } catch (NotFoundException $e) {
            return true;
        }

        return false;
    }

    public function getPriority()
    {
        return 0;
    }
}
 