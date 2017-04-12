<?php

namespace Core\Database;

use Core\App;
use Core\Model;

class DatabaseProvider {

    protected $config;

    function __construct() {
        $this->config = App::getConfig('Database');
    }

    function boot() {
        Model::setMedoo(new Medoo($this->config));
    }

}