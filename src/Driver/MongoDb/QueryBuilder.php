<?php

namespace Respect\Structural\Driver\MongoDb;

use MongoDB\BSON\ObjectID;
use Respect\Data\CollectionIterator;
use Respect\Data\Collections\Collection;
use Respect\Structural\QueryBuilder as BaseQueryBuilder;

class QueryBuilder implements BaseQueryBuilder
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * QueryBuilder constructor.
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function assemble()
    {
        return $this->parseConditions($this->collection);
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
            $condition = ['_id' => new ObjectID($condition)];
        }

        if ($prefix) {
            $condition = static::prefixArrayKeys($condition, $collection->getName() . '.');
        }

        return $condition;
    }

    /**
     * Add prefix in all array keys.
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
                },
                array_keys($array)
            ),
            $array
        );
    }

    /**
     * @param int|string $id
     *
     * @return \MongoId|\MongoInt32
     */
    protected function createMongoId($id)
    {
        if (is_int($id)) {
            return new \MongoInt32($id);
        }

        return new \MongoId($id);
    }
}
