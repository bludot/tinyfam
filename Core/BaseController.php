<?php

namespace Core;
use Core\Inflection;


class BaseController extends Template {
    
    protected $_controller;
    protected $_action;
    protected $_template;

    public $doNotRenderHeader;
    public $render;

    function __construct($controller, $action) {

        $this->_controller = ucfirst($controller);
        $this->_action = $action;
        
        $model = ucfirst(Inflection::singularize($controller)."Model");
        $this->doNotRenderHeader = 0;
        $this->render = 1;
        $this->model = Model::getInstance(ucfirst($controller));
        $this->_template = new Template($controller,$action);



    }



    function __destruct() {

    }

    /*function set($name,$value) {
        $this->_template->set($name,$value);
    }

    function render($path = "") {
            $this->_template->render($path);
    }

    function render($path = "") {
        $this->_template->render($path);
    }*/
        
}
