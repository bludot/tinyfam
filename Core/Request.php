<?php

namespace Core;
use Core\Session;

class Request {

    public $uri;

    public function __contruct($app) {
        $this->app = $app;
        $this->uri = urldecode(
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );
    }

    static function input($name) {
        if(isset($_GET[$name])) {
            return $_GET[$name];
        } else if(isset($_POST[$name])) {
            return $_POST[$name];
        } else {
            return false;
        }
    }

    static function query() {
        return $_SERVER['QUERY_STRING'];
    }

    static function getHeaders() {
        return getallheaders();
    }

    static function uri() {
        return urldecode(
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );
    }
}