<?php

namespace Core\Route;
use Core\App;
use Core\Cache;
use Core\Helpers\RouterTrie;

class RouteCollection {
    protected $routes = [];

    public function __construct() {
        $this->routes = new RouterTrie();
                $this->config = App::getConfig("Routes");
                $file = Cache::get('routes');
                if($file == NULL) {
                    $this->cached = false;
                    $this->routes = new RouterTrie();
                } else {
                    $this->cached = true;
                    $this->routes = $file;
                }
    }

    public function get($url, $callback) {


        $this->addRoute($url, "get", $callback);
    }

    static public function post($url, $callback) {
        $this_ = self::getFacadeRoot();

        $this_->addRoute($url, "post", $callback);
    }

    public function addRoute($url,$method, $callback) {
        $this->routes->add($url, ["url" => $url, 'method' => $method, 'callback' => $callback]);
        //$this->writeCache();
        //$this->routes[preg_replace("/\//", "\\/", $url)] = ['method' => $method, 'callback' => $callback];
    }

    function writeCache() {
        Cache::set('routes', $this->routes);
    }

    public function getRoutes() {
        return $this->routes;
    }

    function _get($name) {
        return $this->$name;
    }

}
