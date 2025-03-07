<?php
session_start();
require '../coretools.php';

require path_to('core/Router.php');

$router = new Router();
require path_to('routes/web.php');
require path_to('routes/api.php');




$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_SERVER['REQUEST_METHOD'];

// dump($original_request, "request headers");
// dump($uri, "URI");
// dnd($method, "http method");

try {
    $router->route($uri, $method);
} catch (Exception $e) {
    die($e->getMessage());
}
