<?php
require "../bootstrap.php";
use Src\Controller\PersonController;
use Src\Controller\ProductController;
use Src\Controller\AuthController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// all of our endpoints start with /person
// everything else results in a 404 Not Found

// if ($uri[1] !== 'person') {
//     header("HTTP/1.1 404 Not Found");
//     exit();
// }

// the user id is, of course, optional and must be a number:
$userId = null;
if (isset($uri[2])) {
    $userId = (int) $uri[2];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
if($uri[1] == 'product' && $uri[2] == 'insert'){
    $controller = new ProductController($dbConnection);
    $controller->insert();
} 
elseif($uri[1] == 'user' && $uri[2] == 'signup'){
    $controller = new AuthController($dbConnection);
    $controller->signup();
}
elseif($uri[1] == 'user' && $uri[2] == 'login'){
    $controller = new AuthController($dbConnection);
    $controller->login();
}
elseif($uri[1] == 'user' && $uri[2] == 'auth'){
    $controller = new AuthController($dbConnection);
    $controller->auth_check();
}
elseif($uri[1] == 'user' && $uri[2] == 'token_verify'){
    $controller = new AuthController($dbConnection);
    $controller->token_verify();
}
else{
    $controller = new PersonController($dbConnection, $requestMethod, $userId);
    $controller->processRequest();
}
