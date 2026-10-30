<?php

require_once "../classes/GestionGroupeMusculaire.php";

class controller {
    public GestionGroupeMusculaire $gestion;

    public function __construct()
    {
        $this->gestion = new GestionGroupeMusculaire();
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
        $this->gestion->ajouter($input["nom"],$input["description"]);
        return ["message"=>"ajouter succes"];
    }
}