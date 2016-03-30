<?php

namespace Respect\Structural\Driver\Mongo;

use Respect\Data\CollectionIterator;
use Respect\Data\Collections\Collection;

class Mapping
{

    /**
     * @param Collection $collection
     *
     * @return array
     */
    public function generateQuery(Collection $collection)
    {
        return $this->parseConditions($collection);
    }

    /**
     * @param Collection $collection
     *
     * @return array
     */
    protected function parseConditions(Collection $collection)
    {
        $allCollections = CollectionIterator::recursive($collection);
        $allCollections = iterator_to_array($allCollections);
        $allCollections = array_slice($allCollections, 1);

        $condition = $this->getConditionArray($collection);

        foreach ($allCollections as $coll) {
            $condition += $this->getConditionArray($coll, true);
        }

        return $condition;
    }

    /**
     * @param Collection $collection
     * @param bool|false $prefix
     *
     * @return array
     */
    protected function getConditionArray(Collection $collection, $prefix = false)
    {
        $condition = $collection->getCondition();

        if (!is_array($condition)) {
            $condition = ['_id' => $this->createObjectId($condition)];
        }

        if ($prefix) {
            $condition = static::prefixArrayKeys($condition, $collection->getName() . '.');
        }

        return $condition;
    }

    /**
     * @param array $array
     * @param string $prefix
     *
     * @return array
     */
    protected static function prefixArrayKeys(array $array, $prefix)
    {
        return array_combine(
            array_map(
                function ($key) use ($prefix) {
                    return "{$prefix}{$key}";
                }, array_keys($array)),
            $array
        );
    }

    /**
     * @param int|string $id
     *
     * @return \MongoId|\MongoInt32
     */
    public function createObjectId($id)
    {
        if (is_int($id)) {
            return new \MongoInt32($id);
        }

        return new \MongoId($id);
    }
}
