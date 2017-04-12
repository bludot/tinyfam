<?php


return [
    'database_type' =>  env('DB_CONNECTION', 'mysql'),
    'database_name' =>  env('DB_DATABASE', 'framework'),
    'server'        =>  env('DB_HOST', '127.0.0.1'),
    'port'          =>  env('DB_PORT', '3306'),
    'username'      =>  env('DB_USERNAME', ''),
    'password'      =>  env('DB_PASSWORD', ''),
    'charset'       =>  'utf8',
];