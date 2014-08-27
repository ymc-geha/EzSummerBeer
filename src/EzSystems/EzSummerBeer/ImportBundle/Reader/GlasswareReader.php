<?php

namespace EzSystems\EzSummerBeer\ImportBundle\Reader;

use Ddeboer\DataImport\Reader\ReaderInterface;
use ArrayIterator;
use DateTime;

/**
 * Import reader for beer glassware.
 */
class GlasswareReader extends ArrayIterator implements ReaderInterface
{
    public function __construct($file)
    {
        parent::__construct($this->loadData($file));
    }

    protected function loadData($file)
    {
        $data = [];
        foreach (json_decode(file_get_contents($file), true)['data'] as $item) {
            $data[] = [
                '_remoteId' => 'glass-' . $item['id'],
                '_creationDate' => new DateTime($item['createDate']),
                'name' => $item['name'],
            ];
        }

        return $data;
    }

    public function getFields()
    {
        return ['_remoteId', '_creationDate', 'name'];
    }
}
