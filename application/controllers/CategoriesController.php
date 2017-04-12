<?php


use Core\BaseController;
use Core\Request;


class CategoriesController extends BaseController {
	
	function beforeAction () {

	}

	function getView($categoryId = null) {

	    $subcategories = $this->model->many(['parent_id[=]' => $categoryId, "ORDER" => ['name' => 'ASC']]);
		$this->set('subcategories',$subcategories);
		$this->render('categories.view');

	}
	
	
	function getIndex() {
        $categories = $this->model->many(['parent_id[=]' => 0, "ORDER" => ['name' => 'ASC']]);
		$this->set('categories',$categories);
		$this->render('categories.index');
	
	}

	function afterAction() {

	}


}