Respect/Structural
==================


```php
require_once __DIR__ . '/vendor/autoload.php';

$mongoDb = new MongoClient();

$driver = new \Respect\Structural\Driver\Mongo\Driver($mongoDb, 'carmen');

$mapper = new \Respect\Structural\Mapper($driver, 'respect');
$mapper->setStyle(new \Respect\Structural\Driver\Mongo\Style());

$authors = $mapper->authors->fetchAll();

foreach ($authors as $author) {
    var_dump((string)$author->_id);
}

$author = new \stdClass();
$author->firstName = 'Antonio';
$mapper->authors->persist($author);
$mapper->flush();

var_dump("'{$author->firstName}' was created with id({$author->_id})");

$author->lastName = 'Spinelli';
$mapper->authors->persist($author);
$mapper->flush();

// find author by ID
$foundAuthor = $mapper->authors[(string)$author->_id]->fetch();
var_dump("{$foundAuthor->firstName} {$foundAuthor->lastName}");

$mapper->authors->remove($author);
$mapper->flush();

$author = $mapper->authors(['lastName' => 'Spinelli'])->fetch();
var_dump($author ? "'Spinelli' was found" : "'Spinelli' removed.");
```