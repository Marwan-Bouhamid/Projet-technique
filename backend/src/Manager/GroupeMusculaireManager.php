<?php
declare(strict_types=1);

namespace App\Manager;

use App\Entity\GroupeMusculaire;
use App\Exception\DuplicateNameException;
use App\Exception\NotFoundException;
use RuntimeException;

/**
 * GESTIONNAIRE : seule classe qui connaît le stockage (fichier JSON).
 * Elle manipule des entités, ne produit ni HTTP ni écho.
 * Remplacer le JSON par PDO/MySQL ne demande de modifier que cette classe.
 */
final class GroupeMusculaireManager
{
    public function __construct(private readonly string $file) {}

    /** @return GroupeMusculaire[] */
    public function findAll(): array
    {
        return $this->transaction(false, fn(array $rows) => [
            $rows,
            array_map(fn(array $r) => GroupeMusculaire::fromArray($r), $rows),
        ]);
    }

    public function find(int $id): GroupeMusculaire
    {
        return $this->transaction(false, function (array $rows) use ($id) {
            foreach ($rows as $r) {
                if ((int) $r['id_group'] === $id) {
                    return [$rows, GroupeMusculaire::fromArray($r)];
                }
            }
            throw new NotFoundException("Groupe musculaire #$id introuvable.");
        });
    }

    public function create(GroupeMusculaire $groupe): GroupeMusculaire
    {
        return $this->transaction(true, function (array $rows) use ($groupe) {
            $this->assertNameAvailable($rows, $groupe->getName(), null);
            $id = $rows ? max(array_column($rows, 'id_group')) + 1 : 1;
            $saved = $groupe->withId($id);
            $rows[] = $saved->toArray();
            return [$rows, $saved];
        });
    }

    public function update(GroupeMusculaire $groupe): GroupeMusculaire
    {
        $id = $groupe->getId() ?? 0;
        return $this->transaction(true, function (array $rows) use ($groupe, $id) {
            $this->assertNameAvailable($rows, $groupe->getName(), $id);
            foreach ($rows as $i => $r) {
                if ((int) $r['id_group'] === $id) {
                    $rows[$i] = $groupe->toArray();
                    return [$rows, $groupe];
                }
            }
            throw new NotFoundException("Groupe musculaire #$id introuvable.");
        });
    }

    public function delete(int $id): void
    {
        $this->transaction(true, function (array $rows) use ($id) {
            $kept = array_values(array_filter($rows, fn($r) => (int) $r['id_group'] !== $id));
            if (count($kept) === count($rows)) {
                throw new NotFoundException("Groupe musculaire #$id introuvable.");
            }
            return [$kept, null];
        });
    }

    private function assertNameAvailable(array $rows, string $name, ?int $exceptId): void
    {
        foreach ($rows as $r) {
            if ((int) $r['id_group'] !== $exceptId && mb_strtolower($r['name']) === mb_strtolower($name)) {
                throw new DuplicateNameException("Un groupe nommé « $name » existe déjà.");
            }
        }
    }

    /**
     * Lecture-modification-écriture sous verrou (flock) pour éviter que deux
     * requêtes simultanées s'écrasent. $fn reçoit les lignes et renvoie [lignes, résultat].
     */
    private function transaction(bool $write, callable $fn): mixed
    {
        $h = @fopen($this->file, 'c+');
        if ($h === false) {
            throw new RuntimeException('Stockage inaccessible.');
        }
        try {
            if (!flock($h, $write ? LOCK_EX : LOCK_SH)) {
                throw new RuntimeException('Impossible de verrouiller le stockage.');
            }
            $raw = stream_get_contents($h);
            $rows = trim((string) $raw) === '' ? [] : json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            [$rows, $result] = $fn($rows);
            if ($write) {
                rewind($h);
                ftruncate($h, 0);
                fwrite($h, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
                fflush($h);
            }
            return $result;
        } finally {
            flock($h, LOCK_UN);
            fclose($h);
        }
    }
}
