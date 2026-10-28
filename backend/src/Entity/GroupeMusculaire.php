<?php
declare(strict_types=1);

namespace App\Entity;

use InvalidArgumentException;

/**
 * ENTITÉ : représente un groupe musculaire et protège ses propres règles
 * métier (validation). Elle ne sait RIEN du stockage, de HTTP ni du JSON.
 */
final class GroupeMusculaire
{
    public const NAME_MAX = 50;
    public const DESCRIPTION_MAX = 255;

    private ?int $id;
    private string $name = '';
    private string $description = '';

    public function __construct(string $name, string $description, ?int $id = null)
    {
        $this->setName($name);
        $this->setDescription($description);
        $this->id = $id;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }

    public function setName(string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('Le nom est obligatoire.');
        }
        if (mb_strlen($name) > self::NAME_MAX) {
            throw new InvalidArgumentException('Le nom ne doit pas dépasser ' . self::NAME_MAX . ' caractères.');
        }
        $this->name = $name;
    }

    public function setDescription(string $description): void
    {
        $description = trim($description);
        if ($description === '') {
            throw new InvalidArgumentException('La description est obligatoire.');
        }
        if (mb_strlen($description) > self::DESCRIPTION_MAX) {
            throw new InvalidArgumentException('La description ne doit pas dépasser ' . self::DESCRIPTION_MAX . ' caractères.');
        }
        $this->description = $description;
    }

    public function withId(int $id): self
    {
        $copy = clone $this;
        $copy->id = $id;
        return $copy;
    }

    /** Format de sérialisation (mêmes clés que la Session 2). */
    public function toArray(): array
    {
        return ['id_group' => $this->id, 'name' => $this->name, 'description' => $this->description];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['name'] ?? ''),
            (string) ($data['description'] ?? ''),
            isset($data['id_group']) ? (int) $data['id_group'] : null
        );
    }
}
