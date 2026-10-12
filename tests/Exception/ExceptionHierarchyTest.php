<?php

declare(strict_types=1);

namespace WilyCode\Database\Driver\Tests\Exception;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use WilyCode\Database\Driver\Exception\ConnectionException;
use WilyCode\Database\Driver\Exception\DatabaseException;
use WilyCode\Database\Driver\Exception\NestedTransactionException;
use WilyCode\Database\Driver\Exception\QueryException;
use WilyCode\Database\Driver\Exception\TransactionException;

final class ExceptionHierarchyTest extends TestCase
{
    public function testDatabaseExceptionExtendsRuntimeException(): void
    {
        self::assertTrue(is_subclass_of(DatabaseException::class, RuntimeException::class));
    }

    public function testSpecificExceptionsExtendExpectedBases(): void
    {
        self::assertTrue(is_subclass_of(ConnectionException::class, DatabaseException::class));
        self::assertTrue(is_subclass_of(QueryException::class, DatabaseException::class));
        self::assertTrue(is_subclass_of(TransactionException::class, DatabaseException::class));
        self::assertTrue(is_subclass_of(NestedTransactionException::class, TransactionException::class));
    }

    public function testBaseExceptionsCanBeExtendedAndConcreteExceptionsAreFinal(): void
    {
        self::assertFalse((new ReflectionClass(DatabaseException::class))->isFinal());
        self::assertFalse((new ReflectionClass(TransactionException::class))->isFinal());

        foreach ([ConnectionException::class, QueryException::class, NestedTransactionException::class] as $class) {
            self::assertTrue((new ReflectionClass($class))->isFinal());
        }
    }
}
