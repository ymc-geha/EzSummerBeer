<?php

namespace EzSystems\EzSummerBeer\ImportBundle\Reader;

use ArrayIterator;
use DateTime;
use Ddeboer\DataImport\Reader\ReaderInterface;

/**
 * Import reader for beer styles and categories.
 */
class StylesReader extends ArrayIterator implements ReaderInterface
{
    public function __construct($file)
    {
        parent::__construct($this->loadData($file));
    }

    protected function loadData($file)
    {
        $data = [];
        foreach (json_decode(file_get_contents($file), true)['data'] as $item) {
            $styleData = [
                '_remoteId' => 'style-' . $item['id'],
                '_creationDate' => new DateTime($item['createDate']),
                'name' => $item['name'],
                'description' => isset($item['description']) ? $item['description'] : null,
                'ibu_min' => isset($item['ibuMin']) ? $item['ibuMin'] : null,
                'ibu_max' => isset($item['ibuMax']) ? $item['ibuMax'] : null,
                'abv_min' => isset($item['abvMin']) ? $item['abvMin'] : null,
                'abv_max' => isset($item['abvMax']) ? $item['abvMax'] : null,

            ];

            $categoryData = [];
            if (isset($item['category'])) {
                $categoryData['_remoteId'] = 'category-' . $item['category']['id'];
                $categoryData['_creationDate'] = new DateTime($item['category']['createDate']);
                $categoryData['name'] = $item['category']['name'];
            }

            $data[] = ['style' => $styleData, 'category' => $categoryData];
        }

        return $data;
    }

    public function getFields()
    {
        return [];
    }
}
