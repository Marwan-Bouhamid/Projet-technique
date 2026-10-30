<?php
    require_once "GroupeMusculaire.php";
    class GestionGroupeMusculaire{
        private static $dataFile=__DIR__ . "/../data/groupes_musculaires.json";

        public function AfficherTous(){
            $data=[];

            $json = file_get_contents(self::$dataFile);
            $groupes = json_decode($json, true);

            foreach($groupes as $groupe){
                $g=new GroupeMusculaire($groupe["id"] ?? 0, $groupe["nom"] ?? '', $groupe["description"] ?? '');
                $data[]=$g;
            }
            return $data;
        }

        public function ajouter($nom,$description){
            $data=[];
            $groupes=$this->AfficherTous();
            $id = 1;
            if (count($groupes) > 0) {
                $last = $groupes[count($groupes) - 1];
                $id = $last->getId() + 1;
            }
            $groupes[]=new GroupeMusculaire($id,$nom,$description);
            foreach ($groupes as $groupe){
                $data[]=[
                    "id"=>$groupe->getId(),
                    "nom"=>$groupe->getNom(),
                    "description"=>$groupe->getDescription(),
                ];
            }

            file_put_contents(self::$dataFile,json_encode($data,JSON_PRETTY_PRINT));
        }
    }
?>
