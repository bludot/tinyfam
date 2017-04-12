<?php

use Core\BaseController;
use Core\Request;
use Core\Session;

class RestController extends BaseController {

	function beforeAction () {

	}

    function getTest($value_, $test) {

        $arr = ["test2", $value_, $test];
        $this->json($arr);

    }

	function getIndex($value_, $test) {

        $arr = [$value_, $test];
        $this->json(json_encode($arr));

    }

	function postIndex() {
	    //$this->set("some_var", "test");
		//$this->render('main');
		$this->json('{"test":"test"}');

	}

	function postCsrf() {
        $this->json('{"crsf": "'.Session::get('csrf_token').'", "valid":"'.(Session::validateCSRF(Request::input('csrf')) ? "true" : "false").'"}');
	}

	function getCsrf() {
	    $this->setHeader("Content-Type: text/html");
	    $this->send("<h2>".Session::get('csrf_token')."</h2>");
	}

	function afterAction() {

	}
}

