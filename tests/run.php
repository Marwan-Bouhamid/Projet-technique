<?php
declare(strict_types=1);
// Usage : php tests/run.php
require_once __DIR__ . '/../backend/src/bootstrap.php';

use App\Entity\GroupeMusculaire;
use App\Exception\{DuplicateNameException, NotFoundException};
use App\Manager\GroupeMusculaireManager;

$fails = 0;
function check(string $label, bool $ok): void { global $fails; echo ($ok ? "  OK  " : " FAIL ") . $label . PHP_EOL; $fails += $ok ? 0 : 1; }
function throws(callable $fn, string $class): bool { try { $fn(); } catch (Throwable $e) { return $e instanceof $class; } return false; }

$file = tempnam(sys_get_temp_dir(), 'grp');
file_put_contents($file, '[]');
$m = new GroupeMusculaireManager($file);

check('entité : nom vide refusé', throws(fn() => new GroupeMusculaire('  ', 'x'), InvalidArgumentException::class));
check('entité : description trop longue refusée', throws(fn() => new GroupeMusculaire('a', str_repeat('x', 256)), InvalidArgumentException::class));
check('entité : trim appliqué', (new GroupeMusculaire('  Dos ', ' d '))->getName() === 'Dos');

$a = $m->create(new GroupeMusculaire('Dos', 'Muscles du dos'));
$b = $m->create(new GroupeMusculaire('Jambes', 'Membres inférieurs'));
check('manager : ids auto-incrémentés (1, 2)', $a->getId() === 1 && $b->getId() === 2);
check('manager : findAll = 2', count($m->findAll()) === 2);
check('manager : doublon (insensible à la casse) -> 409', throws(fn() => $m->create(new GroupeMusculaire('DOS', 'x')), DuplicateNameException::class));
$m->update(new GroupeMusculaire('Dos large', 'MAJ', 1));
check('manager : update', $m->find(1)->getName() === 'Dos large');
check('manager : update inexistant -> 404', throws(fn() => $m->update(new GroupeMusculaire('Z', 'z', 99)), NotFoundException::class));
$m->delete(1);
check('manager : delete', count($m->findAll()) === 1);
check('manager : delete inexistant -> 404', throws(fn() => $m->delete(1), NotFoundException::class));
check('manager : nouvel id = max+1 (3)', $m->create(new GroupeMusculaire('Core', 'Abdos'))->getId() === 3);

unlink($file);
echo $fails === 0 ? "\nTous les tests passent.\n" : "\n$fails échec(s).\n";
exit($fails === 0 ? 0 : 1);
