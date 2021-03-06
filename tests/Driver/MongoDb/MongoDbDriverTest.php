<?php

namespace Respect\Structural\Tests\Driver\MongoDb;

use MongoDB\BSON\ObjectID;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\InsertOneResult;
use Respect\Data\Collections\Collection;
use Respect\Structural\Driver\MongoDb\MongoDbDriver;
use Respect\Structural\Tests\Driver\TestCase;

class MongoDbDriverTest extends TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('mongo') || !extension_loaded('mongodb')) {
            $this->markTestSkipped('missing mongo or mongodb extension');
        }

        if (extension_loaded('mongodb') && !class_exists('\MongoDB\Client')) {
            $this->markTestSkipped('missing mongodb library');
        }

        parent::setUp();
    }

    public function createDriver($connection = null)
    {
        if (is_null($connection)) {
            $connection = $this->createConnection();
        }

        return new MongoDbDriver($connection, 'database');
    }

    public function getConnectionInterface()
    {
        return Client::class;
    }

    public function getMockConnectionRetrieveEmptyResult()
    {
        $collection = $this->getMockBuilder(\MongoDB\Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $collection->expects($this->once())->method('find')->willReturn(new \ArrayIterator());

        $database = $this->getMockBuilder(Database::class)
            ->disableOriginalConstructor()
            ->setMethods(['selectCollection'])
            ->getMock();
        $database->expects($this->once())->method('selectCollection')->willReturn($collection);

        return $this->createConnection('selectDatabase', $database);
    }

    public function getMockConnectionRetrieveFilledResult()
    {
        $result = new \ArrayIterator([
            [
                '_id' => 1,
                'name' => 'Test',
            ],
        ]);
        $collection = $this->getMockBuilder(\MongoDB\Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $collection->expects($this->once())->method('find')->willReturn($result);

        $database = $this->getMockBuilder(Database::class)
            ->disableOriginalConstructor()
            ->setMethods(['selectCollection'])
            ->getMock();
        $database->expects($this->once())->method('selectCollection')->willReturn($collection);

        return $this->createConnection('selectDatabase', $database);
    }

    public function getMockConnectionInsertOne()
    {
        $insertResult = $this->getMockBuilder(InsertOneResult::class)
            ->disableOriginalConstructor()
            ->setMethods(['getInsertedId'])
            ->getMock();
        $insertResult->expects($this->once())->method('getInsertedId')->willReturn(new ObjectID('56d6fb233f90a8231f0041a9'));

        $collection = $this->getMockBuilder(\MongoDB\Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['insertOne'])
            ->getMock();
        $collection->expects($this->once())->method('insertOne')->willReturn($insertResult);

        $database = $this->getMockBuilder(Database::class)
            ->disableOriginalConstructor()
            ->setMethods(['selectCollection'])
            ->getMock();
        $database->expects($this->once())->method('selectCollection')->willReturn($collection);

        return $this->createConnection('selectDatabase', $database);
    }

    public function getMockConnectionUpdateOne()
    {
        $collection = $this->getMockBuilder(\MongoDB\Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['updateOne'])
            ->getMock();
        $collection->expects($this->once())->method('updateOne')->willReturn(null);

        $database = $this->getMockBuilder(Database::class)
            ->disableOriginalConstructor()
            ->setMethods(['selectCollection'])
            ->getMock();
        $database->expects($this->once())->method('selectCollection')->willReturn($collection);

        return $this->createConnection('selectDatabase', $database);
    }

    public function getMockConnectionRemoveOne()
    {
        $collection = $this->getMockBuilder(\MongoDB\Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['deleteOne'])
            ->getMock();
        $collection->expects($this->once())->method('deleteOne')->willReturn(null);

        $database = $this->getMockBuilder(Database::class)
            ->disableOriginalConstructor()
            ->setMethods(['selectCollection'])
            ->getMock();
        $database->expects($this->once())->method('selectCollection')->willReturn($collection);

        return $this->createConnection('selectDatabase', $database);
    }

    public function provideGenerateQueryShouldReturnSimpleFindById()
    {
        return [
            'simple return' => [
                Collection::my_coll('56d6fb233f90a8231f0041a9'),
                [
                    '_id' => '56d6fb233f90a8231f0041a9'
                ]
            ]
        ];
    }

    public function provideCollectionAndSearchShouldRetrieveEmptyResult()
    {
        return [
            ['collection', ['_id' => 1]]
        ];
    }

    public function provideGenerateQueryShouldUsePartialResultSets()
    {
        return [
            'simple' => [
                Collection::article()->author['56d6fb233f90a8231f0041a9'],
                [
                    'author._id' => new ObjectID('56d6fb233f90a8231f0041a9'),
                ]
            ]
        ];
    }

    public function provideCollectionAndSearchShouldRetrieveFilledResult()
    {
        return [
            'simple result' => [
                'authors',
                ['_id' => 1],
                new \ArrayIterator([
                    [
                        '_id' => 1,
                        'name' => 'Test'
                    ]
                ])
            ],
        ];
    }
}
