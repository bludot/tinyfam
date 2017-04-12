<?php

use Core\BaseController;

class MainController extends BaseController {

	function beforeAction () {

	}

	function getIndex() {
	    $this->set("some_var", "test");
		$this->render('main');
	}

	function afterAction() {

	}
}

