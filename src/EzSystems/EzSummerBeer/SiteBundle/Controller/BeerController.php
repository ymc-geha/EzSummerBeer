<?php

namespace EzSystems\EzSummerBeer\SiteBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Relation;

class BeerController extends Controller
{
    public function viewHomeAction($locationId, $viewType, $params = [], $layout = false)
    {
        $criterion = new Criterion\LogicalAnd([
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            new Criterion\ContentTypeIdentifier('beer_selection')
        ]);
        $lastSelection = $this->getRepository()->getSearchService()->findSingle($criterion);

        return $this->get('ez_content')->viewLocation(
            $locationId, $viewType, $layout,
            ['last_selection' => $lastSelection] + $params
        );
    }

    public function viewBeerAction($locationId, $viewType, $params = [], $layout = false)
    {
        // Get the beer review, defined as reverse relation, if any.
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        $location = $locationService->loadLocation($locationId);
        $reverseRelations = $contentService->loadReverseRelations($location->getContentInfo());

        $reviewContentInfo = null;
        foreach ($reverseRelations as $relation) {
            if ($relation->type === Relation::FIELD && $relation->sourceFieldDefinitionIdentifier === 'beer') {
                $reviewContentInfo = $relation->getSourceContentInfo();
                break;
            }
        }

        // Get the glass type
        $glassContentInfo = null;
        $content = $contentService->loadContentByContentInfo($location->getContentInfo());
        /** @var \eZ\Publish\Core\FieldType\RelationList\Value $glassValue */
        $glassValue = $this->get('ezpublish.translation_helper')->getTranslatedField($content, 'glass')->value;
        if ( $glassValue->destinationContentIds ) {
            $glassContentInfo = $contentService->loadContentInfo($glassValue->destinationContentIds[0]);
        }

        return $this->get('ez_content')->viewLocation(
            $locationId, $viewType, $layout, [
                'reviewContentInfo' => $reviewContentInfo,
                'glassContentInfo' => $glassContentInfo
            ] + $params
        );
    }
}
