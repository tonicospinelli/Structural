<?php

namespace Respect\Structural\Driver\Mongo;

use Respect\Data\CollectionIterator;
use Respect\Data\Collections\Collection;
use Respect\Structural\Driver as BaseDriver;

class Driver implements BaseDriver
{
    /**
     * @var \MongoClient
     */
    private $connection;

    private $database;

    /**
     * Driver constructor.
     * @param \MongoClient $connection
     */
    public function __construct(\MongoClient $connection, $database)
    {
        $this->connection = $connection;
        $this->database = $connection->{$database};
    }

    /**
     * @return \MongoDB
     */
    public function getDatabase()
    {
        return $this->database;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param \Iterator $cursor
     * @return array
     */
    public function fetch(\Iterator $cursor)
    {
        $cursor->next();
        return $cursor->current();
    }

    /**
     * @param array $collection
     * @param array $query
     * @return \Iterator
     */
    public function find($collection, array $query = array())
    {
        return $this->getDatabase()->{$collection}->find($query);
    }

    public function generateQuery(Collection $collection)
    {
        return $this->parseConditions($collection);
    }

    protected function parseConditions(Collection $collection)
    {
        $allCollections = CollectionIterator::recursive($collection);
        $allCollections = iterator_to_array($allCollections);
        $allCollections = array_slice($allCollections, 1);

        $condition = $this->getConditionArray($collection);

        foreach ($allCollections as $name => $coll)
            $condition += $this->getConditionArray($coll, true);

        return $condition;
    }

    protected function getConditionArray(Collection $collection, $prefix = false)
    {
        $condition = $collection->getCondition();

        if (!is_array($condition)) {
            $condition = array('_id' => new \MongoId($condition));
        }

        if ($prefix)
            $condition = static::prefixArrayKeys($condition, $collection->getName() . ".");

        return $condition;
    }

    protected static function prefixArrayKeys(array $array, $prefix)
    {
        $new = array();

        foreach ($array as $key => $value)
            $new["{$prefix}{$key}"] = $value;

        return $new;
    }

    public function insert($collection, $document)
    {
        $this->getDatabase()->{$collection}->insert($document);
    }

    public function update($collection, $criteria, $document)
    {
        $this->getDatabase()->{$collection}->update($criteria, $document);
    }

    public function remove($collection, $criteria)
    {
        $this->getDatabase()->{$collection}->remove($criteria);
    }
}
