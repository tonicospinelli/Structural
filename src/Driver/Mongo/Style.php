<?php

namespace Respect\Structural\Driver\Mongo;

use Respect\Data\Styles\Standard;

class Style extends Standard
{
    public function identifier($name)
    {
        return '_' . parent::identifier($name);
    }

    public function convertToDate(\DateTime $dateTime)
    {
        return new \MongoDate($dateTime->getTimestamp());
    }
    
}
