<?php

namespace Core\Route;

use Core\App;
use Core\Route\RouteCollection;
use Core\Route;

class RouteProvider extends RouteCollection {

    protected $cached;

    protected $middleware;

    public function routeURL($url) {

        $uri = ["url" => $url];
        $route = $this->routes->getVal("/".$url);

        if(!$route) {

            $route = $this->routes->getVal("/".preg_replace('/\/(.+?)(((?=\/)(?=\{))|$){1}/', "", $url));
        }

        if($route) {
            $uri = array_merge($uri, $route);

            // <name>Controller@<action> to url: <name>/<?action>
            $callbackUrl = preg_replace(
                '/^\/+|\/+$/',
                "",
                strtolower(
                    preg_replace(
                        '/(.+?)Controller@(.+?)$|(.+?)Controller/',
                        '/$1/$2/$3',
                        $route['callback']
                    )
                )
            );

            $url_ = explode('/', $url);

            $urlRoute = explode('/', ltrim($route['url'], '/'));


            // Check if action else, return default
            if(!strpos($route['callback'], '@')) {
                if(count($url_) > count($urlRoute)) {
                    preg_match('/\/(.+?)((\/(?=\{))|$){1}/', $url, $matches, PREG_OFFSET_CAPTURE, 0);
                    $callbackUrl.="/".$matches[1][0];

                } else {
                    $callbackUrl.="/".$this->config["defaultAction"];
                }
            }



            $i = 0;

            // return all args
            $final = array_reduce($urlRoute, function($prev, $curr) use ($url_) {
                global $i;
                if(isset($curr[0]) && $curr[0] == '{') {
                  $prev.= "/{" . $url_[$i] . "}";
                }
                $i++;

                return $prev;
            }, "");

            $final = $callbackUrl.$final;
            $uri['url'] = $final;

            return $uri;


        }
        $uri['method'] = "get";
        if($uri["url"] == "" || $uri["url"] == NULL) {

            $uri["url"] = $this->config["defaultController"]."/".$this->config["defaultAction"];
        }
        return ($uri);
    }

    function callHook($req) {
        /*if(!$this->cached) {
            $this->writeCache();
        }*/
        $this->checkCached();
        //serialize($array);
        $uri = ltrim($req->uri(), '/');

        $queryString = array();
        $args = [];
        $uri = $this->routeURL($uri);
        $url = $uri['url'];
        $urlArray = array();
        $urlArray = explode("/",$url);
        $controller = $urlArray[0];

        array_shift($urlArray);

        if (isset($urlArray[0]) && substr($urlArray[0], 0, 1) != "{") {
            $action = ucfirst($urlArray[0]);
            array_shift($urlArray);
            while($arg = array_shift($urlArray)) {
                $args[] = substr($arg, 1, -1);

            }
        } else {
            $action = 'Index'; // Default Action
            if(isset($urlArray[0])) {

                while($arg = array_shift($urlArray)) {
                    $args[] = substr($arg, 1, -1);

                }
            }
        }
        $queryString = $urlArray;

        $controllerName = ucfirst($controller).'Controller';


        $dispatch = new $controllerName($controller,$action);
        $serverMethod = strtolower($_SERVER['REQUEST_METHOD']);

        if(!method_exists($dispatch, $serverMethod.$action) && !method_exists($dispatch, $action)) {
            throw new \Exception("This doesnt exist!");
        }
        if(!method_exists($dispatch, $serverMethod.$action) && !method_exists($dispatch, $action) || $uri['method'] !== $serverMethod) {
            Template::renderError("500", "Does not accept ${serverMethod} Request");
        } else {
            $action = $serverMethod.$action;
        }

        if ((int)method_exists($controllerName, $action)) {
            call_user_func_array(array($dispatch,"beforeAction"),$args);
            call_user_func_array(array($dispatch,$action),$args);
            call_user_func_array(array($dispatch,"afterAction"),$args);
        } else {
            /* Error Generation Code Here */
        }
        return true;

    }

    function performAction($controller,$action,$queryString = null,$render = 0) {

        $controllerName = ucfirst($controller).'Controller';
        $dispatch = new $controllerName($controller,$action);
        $dispatch->render = $render;
        return call_user_func_array(array($dispatch,$action),$queryString);
    }

    function checkCached() {
        if(!$this->_get("cached")) {
            App::loadLibrary('Routes');
            $this->writeCache();
        }
    }

}