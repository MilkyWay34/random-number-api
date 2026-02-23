<?php

declare(strict_types=1);

namespace App\Kernel\Exception;

use DomainException;

/**
 * Базовое исключение ядра: запрошенный ресурс не найден.
 */
class NotFoundException extends DomainException
{
}
