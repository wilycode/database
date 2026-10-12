<?php

declare(strict_types=1);

namespace WilyCode\Database\Driver\Tests\Exception;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WilyCode\Database\Driver\Exception\QueryException;

final class QueryExceptionTest extends TestCase
{
    public function testFromThrowableStoresQueryAndParamKeysOnly(): void
    {
        $previous = new RuntimeException('Low-level driver error');

        $exception = QueryException::fromThrowable(
            $previous,
            'SELECT * FROM users WHERE id = :id AND email = :email',
            [
                ':id' => 123,
                'email' => 'secret@example.com',
                0 => 'positional',
            ],
        );

        self::assertSame('Database query failed.', $exception->getMessage());
        self::assertSame('SELECT * FROM users WHERE id = :id AND email = :email', $exception->query);
        self::assertSame(['paramKeys' => [':id', 'email', 0]], $exception->context);
        self::assertSame($previous, $exception->getPrevious());
        self::assertStringNotContainsString('secret@example.com', json_encode($exception->context, JSON_THROW_ON_ERROR));
    }

    public function testFromThrowableWithNoParametersUsesEmptyKeyList(): void
    {
        $exception = QueryException::fromThrowable(new RuntimeException('Failure'), 'SELECT 1');

        self::assertSame(['paramKeys' => []], $exception->context);
    }

    public function testFromThrowableDoesNotDoubleWrapAQueryException(): void
    {
        $original = new QueryException('Already wrapped', 'SELECT 1', ['safe' => true]);

        self::assertSame($original, QueryException::fromThrowable($original, 'SELECT 2', ['secret' => 'value']));
    }

    public function testConstructorKeepsProvidedFields(): void
    {
        $previous = new RuntimeException('Original failure');
        $exception = new QueryException('Boom', 'SELECT 1', ['foo' => 'bar'], $previous);

        self::assertSame('Boom', $exception->getMessage());
        self::assertSame('SELECT 1', $exception->query);
        self::assertSame(['foo' => 'bar'], $exception->context);
        self::assertSame($previous, $exception->getPrevious());
    }
}
