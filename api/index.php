<?php 
    declare(strict_types=1);
    require dirname(__DIR__) . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    set_error_handler("ErrorHandler::handleError");
    set_exception_handler("ErrorHandler::handleException");
    $path = parse_url($_SERVER['REQUEST_URI'] , PHP_URL_PATH);
    $parts = explode('/' , $path);
    $resource = $parts[2];
    $id = $parts[3] ?? null;

    if($resource !== 'tasks'){
        http_response_code(404);
        exit;
    }
    header("Content-Type: application/json; charset=UTF-8");
    require dirname(__DIR__) . "/src/TaskController.php";
    $db = new Database($_ENV['DB_HOST'] , $_ENV["DB_NAME"] , $_ENV["DB_USER"] , $_ENV["DB_PASS"]);
    $db->getConnection();
    $taskGateway = new TaskGateway($db);
    $controller = new  TaskController($taskGateway);
    $controller->processRequest($_SERVER['REQUEST_METHOD'] , $id);
?>