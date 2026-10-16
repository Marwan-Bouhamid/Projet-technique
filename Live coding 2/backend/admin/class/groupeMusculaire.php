<?php
    class groupeMusculaire {
        private int $id_group;
        private string $name;
        private string $description;

        public function __construct(string $name="",string $description=""){
            $this->name=$name;
            $this->description=$description;
        }

        public function getName():string {
            return $this->name;
        }

        public function getDescription():string {
            return $this->description;
        }

        public function setName(string $name) : void {
            if (!empty($name)) {
                $this->name=$name;
            }
        }

        public function setDescription(string $description) : void {
            if (!empty($description)){
                $this->description=$description;
            }
        }
    }


?>
