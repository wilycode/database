<?php

declare(strict_types=1);

namespace WilyCode\Database\Driver\Exception;

use RuntimeException;

/**
 * Base exception for all WilyCode database drivers.
 *
 * Implementations SHOULD NOT leak driver-specific exceptions (e.g. PDOException).
 */
class DatabaseException extends RuntimeException
{
}
