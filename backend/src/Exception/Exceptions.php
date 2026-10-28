<?php
declare(strict_types=1);

namespace App\Exception;

/** Ressource introuvable (-> HTTP 404). */
final class NotFoundException extends \RuntimeException {}

/** Nom déjà utilisé (-> HTTP 409). */
final class DuplicateNameException extends \RuntimeException {}
