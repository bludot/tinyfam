<?php

namespace Core;
use Core\App;

class Template {
	
	protected $variables = array();
	protected $_controller;
	protected $_action;
	
	function __construct($controller,$action) {
		$this->_controller = $controller;
		$this->_action = $action;
	}

	/** Set Variables **/

	function set($name,$value) {
		$this->variables[$name] = $value;
	}

	/** Display Template **/

	function send($data) {
	    echo $data;
	}

	function setHeader($header) {
	    header($header);
	}

	static function renderError($type, $msg) {
	    $html = new HTML;
	    echo $html->twig->render('error.html', ["type" => $type, "msg" => $msg]);
    }

    function json($data) {
        header('Content-Type: application/json');
        if(gettype($data) == "string") {
            echo $data;
        } else {
            echo json_encode($data);
        }
    }

    function render($path) {

		$html = new HTML;
		$config = App::getConfig("App");
		$this->variables['title'] = $config['title'];
		$this->variables['appName'] = $config['appName'];
		$this->variables['appUrl'] = $config['appUrl'];
		$this->variables['csrf'] = $config['csrf'];

		$path = preg_replace('/\./', '/', $path);
		if($path != "") {
        	echo $html->twig->render($path . '.html', $this->variables);
        } else {
            try {
                echo $html->twig->render('main.html', $this->variables);
            } catch(Exception $e) {
                throw new Exception($e);
            }
        }

    }

}