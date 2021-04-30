<?php
require "../bootstrap.php";

use Src\Controller\AuthController;
use Src\Controller\OrderController;
use Src\Controller\ProductController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
if ($uri[1] == 'order' && $uri[2] == 'create') {
    $controller = new OrderController($dbConnection);
    $controller->create();
} elseif ($uri[1] == 'order' && $uri[2] == 'status') {
    $controller = new OrderController($dbConnection);
    $controller->order_status();
} elseif ($uri[1] == 'order' && $uri[2] == 'update') {
    $controller = new OrderController($dbConnection);
    $controller->order_status_update();
} elseif ($uri[1] == 'product' && $uri[2] == 'insert') {
    $controller = new ProductController($dbConnection);
    $controller->insert();
} elseif ($uri[1] == 'product' && $uri[2] == 'show') {
    $controller = new ProductController($dbConnection);
    $controller->show($uri[3]);
} elseif ($uri[1] == 'product' && $uri[2] == 'update') {
    $controller = new ProductController($dbConnection);
    $controller->update();
} elseif ($uri[1] == 'product' && $uri[2] == 'delete') {
    $controller = new ProductController($dbConnection);
    $controller->delete();
} elseif ($uri[1] == 'user' && $uri[2] == 'signup') {
    $controller = new AuthController($dbConnection);
    $controller->signup();
} elseif ($uri[1] == 'user' && $uri[2] == 'login') {
    $controller = new AuthController($dbConnection);
    $controller->login();
} elseif ($uri[1] == 'user' && $uri[2] == 'auth') {
    $controller = new AuthController($dbConnection);
    $controller->auth_check();
} elseif ($uri[1] == 'user' && $uri[2] == 'token_verify') {
    $controller = new AuthController($dbConnection);
    $controller->token_verify();
} else {
    echo json_encode([
        'status' => "Bad routing request",
    ]);
    http_response_code(400);
}
