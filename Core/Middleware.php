<?php

<?php

namespace Core;
use Core\Inflection;
use Core\Facade;
use Core\Cache;


class Middleware extends Facade {

    protected $routes;

    protected $cached;



    protected static function getFacadeAccessor()
    {
        return 'Middleware\MiddlewareProvider';
    }

}

