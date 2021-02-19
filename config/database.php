<?php

return [
    'driver'   => 'mysql',
    'host'     => getenv('DB_HOSTNAME') ? getenv('DB_HOSTNAME') : 'localhost',
    'username' => getenv('DB_USERNAME') ? getenv('DB_USERNAME') : 'root',
    'password' => getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : '',
    'database' => getenv('DB_DATANAME') ? getenv('DB_DATANAME') : 'test',
    'collation' => 'utf8_unicode_ci',
    'charset'  => 'utf8',
    'prefix'    => '',
];