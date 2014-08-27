<?php

namespace EzSystems\EzSummerBeer\ImportBundle\Reader;

use ArrayIterator;
use DateTime;
use Ddeboer\DataImport\Reader\ReaderInterface;

class BeersReader extends ArrayIterator implements ReaderInterface
{
    public function __construct($file)
    {
        parent::__construct($this->loadData($file));
    }

    protected function loadData($file)
    {
        $data = [];
        foreach (json_decode(file_get_contents($file), true) as $item) {
            if (isset($item['styleId'])) {
                $styleId = $item['styleId'];
            } elseif (isset($item['style']['id'])) {
                $styleId = $item['style']['id'];
            } else {
                $styleId = null;
            }

            $data[] = [
                '_remoteId' => 'beer-' . $item['id'],
                '_creationDate' => new DateTime($item['createDate']),
                '_styleId' => $styleId,
                'name' => $item['name'],
                'description' => isset($item['description']) ? $item['description'] : null,
                'abv' => isset($item['abv']) ? $item['abv'] : null,
                'ibu' => isset($item['ibu']) ? $item['ibu'] : null,
                'glass' => isset($item['glass']) ? $item['glass'] : null,
                'is_organic' => isset($item['isOrganic']) && $item['isOrganic'] === 'Y' ? true : false,
                'label' => isset($item['labels']['large']) ? $item['labels']['large'] : null,
                'serving_temperature' => isset($item['serving_temperature']) ? $item['serving_temperature'] : null,
                'variation_from' => isset($item['variation_from']) ? $item['variation_from'] : null,
            ];
        }

        return $data;
    }

    public function getFields()
    {
        return [
            '_remoteId',
            '_creationDate',
            '_styleId',
            'name',
            'description',
            'abv',
            'ibu',
            'glass',
            'is_organic',
            'label',
            'serving_temperature',
            'variation_from'
        ];
    }
}
 