<?php

/**
 * set container
 */
$container = $app->getContainer();

/* app */
$container['app'] = function () use($app) {
    return $app;
};

$mysql_config = $config['db'];
$pdo = new PDO("mysql:host=" . $mysql_config['host'] . ";dbname=" . $mysql_config['dbname'],
    $mysql_config['user'], $mysql_config['pass']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

/* pdo */
$container['pdo'] = function () use ($pdo) {
    return $pdo;
};

/* db */
$container['db'] = function () use ($pdo) {
    return new NotORM($pdo, new NotORM_Structure_Convention($primary = 'id', $foreign = '%s_id', $table = '%s', $prefix = 'ys_'));
};

/* redis */
$container['redis'] = function () use ($config) {
    $redis_config = $config['redis'];
    $redis = new \Redis();
    $redis->connect($redis_config['host'], $redis_config['port']);
    return $redis;
};

/* notFoundHandler */
$container['notFoundHandler'] = function () {
    return '\front\api\ErrorController::notFound';
};