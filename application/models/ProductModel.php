<?php

use Core\Model;

class ProductModel extends Model {

    // Declare default value for each field here
        protected $_properties = [
            'id' => null,
            'category_id' => null,
            'name' => '',
            'price' => ''
        ];

        // Declare data type for each field here
        protected static $_meta = [
            'id' => 'int',
            'category_id' => 'int',
            'name' => 'str',
            'price' => 'str'
        ];

        // Override the `table_name` getter
        public function get_table_name() {
             return 'products';
        }

	var $hasOne = array('Category' => 'Category');
	var $hasManyAndBelongsToMany = array('Tag' => 'Tag');
}