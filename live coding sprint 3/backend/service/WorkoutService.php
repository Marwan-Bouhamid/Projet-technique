<?php

require_once __DIR__ . "/../dao/WorktoutDAO.php";

class controller {
    public WorkoutDao $gestion;

    public function __construct()
    {
        $this->gestion = new WorkoutDao();
    }

    public function listerGroupeMusculaire(){
        $groupes = $this->gestion->afficherTous();
        $data =[];
        foreach($groupes as $groupe){
            $data[]=[
                "id"=>$groupe->getId(),
                "nom"=>$groupe->getNom(),
                "description"=>$groupe->getDescription()
            ];
        }
        return $data;
    }

    public function ajouterGroupeMusculaire(){
        $input= json_decode(file_get_contents("php://input"),true);
        try{
            $this->gestion->ajouter($input["nom"],$input["description"]);
            return ["message"=>"valide","success" => true];
        }catch(Exception $e){
            return ["message"=>$e->getMessage(),"success" => false];
        }
    }
}