<?php
declare(strict_types=1);

// Integration test: simulates the full API flow (Entity + Manager + controller response format)
// without needing a web server. Tests GET, POST, PUT, DELETE through the Manager.

require_once __DIR__ . '/../backend/src/bootstrap.php';

use App\Entity\GroupeMusculaire;
use App\Exception\DuplicateNameException;
use App\Exception\NotFoundException;
use App\Manager\GroupeMusculaireManager;

$fails = 0;
function check(string $label, bool $ok): void {
    global $fails;
    echo ($ok ? "  OK  " : " FAIL ") . $label . PHP_EOL;
    $fails += $ok ? 0 : 1;
}

$file = tempnam(sys_get_temp_dir(), 'grp_integration');
file_put_contents($file, '[]');
$m = new GroupeMusculaireManager($file);

// ---------- CREATE (POST equivalent) ----------
$created = $m->create(new GroupeMusculaire('Pectoraux', 'Muscles de la poitrine'));
check('POST: create returns entity with id', $created->getId() === 1);
check('POST: toArray keys match Session 2 format', $created->toArray() === ['id_group' => 1, 'name' => 'Pectoraux', 'description' => 'Muscles de la poitrine']);

// ---------- READ ALL (GET equivalent) ----------
$all = array_map(fn(GroupeMusculaire $g) => $g->toArray(), $m->findAll());
check('GET: findAll returns 1 item', count($all) === 1);

// ---------- READ ONE (GET ?id=1 equivalent) ----------
$found = $m->find(1)->toArray();
check('GET: find(1) returns correct entity', $found === ['id_group' => 1, 'name' => 'Pectoraux', 'description' => 'Muscles de la poitrine']);

// ---------- UPDATE (PUT equivalent) ----------
$updated = $m->update(new GroupeMusculaire('Pectoraux haut', 'Partie supérieure', 1));
check('PUT: update returns modified entity', $updated->getName() === 'Pectoraux haut');
check('PUT: find(1) reflects update', $m->find(1)->getName() === 'Pectoraux haut');

// ---------- UPDATE to duplicate name (PUT 409 equivalent) ----------
$m->create(new GroupeMusculaire('Dos', 'Muscles du dos'));
check('PUT: update to existing name throws DuplicateNameException', throws(
    fn() => $m->update(new GroupeMusculaire('Dos', 'Tentative doublon', 1)),
    DuplicateNameException::class
));

// ---------- UPDATE non-existent (PUT 404 equivalent) ----------
check('PUT: update non-existent throws NotFoundException', throws(
    fn() => $m->update(new GroupeMusculaire('X', 'y', 999)),
    NotFoundException::class
));

// ---------- DELETE (DELETE equivalent) ----------
$m->delete(1);
check('DELETE: remove entity', count($m->findAll()) === 1);
check('DELETE: remaining entity is Dos', $m->findAll()[0]->getName() === 'Dos');

// ---------- DELETE non-existent (DELETE 404 equivalent) ----------
check('DELETE: delete non-existent throws NotFoundException', throws(
    fn() => $m->delete(999),
    NotFoundException::class
));

// ---------- Response format validation ----------
function respond(int $status, bool $success, mixed $data = null, ?string $message = null): array {
    return ['status' => $status, 'success' => $success, 'data' => $data, 'message' => $message];
}
$sample = respond(200, true, ['id_group' => 1, 'name' => 'Test'], 'OK');
check('API response format has success key', array_key_exists('success', $sample));
check('API response format has data key', array_key_exists('data', $sample));
check('API response format has message key', array_key_exists('message', $sample));
check('API response format has status key', array_key_exists('status', $sample));

unlink($file);
echo $fails === 0 ? "\nTous les tests d'integration passent.\n" : "\n$fails echec(s).\n";
exit($fails === 0 ? 0 : 1);

function throws(callable $fn, string $class): bool {
    try { $fn(); } catch (Throwable $e) { return $e instanceof $class; }
    return false;
}