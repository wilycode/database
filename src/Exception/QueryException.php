<?php

declare(strict_types=1);

namespace WilyCode\Database\Driver\Exception;

use Throwable;

/**
 * Thrown when a query (prepare/execute/fetch) fails.
 *
 * Note: by default we store only parameter *keys* (not values) to avoid leaking secrets.
 */
final class QueryException extends DatabaseException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        string $message,
        public readonly ?string $query = null,
        public readonly array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * @param array<int|string, mixed> $params
     */
    public static function fromThrowable(Throwable $e, string $query, array $params = []): self
    {
        if ($e instanceof self) {
            return $e;
        }

        $context = [
            'paramKeys' => array_map(
                static fn ($k) => is_int($k) ? $k : (string) $k,
                array_keys($params)
            ),
        ];

        return new self('Database query failed.', $query, $context, $e);
    }
}
