<?php

namespace Core;

use Core\Facade;

class Database extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'Database\DatabaseProvider';
    }



}