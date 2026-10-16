<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../class/groupeMusculaire.php";

class GestionGroupeMusculaire
{
    private $path_file;

    public function __construct()
    {
        $this->path_file = __DIR__ . "/../../database/gestionGroupeMusculaire.json";
    }

    public function getGroupesMusculaires()
    {
        $contenu = file_get_contents($this->path_file);

        $groupes = json_decode($contenu, true);

        echo json_encode($groupes);
    }

    public function ajouterGroupeMusculaire()
    {
        $contenu = file_get_contents($this->path_file);

        $groupes = json_decode($contenu, true);

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        $name = trim($data["name"] ?? "");
        $description = trim($data["description"] ?? "");

        $groupe = new groupeMusculaire($name, $description);

        $groupes[] = [
            "id_group" => empty($groupes) ? 1 : max(array_column($groupes, "id_group")) + 1,
            "name" => $groupe->getName(),
            "description" => $groupe->getDescription()
        ];

        file_put_contents(
            $this->path_file,
            json_encode($groupes, JSON_PRETTY_PRINT)
        );

        echo json_encode($groupes);
    }

    public function supprimerGroupeMusculaire()
    {
        $contenu = file_get_contents($this->path_file);

        $groupes = json_decode($contenu, true);

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        $id_group = (int) ($data["id_group"] ?? 0);

        $groupes = array_values(array_filter(
            $groupes,
            function ($element) use ($id_group) {
                return $element["id_group"] !== $id_group;
            }
        ));

        file_put_contents(
            $this->path_file,
            json_encode($groupes, JSON_PRETTY_PRINT)
        );

        echo json_encode($groupes);
    }

    public function traiterRequete()
    {
        $method = $_SERVER["REQUEST_METHOD"];

        if ($method === "GET") {
            $this->getGroupesMusculaires();
        }

        if ($method === "POST") {
            $this->ajouterGroupeMusculaire();
        }

        if ($method === "DELETE") {
            $this->supprimerGroupeMusculaire();
        }
    }




}


$api = new GestionGroupeMusculaire();
$api->traiterRequete();
