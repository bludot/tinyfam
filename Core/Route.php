<?php

namespace Core;
use Core\Inflection;
use Core\Facade;
use Core\Cache;
use Core\Helpers\RouterTrie;

class Route extends Facade {

    protected $routes;

    protected $cached;



    protected static function getFacadeAccessor()
    {
        return 'Route\RouteProvider';
    }

}

