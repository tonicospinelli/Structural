<?php

namespace Respect\Structural\Driver\MongoDb;

use MongoDB\BSON\ObjectID;
use MongoDB\Client as MongoDBClient;
use MongoDB\Database;
use Respect\Data\CollectionIterator;
use Respect\Data\Collections\Collection;
use Respect\Structural\Driver as BaseDriver;

class Driver implements BaseDriver
{
    /**
     * @var MongoDBClient
     */
    private $connection;

    /**
     * @var Database
     */
    private $database;

    /**
     * Driver constructor.
     *
     * @param MongoDBClient $connection
     * @param string        $database
     */
    public function __construct(MongoDBClient $connection, $database)
    {
        $this->connection = $connection;
        $this->database = $connection->selectDatabase($database);
    }

    /**
     * @return Database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @return MongoDBClient
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(\Iterator $cursor)
    {
        $data = null;
        if ($cursor->valid()) {
            $data = $cursor->current();
            $cursor->next();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function find($collection, array $query = [])
    {
        $cursor = $this->getDatabase()->selectCollection($collection)->find($query);
        $iterator = new \IteratorIterator($cursor);
        $iterator->rewind();

        return $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function generateQuery(Collection $collection)
    {
        return new QueryBuilder($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function insert($collection, $document)
    {
        $result = $this->getDatabase()->selectCollection($collection)->insertOne($document);
        $document->_id = $result->getInsertedId();
    }

    /**
     * {@inheritdoc}
     */
    public function update($collection, $criteria, $document)
    {
        $this->getDatabase()->selectCollection($collection)->updateOne($criteria, ['$set' => $document]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($collection, $criteria)
    {
        $this->getDatabase()->selectCollection($collection)->deleteOne($criteria);
    }
}
