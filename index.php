<?php
$startTimePhp = microtime(true);
require_once('config.php');
header('Content-Type: text/html; charset='.CHARSET);

function __autoload($class_name) {
    require_once $class_name . '.php';
}

set_include_path(get_include_path()
                .PATH_SEPARATOR.PRJ_PATH.'/application/controllers/'
                .PATH_SEPARATOR.PRJ_PATH.'/application/models/'
                .PATH_SEPARATOR.PRJ_PATH.'/application/views/'
                .PATH_SEPARATOR.PRJ_PATH.'/lib');

FrontController::getInstance()->route();
