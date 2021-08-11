<?php

    require("config.php");
    date_default_timezone_set("Europe/Madrid");

    //conectamos y seleccionamos db
try {
    $conn = new PDO("mysql:host=" . $mysql_credentials['host'] . ";dbname=" . $mysql_credentials['database'], $mysql_credentials['user'], $mysql_credentials['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4; SET time_zone = '+02:00'"));
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}
$conn->query("SET NAMES utf8mb4");
?>
