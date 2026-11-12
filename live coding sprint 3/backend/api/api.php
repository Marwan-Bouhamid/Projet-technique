<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
require_once "../service/WorkoutService.php";

header('Content-Type : application/json');
$methode = $_SERVER["REQUEST_METHOD"];
$WorkoutService = new WorkoutService();
if($methode == "GET"){
echo json_encode($WorkoutService->listerGroupeMusculaire());
}
if($methode == "POST"){
echo json_encode($WorkoutService->ajouterGroupeMusculaire());
}