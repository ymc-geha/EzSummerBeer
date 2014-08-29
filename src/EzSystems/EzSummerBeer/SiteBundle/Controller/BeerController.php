<?php

namespace EzSystems\EzSummerBeer\SiteBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

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
}
