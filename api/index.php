<?php 
    declare(strict_types=1);
    require dirname(__DIR__) . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    set_error_handler("ErrorHandler::handleError");
    set_exception_handler("ErrorHandler::handleException");

    
    if(empty($_SERVER["HTTP_X_API_KEY"])){
        http_response_code(400);
        echo json_encode(["message" => "missing API key"]);
        exit;
    }
    $api_key = $_SERVER["HTTP_X_API_KEY"];
    $db = new Database($_ENV['DB_HOST'] , $_ENV["DB_NAME"] , $_ENV["DB_USER"] , $_ENV["DB_PASS"]);
    $user_gateway = new UserGateway($db);
    if($user_gateway->getByApiKey($api_key) === false){
        http_response_code(401);
        echo json_encode(["message" => "invalid API key"]);
        exit;
    }

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
   
    $taskGateway = new TaskGateway($db);
    $controller = new  TaskController($taskGateway);
    $controller->processRequest($_SERVER['REQUEST_METHOD'] , $id);
?>