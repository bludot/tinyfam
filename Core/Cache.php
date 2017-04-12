<?php

namespace Core;

abstract class Cache {

    protected static $app;

    private static $config;

    static function setApp($app) {
        static::$app = $app;
        if(static::$app->config['environment'] == "development") {
            static::$config = ["defaultLifespan" => 0];
        } else {
            static::$config = static::$app->getConfig("Cache");
        }
    }


	static function get($fileName) {


	    $lifespan = static::$config["defaultLifespan"];
	    if(isset($config[$fileName."Lifespan"])) {
	        $lifespan = $config[$fileName."Lifespan"];
	    }
		$fileName = ROOT.DS.'tmp'.DS.'cache'.DS.$fileName;
		$now   = time();
		if(file_exists($fileName)) {
		    if ($now - filemtime($fileName) >= $lifespan) {
                unlink($fileName);
            }
		}
		if (file_exists($fileName)) {
			$handle = fopen($fileName, 'rb');
			$variable = fread($handle, filesize($fileName));
			fclose($handle);
			return unserialize($variable);
		} else {
		    $handle = fopen($fileName, 'a');
		    fwrite($handle, serialize("NULL"));
		    fclose($handle);

			return null;
		}
	}
	
	static function set($fileName,$variable) {
		$fileName = ROOT.DS.'tmp'.DS.'cache'.DS.$fileName;
		$handle = fopen($fileName, 'w');
		fwrite($handle, serialize($variable));
		fclose($handle);
	}


}
