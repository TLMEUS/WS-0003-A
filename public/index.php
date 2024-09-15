<?php
/**
 * This file contains the public/index.php for project WS-0003.
 *
 * File information:
 * Project Name: WS-0003
 * Module Name: public
 * File Name: index.php
 * File Author: Troy L. Marker
 * Language: PHP 8.3
 *
 * File Copyright: 06/2024
 */
declare(strict_types=1);


use App\Database;
use App\Models\LoginModel;

define("ROOT_PATH", dirname(path: __DIR__));
spl_autoload_register(callback: function (string $class_name) {
    require ROOT_PATH . "/src/" . str_replace(search: "\\", replace: "/", subject: $class_name) . ".php";
});
$dotenv = new Framework\Dotenv;
$dotenv->load(path: dirname( path: ROOT_PATH) . "/RWS-0001-A/config/.env");
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
$loginModel = new LoginModel($database);
if (!isset($_COOKIE['LoggedUser'])) {
    $loginModel->redirectToLogin();
}
$loginModel->loadLogin($_COOKIE['LoggedUser']);
set_error_handler(callback: "Framework\ErrorHandler::handleError");
set_exception_handler(callback: "Framework\ErrorHandler::handleException");
$router = require ROOT_PATH . "/config/routes.php";
$container = require ROOT_PATH . "/config/services.php";
$middleware = require ROOT_PATH . "/config/middleware.php";
$dispatcher = new Framework\Dispatcher($router, $container, $middleware);
$request = Framework\Request::createFromGlobals();
try {
    $response = $dispatcher->handle($request);
    $response->send();
} catch (ReflectionException $e) {
}


