<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once "GroupeMusculaire.php";


class GestionGroupeMusculaire{
    private static $path_file =__DIR__ . "/../database/gestionGroupeMusculaire.json";

    public function afficherTous(){
        $data = [];
        $json = json_decode(file_get_contents(self::$path_file),true);
        $groupes =$json ;
        foreach ($groupes as $groupe) {
            $g = new groupeMusculaire($groupe["id"]?? 0,$groupe["nom"]?? "",$groupe["description"]?? "");
            $data[] = $g;
        }
        return $data;
    }
    public function ajouter($nom ,$description){
        $data = [];
        $groupes = $this->afficherTous();
        $id = 1;
        if(count($groupes) > 0){
            $last = $groupes[count($groupes) -1];
            $id = $last->getId() + 1;
        }

        $groupes[] = new groupeMusculaire($id,$nom,$description);

        foreach ($groupes as $groupe) {
            $data[]= [
                "id" => $groupe->getId(),
                "nom" => $groupe->getNom(),
                "description" => $groupe->getDescription()
            ];
        }

        file_put_contents(self::$path_file,json_encode($data,JSON_PRETTY_PRINT));
        
    }
}