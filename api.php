<?php

define('SITE_ROOT', __DIR__);

require_once SITE_ROOT . "/utility/Database.php";
require_once SITE_ROOT . "./user/Roles.php";
require_once SITE_ROOT . "./user/User.php";
require_once SITE_ROOT . "./user/UserRepository.php";
require_once SITE_ROOT . "./user/UserController.php";

// --------------------------------------------------------------------
// Chequea si el usuario esta autenticado
// --------------------------------------------------------------------
if (!isset($_SERVER['PHP_AUTH_USER'])) 
{
    header('WWW-Authenticate: Basic realm="My API"');
    header('HTTP/1.1 401 Unauthorized');
    exit;
}


$db = new Database();
$ur = new UserRepository($db->getConnection()); 


// Cheque aque el usuario actual sea el administrador
$admin = $ur->findById(1);
$username = $_SERVER['PHP_AUTH_USER'];
$password = $_SERVER['PHP_AUTH_PW'];

if ($admin->username != $username) 
{
    header('HTTP/1.1 403 Forbidden');
    exit;
}

if ($admin->password != $password) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

/*
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Mi dominio"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Texto a enviar si el usuario pulsa el botón Cancelar';
    exit;
} else {
    $username = $_SERVER['PHP_AUTH_USER'];
    $passw = $_SERVER['PHP_AUTH_PW'];

    $currentUser = $ur->findByUserName($username);
    if ($currentUser->Role == ROLE_PAGE_1) 
    {

    }
    if ($currentUser->Role == ROLE_PAGE_2) 
    {
        
    }
}

*/
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pos = strripos($uri,"/user");
if ($pos == FALSE)
{
    header("HTTP/1.1 404 Not Found");
    exit();
}
$endPoint =  substr($uri, $pos + 1, strlen($uri)-$pos);
$tokens = explode( '/', $endPoint );

// Todos los endpoint empiezan con /user
if ($tokens[0] !== 'user') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the user id is, of course, optional and must be a number:
$userId = null;
if (isset($tokens[1])) {
    $userId = (int) $tokens[1];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

$controller = new UserController($ur, $requestMethod, $userId);
$controller->processRequest();
