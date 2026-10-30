<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
require_once "../controller/controller.php";

header('Content-Type : application/json');
$methode = $_SERVER["REQUEST_METHOD"];
$controller = new controller();
if($methode == "GET"){
echo json_encode($controller->listerGroupeMusculaire());
}
if($methode == "POST"){
echo json_encode($controller->ajouterGroupeMusculaire());
}