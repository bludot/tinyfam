<?php

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return value($default);
        }
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }
        //if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            //return substr($value, 1, -1);
        //}
        return $value;
    }
}

spl_autoload_register(function ($className) {
if(strpos($className, '\\') > 0) {
    $className = explode('\\', $className);
    //array_shift($className);
    $className = implode('/', $className);
}
if (file_exists(ROOT . DS . 'library' . DS . strtolower($className) . '.php')) {
                require_once(ROOT . DS . 'library' . DS . strtolower($className) . '.php');
            } else if (file_exists(ROOT . DS . 'Core' . DS . 'Helpers' . DS . $className . '.php')) {
                require_once(ROOT . DS . 'Core' . DS . 'Helpers' . DS . $className . '.php');
            } else if (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . $className . '.php')) {
                require_once(ROOT . DS . 'application' . DS . 'controllers' . DS . $className . '.php');
            } else if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . $className . '.php')) {
                require_once(ROOT . DS . 'application' . DS . 'models' . DS . $className . '.php');
            } else if (file_exists(ROOT . DS . 'Core' . DS . $className . '.php')) {
                require_once(ROOT . DS . 'Core' . DS . $className . '.php');
            } else if(file_exists(ROOT . DS . $className . '.php') && strpos($className, 'Helpers') == 0) {
                require_once(ROOT . DS . $className . '.php');
            } else if(substr($className, 0, 4) == "Core" && strpos($className, 'Helpers') > 0) {
                if (file_exists(ROOT . DS . $className . '.php')) {
                    require_once(ROOT . DS . $className . '.php');
                } else {
                   $name = explode('/', $className);
                   $name = array_pop($name);
                require_once(ROOT . DS . $className . DS . $name . '.php');
                }

            } else {
                //echo "</br>error loading files</br>Classname: ".$className;
                //die("error autoloading files");
                return false;

            }
});


$app;


require_once (ROOT . DS . 'Core'. DS . 'App.php');



$app = new Core\App;
$app->setReporting(true);

require_once (ROOT . DS . 'config' . DS . 'config.php');
require_once (ROOT . DS . 'config' . DS . 'inflection.php');


return $app;
