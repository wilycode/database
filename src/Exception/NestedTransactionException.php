<?php

declare(strict_types=1);

namespace WilyCode\Database\Driver\Exception;

/**
 * Thrown when nested transactions are requested but not supported.
 */
final class NestedTransactionException extends TransactionException
{
}
