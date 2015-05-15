<?php

namespace EzSystems\EzSummerBeer\SiteBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Relation;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * Beer style view.
     * We'll list beers for requested style.
     *
     * @param Request $request
     * @param int $locationId
     * @param string $viewType
     * @param array $params
     * @param bool $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewBeerStyleAction(Request $request, $locationId, $viewType, $params = [], $layout = false)
    {
        // First build the location search query.
        // We're looking for beers right under the current location.
        $query = new LocationQuery();
        $query->criterion = new Criterion\LogicalAnd([
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            new Criterion\ContentTypeIdentifier('beer'),
            new Criterion\ParentLocationId($locationId)
        ]);
        $query->sortClauses = [new SortClause\ContentName()];

        // Now initialize the pager, using LocationSearchAdapter
        $beerPager = new Pagerfanta(new LocationSearchAdapter($query, $this->getRepository()->getSearchService()));
        $beerPager->setCurrentPage($request->get('page', 1));
        $beerPager->setMaxPerPage(5);

        return $this->get('ez_content')->viewLocation(
            $locationId, $viewType, $layout, [
                'beers' => $beerPager
            ] + $params
        );
    }
}
