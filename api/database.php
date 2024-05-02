<?php

$servername = "localhost";
$username_name = "root";
$password = "root";
$database = "sssdatabase";
$port = "3306";

try {
    $connection = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username_name, $password);
    $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

return $connection;