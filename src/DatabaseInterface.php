<?php

declare(strict_types=1);

namespace WilyCode\Database\Driver;

use WilyCode\Database\Driver\Exception\DatabaseException;
use WilyCode\Database\Driver\Exception\NestedTransactionException;
use WilyCode\Database\Driver\Exception\QueryException;

interface DatabaseInterface
{
    /**
     * Executes a write or DDL statement and returns the number of affected rows.
     *
     * @param array<int|string, mixed> $params
     * @param array<int|string, mixed> $types
     * @throws QueryException
     * @throws DatabaseException
     */
    public function execute(string $query, array $params = [], array $types = []): int;

    /**
     * Fetches one row or returns an empty array when no row exists.
     *
     * @param array<int|string, mixed> $params
     * @param array<int|string, mixed> $types
     * @return array<string, mixed>
     * @throws QueryException
     * @throws DatabaseException
     */
    public function fetch(string $query, array $params = [], array $types = []): array;

    /**
     * @param array<int|string, mixed> $params
     * @param array<int|string, mixed> $types
     * @return list<array<string, mixed>>
     * @throws QueryException
     * @throws DatabaseException
     */
    public function fetchAll(string $query, array $params = [], array $types = []): array;

    /**
     * Returns null when no row exists.
     *
     * @param array<int|string, mixed> $params
     * @param array<int|string, mixed> $types
     * @throws QueryException
     * @throws DatabaseException
     */
    public function fetchColumn(string $query, array $params = [], int $columnIndex = 0, array $types = []): mixed;

    /** @throws DatabaseException */
    public function getLastInsertId(): string;

    /**
     * Runs a callback in a new transaction.
     *
     * Direct driver transactions are strict: nesting is rejected. Higher-level
     * executors may choose to join an already active transaction.
     *
     * @template T
     * @param callable(self):T $callback
     * @return T
     * @throws NestedTransactionException
     * @throws DatabaseException
     * @throws \Throwable
     */
    public function transaction(callable $callback): mixed;

    public function inTransaction(): bool;
}
