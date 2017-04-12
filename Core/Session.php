<?php

namespace Core;
use Core\App;
use Core\Request;

class Session {

    function __construct() {
        session_start();
        if(Request::input('csrf')) {
            if($this->validateCSRF(rawurldecode(Request::input('csrf')))) {
                throw new \Exception("bad csrf token");
            }
        }
        $this->generateCRSF();

    }

    function set($name, $value) {
        $_SESSION[$name] = $value;
    }

    static function get($name) {
        if(isset($_SESSION[$name])) {
         return $_SESSION[$name];
         } else {
         return false;
         }
    }

    static function validateCSRF($csrf) {
        return strcmp($csrf, $_SESSION['csrf_token']) == 0;
    }

    static function generateCRSF() {
        $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
    }
}