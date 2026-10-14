# WilyCode Database

[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-777BB4)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Tests](https://github.com/wilycode/database/actions/workflows/tests.yml/badge.svg)](https://github.com/wilycode/database/actions/workflows/tests.yml)

**A lightweight, database-agnostic contract for queries, connections, and transactions in PHP 8.4+.**

Use the same `DatabaseInterface` with different database adapters. This package provides **interfaces and exceptions only**: it does not open connections or include PDO, MySQLi, an ORM, or a query builder.

> **WilyCode PHP Ecosystem**
>
> Part of **[WilyCode](https://github.com/wilycode/wilycode)** — a collection of independent, reusable PHP components. Each package has its own repository, releases, and Composer installation.
>
> **[Explore the WilyCode ecosystem →](https://github.com/wilycode/wilycode)**

## Features

- One `DatabaseInterface` for query execution, fetching, and transactions.
- Named or positional query parameters, with optional adapter-specific parameter types.
- Dedicated connection, query, and transaction exceptions.
- A `QueryException` helper that keeps parameter **keys**, not parameter values, in its context.
- Strict direct-driver transaction semantics: nested transactions are rejected.
- No runtime dependencies other than PHP 8.4+.

## Requirements

- PHP 8.4 or newer.
- A separate database adapter that implements `DatabaseInterface` when connecting to a real database.

## Installation

```bash
composer require wilycode/database
```

## Quick start

Depend on the interface rather than on a specific driver:

```php
<?php

declare(strict_types=1);

use WilyCode\Database\Driver\DatabaseInterface;

function findUser(DatabaseInterface $database, int $id): array
{
    return $database->fetch(
        'SELECT id, name FROM users WHERE id = :id',
        ['id' => $id],
    );
}

// Pass a DatabaseInterface implementation supplied by your application.
// Returns an associative row, or [] when the query finds no matching row.
```

The package itself cannot be instantiated to make a database connection; install or implement a separate driver.

## DatabaseInterface

| Method | Purpose | Return value |
| --- | --- | --- |
| `execute($query, $params = [], $types = [])` | Execute a write or DDL statement | Number of affected rows (`int`) |
| `fetch($query, $params = [], $types = [])` | Fetch the first matching row | Associative array; `[]` if none |
| `fetchAll($query, $params = [], $types = [])` | Fetch all matching rows | List of associative arrays |
| `fetchColumn($query, $params = [], $columnIndex = 0, $types = [])` | Fetch one column from the first row | Column value, or `null` if no row |
| `getLastInsertId()` | Obtain the last insert identifier | `string` |
| `transaction($callback)` | Run a callback within a new transaction | Callback's return value |
| `inTransaction()` | Check whether a transaction is active | `bool` |

Parameters may be named or positional. The interpretation of `$types` depends on the chosen adapter; this contract does not define a shared type system.

### Query examples

```php
use WilyCode\Database\Driver\DatabaseInterface;

function createUser(DatabaseInterface $db, string $email): string
{
    $db->execute(
        'INSERT INTO users (email) VALUES (:email)',
        ['email' => $email],
    );

    return $db->getLastInsertId(); // Always a string.
}

function listUsers(DatabaseInterface $db): array
{
    return $db->fetchAll('SELECT id, email FROM users');
}
```

An empty result from `fetch()` is `[]`; `fetchColumn()` returns `null` when there is no row. A database column containing SQL `NULL` can also produce a `null` column value.

## Transactions

`transaction()` supplies the database driver to the callback and returns the callback result. The driver is responsible for committing on success and rolling back on failure.

```php
use WilyCode\Database\Driver\DatabaseInterface;

function transfer(DatabaseInterface $db, int $fromId, int $toId, int $amount): int
{
    return $db->transaction(
        static function (DatabaseInterface $tx) use ($fromId, $toId, $amount): int {
            $tx->execute(
                'UPDATE wallets SET balance = balance - :amount WHERE id = :id',
                ['amount' => $amount, 'id' => $fromId],
            );
            $tx->execute(
                'UPDATE wallets SET balance = balance + :amount WHERE id = :id',
                ['amount' => $amount, 'id' => $toId],
            );

            return 2;
        },
    );
}
```

Direct driver transactions are **not nested**: implementations should throw `NestedTransactionException` if a transaction is already active. Higher-level executors may offer different transaction semantics, but those are outside this package.

## Exceptions

All database exceptions extend `DatabaseException`, which extends PHP's `RuntimeException`.

| Exception | Meaning |
| --- | --- |
| `DatabaseException` | Common base exception |
| `ConnectionException` | A connection or driver could not be initialized |
| `QueryException` | Preparing, executing, or fetching a query failed |
| `TransactionException` | A transaction operation failed |
| `NestedTransactionException` | A direct driver transaction was nested; extends `TransactionException` |

### Wrapping a driver error

Adapter implementations can use `QueryException::fromThrowable()`:

```php
use WilyCode\Database\Driver\Exception\QueryException;

try {
    // Run a query using your chosen low-level database driver.
} catch (\Throwable $exception) {
    throw QueryException::fromThrowable(
        $exception,
        'SELECT * FROM users WHERE id = :id',
        ['id' => 42],
    );
}
```

The resulting `QueryException` keeps the SQL query, the original exception as `getPrevious()`, and `context['paramKeys']` (for example `['id']`). It does **not** add bound parameter values to its context. **SQL text and original exception messages can still be sensitive**: use placeholders and avoid logging raw exceptions or queries containing secrets.

## Related packages

| Package | Purpose |
| --- | --- |
| [wilycode/database-pdo](https://github.com/wilycode/database-pdo) | PDO-based implementation of the driver contract |
| [wilycode/database-mysqli](https://github.com/wilycode/database-mysqli) | MySQLi-based implementation of the driver contract |

These adapter packages are developed and versioned separately.

## Development

```bash
composer install
composer validate --strict
composer test
```

GitHub Actions runs the test suite on pushes and pull requests. Tests are kept in the Git repository and excluded from distribution archives.

## Contributing

Issues and pull requests are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md) and the [issue tracker](https://github.com/wilycode/database/issues).

## License

MIT © 2026 Simon Piter. See [LICENSE](LICENSE).
