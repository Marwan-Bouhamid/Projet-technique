<?php
declare(strict_types=1);

/**
 * CONTRÔLEUR (mince) : traduit HTTP <-> Gestionnaire.
 * Pas de règle métier ici (-> Entité), pas d'accès fichier (-> Gestionnaire).
 *
 * GET    groupes.php         liste          | GET groupes.php?id=1  détail
 * POST   groupes.php         création       | PUT groupes.php?id=1  modification
 * DELETE groupes.php?id=1    suppression
 *
 * Paramètres de DÉMO (à retirer en production) :
 *   ?delay=1500 simule la latence (spinners) ; ?fail=1 simule une erreur 500.
 */

require_once __DIR__ . '/../src/bootstrap.php';

use App\Entity\GroupeMusculaire;
use App\Exception\DuplicateNameException;
use App\Exception\NotFoundException;
use App\Manager\GroupeMusculaireManager;

header('Content-Type: application/json; charset=utf-8');

function respond(int $status, bool $success, mixed $data = null, ?string $message = null): never
{
    http_response_code($status);
    echo json_encode(
        ['success' => $success, 'data' => $data, 'message' => $message],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

try {
    $manager = new GroupeMusculaireManager(__DIR__ . '/../database/groupes.json');

    $delay = min(2000, max(0, (int) ($_GET['delay'] ?? 0)));
    if ($delay > 0) {
        usleep($delay * 1000);
    }
    if (($_GET['fail'] ?? '') === '1') {
        throw new RuntimeException('Erreur simulée (démo).');
    }

    $method = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
    $body = [];
    if ($method === 'POST' || $method === 'PUT') {
        $body = json_decode(file_get_contents('php://input') ?: '', true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($body)) {
            respond(400, false, null, 'Corps de requête invalide.');
        }
    }

    switch ($method) {
        case 'GET':
            $data = $id !== null
                ? $manager->find($id)->toArray()
                : array_map(fn(GroupeMusculaire $g) => $g->toArray(), $manager->findAll());
            respond(200, true, $data);

        case 'POST':
            $created = $manager->create(new GroupeMusculaire((string) ($body['name'] ?? ''), (string) ($body['description'] ?? '')));
            respond(201, true, $created->toArray(), 'Groupe musculaire ajouté.');

        case 'PUT':
            if ($id === null) {
                respond(400, false, null, 'Paramètre id manquant.');
            }
            $updated = $manager->update(new GroupeMusculaire((string) ($body['name'] ?? ''), (string) ($body['description'] ?? ''), $id));
            respond(200, true, $updated->toArray(), 'Groupe musculaire modifié.');

        case 'DELETE':
            if ($id === null) {
                respond(400, false, null, 'Paramètre id manquant.');
            }
            $manager->delete($id);
            respond(200, true, null, 'Groupe musculaire supprimé.');

        default:
            header('Allow: GET, POST, PUT, DELETE');
            respond(405, false, null, 'Méthode non autorisée.');
    }
} catch (InvalidArgumentException $e) {
    respond(422, false, null, $e->getMessage());
} catch (NotFoundException $e) {
    respond(404, false, null, $e->getMessage());
} catch (DuplicateNameException $e) {
    respond(409, false, null, $e->getMessage());
} catch (JsonException $e) {
    respond(400, false, null, 'JSON invalide.');
} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(500, false, null, 'Erreur interne du serveur.');
}
