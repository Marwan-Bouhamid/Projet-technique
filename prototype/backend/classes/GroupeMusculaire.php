<?php
    class GroupeMusculaire{
        private int $id;
        private string $nom;
        private string $description;

        public function __construct($id,$nom,$description){
            $this->id=$id;
            $this->nom=$nom;
            $this->description=$description;
        }

        public function getId(){
            return $this->id;
        }
        public function getNom(){
            return $this->nom;
        }
        public function getDescription(){
            return $this->description;
        }
    }
?>
