<?php

//database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'Camo_it');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_CHARSET', 'utf8');

define('PRJ_PATH', str_replace('\\', '/', __DIR__));
define('CHARSET', 'utf-8');
define('PRJ_NAME', 'Camo_it');

define('ERROR_FILE', 'Errors.txt');

define('TOPICS_ON_PAGE', 20);
define('MSGS_ON_PAGE', 10);


if(substr_count($_SERVER['REQUEST_URI'], PRJ_NAME)) {
    define('PRJ_URL', '/'.PRJ_NAME);    
} else {
    define('PRJ_URL', '');    
}

define('CSS_URL', PRJ_URL.'/css');
define('JS_URL', PRJ_URL.'/js');
define('TPL_URL', PRJ_URL.'/application/views');
