<?php
//ini_set("display_errors","On");
//error_reporting(E_ALL);
require(__DIR__ . '/../vendor/autoload.php');
spl_autoload_register(function ($className) {
    $file = dirname(__DIR__) . '/' . str_replace("\\", "/", $className) . '.php';
    if (is_file($file)) {
        require_once dirname(__DIR__) . '/' . str_replace("\\", "/", $className) . '.php';
    }
});

$config = array_merge(
    require(__DIR__ . '/config/config.php'),
    require(__DIR__ . '/config/config-local.php')
);
function p($a){
    echo "<pre>";
    print_r($a);die;
}
try {
    $app = new \Slim\App(["settings" => $config]);
    require(__DIR__ . '/config/container.php');
    require(__DIR__ . '/route/route.php');
    $app->run();
} catch (\Exception $exception) {
    print_r($exception->getTraceAsString());
}