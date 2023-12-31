<?php
    require __DIR__ . "/vendor/autoload.php";
    if($_SERVER['REQUEST_METHOD'] === "POST"){
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $database = new Database(
            $_ENV['DB_HOST'] , 
            $_ENV['DB_NAME'] , 
            $_ENV['DB_USER'] , 
            $_ENV['DB_PASS']
        );
        $connection = $database->getConnection();
        
        $sql = "INSERT INTO user (name , username , password_hash , api_key ) 
                VALUES (:name , :username , :password_hash , :api_key)";

        $api_key = bin2hex(random_bytes(16));   
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":name" , $_POST["name" ], PDO::PARAM_STR);
        $stmt->bindValue(":username" , $_POST["username"] , PDO::PARAM_STR);
        $stmt->bindValue(":password_hash" ,password_hash($_POST["password"] , PASSWORD_DEFAULT) , PDO::PARAM_STR );
        $stmt->bindValue(":api_key" , $api_key , PDO::PARAM_STR);

        $stmt->execute();

        echo "Thank you for registering . Your api key is " . $api_key;
        exit;
    }
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
</head>
<body>
    <main class="container">
        <h1>Register form</h1>
        <form method="post">
            <label for="name">
                Name
                <input type="text" name="name" id="name">
            </label>
            <label for="username">
                Username
                <input type="text" name="username" id="username">
            </label>
            <label for="password">
                Password
                <input type="password" name="password" id="password">
            </label>
            <button>Register</button>
        </form>
    </main>
</body>
</html>