<?php
    require_once "../controller/ControllerGroupeMusculaire.php";
    header("Content-Type: application/json");
    $methode=$_SERVER["REQUEST_METHOD"];
    $controller=new ControllerGroupeMusculaire();
    if($methode=="GET"){
        echo json_encode($controller->listerGroupesMusculaires());
    }
    if($methode=="POST"){
        echo json_encode($controller->ajouterGroupeMusculaire());
    }
?>
