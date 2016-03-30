<?php

namespace Respect\Structural\Driver\MongoDb;

use MongoDB\BSON\ObjectID;
use MongoDB\Client;
use MongoDB\Database;
use Respect\Data\Collections\Collection;
use Respect\Data\Styles\Stylable;
use Respect\Structural\Driver as BaseDriver;

class Driver implements BaseDriver
{
    /**
     * @var Stylable
     */
    protected $style;

    /**
     * @var Client
     */
    private $connection;

    /**
     * @var Database
     */
    private $database;

    /**
     * @var Mapping
     */
    private $mapping;

    /**
     * Driver constructor.
     *
     * @param Client $connection
     * @param string $database
     */
    public function __construct(Client $connection, $database)
    {
        $this->connection = $connection;
        $this->database = $connection->selectDatabase($database);
        $this->mapping = new Mapping();
    }

    /**
     * @return Mapping
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param Mapping $mapping
     * @return Driver
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStyle()
    {
        if (is_null($this->style)) {
            $this->style = new Style();
        }

        return $this->style;
    }

    /**
     * {@inheritdoc}
     */
    public function setStyle(Stylable $style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return \MongoDB\Database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param int|string $id
     *
     * @return ObjectID
     */
    public function createObjectId($id = null)
    {
        return new ObjectID($id);
    }

    /**
     * {@inheritdoc}
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
    public function insert($collection, $document)
    {
        $result = $this->getDatabase()->selectCollection($collection)->insertOne($document);
        $identifier = $this->getStyle()->identifier($collection);
        $document->{$identifier} = $result->getInsertedId();
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

    /**
     * {@inheritdoc}
     */
    public function generateQuery(Collection $collection)
    {
        return $this->getMapping()->generateQuery($collection);
    }
}
