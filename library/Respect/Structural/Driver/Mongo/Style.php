<?php
/**
 * Created by PhpStorm.
 * User: aspinelli
 * Date: 2/24/16
 * Time: 10:11 PM
 * @author Antonio Spinelli <antonio.spinelli@kanui.com.br>
 */

namespace Respect\Structural\Driver\Mongo;

use Respect\Data\Styles\Standard;

class Style extends Standard
{
    public function identifier($name)
    {
        return '_' . parent::identifier($name);
    }
}
