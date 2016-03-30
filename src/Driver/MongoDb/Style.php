<?php

namespace Respect\Structural\Driver\MongoDb;

use Respect\Data\Styles\Standard;

class Style extends Standard
{
    public function identifier($name)
    {
        return '_' . parent::identifier($name);
    }

    public function convertToMongoDate(\DateTime $dateTime)
    {
        return new \MongoDate($dateTime->getTimestamp());
    }
    
}
