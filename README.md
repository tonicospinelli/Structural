Respect/Structural
==================


```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$mongoDb = new MongoClient();

$driver = new \Respect\Structural\Driver\Mongo\Driver($mongoDb, 'carmen');

$mapper = new \Respect\Structural\Mapper($driver, 'respect');
$mapper->setStyle(new \Respect\Structural\Driver\Mongo\Style());

$authors = $mapper->authors->fetchAll();

echo "Fetching all authors:" . PHP_EOL;
foreach ($authors as $index => $author) {
    echo "{$index} {$author->firstName} {$author->lastName}" . PHP_EOL;
}

$author = new \stdClass();
$author->firstName = 'Antonio';
$mapper->authors->persist($author);
$mapper->flush();

echo "'{$author->firstName}' was created with id({$author->_id})".PHP_EOL;

$author->lastName = 'Spinelli';
$mapper->authors->persist($author);
$mapper->flush();

echo "last name was updated to '{$author->lastName}' from id({$author->_id})".PHP_EOL;

// find author by ID
$foundAuthor = $mapper->authors[(string)$author->_id]->fetch();
echo "find by id('{$author->_id}') {$foundAuthor->firstName} {$foundAuthor->lastName}".PHP_EOL;

$mapper->authors->remove($author);
$mapper->flush();

$author = $mapper->authors(['lastName' => 'Spinelli'])->fetch();
echo ($author ? "'Spinelli' was found" : "'Spinelli' removed.");
```