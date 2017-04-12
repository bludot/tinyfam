<?php

use Core\Model;

class CategoryModel extends Model {
    // Declare default value for each field here
    protected $_properties = [
        'id' => null,
        'name' => '',
        'parent_id' => ''
    ];

    // Declare data type for each field here
    protected static $_meta = [
        'id' => 'int',
        'name' => 'str',
        'parent_id' => 'int'
    ];

    // Override the `table_name` getter
    public function get_table_name() {
         return 'categories';
    }



    var $hasMany = array('Product' => 'Product');
    var $hasOne = array('Parent' => 'Category');

}