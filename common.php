<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
session_start();
define('PRIVATE_DIR', './');
define('SERVER_DIR', [
    'CLASS' => PRIVATE_DIR . 'class/',
    'INIT' => PRIVATE_DIR . 'init/',
    'HTML' => PRIVATE_DIR . 'html/',
    'CSS' => PRIVATE_DIR . 'css/',
    'IMG' => PRIVATE_DIR . 'images/',
    'JS' => PRIVATE_DIR . 'js/',
]);
//require(PRIVATE_DIR . 'vendor/autoload.php');

spl_autoload_register(function ($cls) {
    $ns = explode("\\", $cls);
    $fl = SERVER_DIR['CLASS'] . end($ns) . '.php';
    if (is_readable($fl)) require $fl;
});

function debug()
{
    $arr = debug_backtrace();
    echo '<div>' . $arr[0]['file'] . '  ' . $arr[0]['line'] . '</div>
    <pre style="padding: 5px;color:#FFF;background-color:#2A5D84;">';
    foreach (func_get_args() as $val) print_r($val);
    echo '</pre>';
}
