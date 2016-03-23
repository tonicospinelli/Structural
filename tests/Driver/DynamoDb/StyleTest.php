<?php

namespace Respect\Structural\Tests\Driver\DynamoDb;

use Respect\Structural\Driver\DynamoDb\Style;

class StyleTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldRetrieveIdentifier()
    {
        $this->assertEquals('_id', (new Style())->identifier('id'));
    }
}
