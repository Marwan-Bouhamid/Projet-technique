<?php
$host = 'localhost';
$user = 'root';
$pass = '12345678';
$dbname = 'fitness';
$port = '3306';

try{

    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE , PDO::FETCH_ASSOC);
}catch(PDOException $e){
    die("failde connection".$e->getMessage());
}
