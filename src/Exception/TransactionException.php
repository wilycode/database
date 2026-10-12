<?php

declare(strict_types=1);

namespace WilyCode\Database\Driver\Exception;

/**
 * Thrown for transaction-related failures (begin/commit/rollback).
 */
class TransactionException extends DatabaseException
{
}
