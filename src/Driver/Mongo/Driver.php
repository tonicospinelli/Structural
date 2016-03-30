<?php

namespace Respect\Structural\Driver\Mongo;

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
     * @var \MongoClient
     */
    private $connection;

    /**
     * @var \MongoDB
     */
    private $database;

    /**
     * @var Mapping
     */
    private $mapping;

    /**
     * Driver constructor.
     *
     * @param \MongoClient $connection
     * @param string $database
     */
    public function __construct(\MongoClient $connection, $database)
    {
        $this->connection = $connection;
        $this->database = $connection->selectDB($database);
        $this->mapping = new Mapping();
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
     *
     * @return array
     */
    public function fetch(\Iterator $cursor)
    {
        $data = $cursor->current();
        $cursor->next();

        return $data;
    }

    /**
     * @param array $collection
     * @param array $query
     *
     * @return \Iterator
     */
    public function find($collection, array $query = [])
    {
        $cursor = $this->getDatabase()->selectCollection($collection)->find($query);
        $cursor->rewind();

        return $cursor;
    }

    /**
     * @param Collection $collection
     * @param $document
     *
     * @return void
     */
    public function insert($collection, $document)
    {
        $this->getDatabase()->selectCollection($collection)->insert($document);
    }

    public function update($collection, $criteria, $document)
    {
        $this->getDatabase()->selectCollection($collection)->update($criteria, $document);
    }

    public function remove($collection, $criteria)
    {
        $this->getDatabase()->selectCollection($collection)->remove($criteria);
    }

    public function generateQuery(Collection $collection)
    {
        return $this->mapping->generateQuery($collection);
    }

}
