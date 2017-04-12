<?php

namespace Core;
use Core\Model;
use Core\Database\Medoo;
use Core\Session;
use Core\Request;
use Core\Database\DatabaseProvider;
use Core\Route\RouteProvider;
use Core\Helpers\Arr;

class App {

    public $Providers = [];

    public $runner;

    private $session;

    public $config;



    public function __construct() {
        $this->config = $this->getConfig("App");
        Cache::setApp($this);
        $this->loadProviders([
            new DatabaseProvider($this),
            new Request($this),
            new RouteProvider($this),
            new Inflection(),
            new Session()
        ]);

        Facade::setFacadeApplication($this);
    }

    protected function loadProviders($providers) {
        foreach($providers as $provider) {
            $this->loadProvider($provider);
        }
    }

    protected function loadProvider($provider) {
        if (($registered = $this->getProvider($provider))) {
            return $provider;
        }

        /*if (method_exists($provider, 'register')) {
                    $this->Providers[] = $provider;
        }*/
        $this->Providers[] = $provider;
        return $provider;
    }

    function getProvider($provider) {
        $name = is_string($provider) ? $provider : get_class($provider);

        return Arr::first($this->Providers, function ($value) use ($name) {
            return $value instanceof $name;
        });
    }

    /** Check if environment is development and display errors **/

    function setReporting($reporting) {
        if ($reporting == true) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
        }
    }

    function get($name) {
        return $this->{$name};
    }

    static function loadLibrary($className) {



        if (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . $className . '.php')) {
            require_once(ROOT . DS . 'application' . DS . 'controllers' . DS . $className . '.php');
        } else if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . $className . '.php')) {
            require_once(ROOT . DS . 'application' . DS . 'models' . DS . $className . '.php');
        } else if (file_exists(ROOT . DS . 'application' . DS . $className . '.php')) {
            require_once(ROOT . DS . 'application' . DS . $className . '.php');
        } else if (file_exists(ROOT . DS . 'Core' . DS . $className . '.php')) {
            require_once(ROOT . DS . 'Core' . DS . $className . '.php');
        } else if (file_exists(ROOT . DS . 'library' . DS . strtolower($className) . '.php')) {
            require_once(ROOT . DS . 'library' . DS . strtolower($className) . '.php');
        } else {
            //echo "</br>error loading files</br>Classname: ".$className;
            //die("error autoloading files");
            return false;

        }
        return true;
    }

    public function boot() {
        $bootqueue = include(ROOT . DS . 'Core/Boot.php');
        foreach($bootqueue as $boot) {
            call_user_func_array(array($boot, "boot"),[]);
        }
    }

    public function init() {

        $req = $this->getProvider('Core\Request');
        Route::callHook($req);

    }


    public static function getConfig($name) {
        $config = include(ROOT . DS . 'config/'.$name.'.php');

        $config['csrf'] = Session::get('csrf_token');
        return $config;
    }
}


