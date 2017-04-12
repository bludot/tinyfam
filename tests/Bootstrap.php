<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('VENDOR', ROOT . DS . 'vendor');

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
