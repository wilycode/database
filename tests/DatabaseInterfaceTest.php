<?php

declare(strict_types=1);

namespace WilyCode\Database\Driver\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use WilyCode\Database\Driver\DatabaseInterface;

final class DatabaseInterfaceTest extends TestCase
{
    public function testContractIsAnInterfaceWithExactlySevenMethods(): void
    {
        $contract = new ReflectionClass(DatabaseInterface::class);

        self::assertTrue($contract->isInterface());
        self::assertSame(
            ['execute', 'fetch', 'fetchAll', 'fetchColumn', 'getLastInsertId', 'transaction', 'inTransaction'],
            array_map(static fn (ReflectionMethod $method): string => $method->getName(), $contract->getMethods()),
        );
    }

    public function testQueryMethodSignaturesAndDefaults(): void
    {
        foreach (['execute' => 'int', 'fetch' => 'array', 'fetchAll' => 'array'] as $name => $returnType) {
            $method = new ReflectionMethod(DatabaseInterface::class, $name);
            self::assertSame($returnType, (string) $method->getReturnType());
            self::assertSame(['query', 'params', 'types'], array_map(
                static fn ($parameter): string => $parameter->getName(),
                $method->getParameters(),
            ));
            self::assertSame([], $method->getParameters()[1]->getDefaultValue());
            self::assertSame([], $method->getParameters()[2]->getDefaultValue());
        }

        $column = new ReflectionMethod(DatabaseInterface::class, 'fetchColumn');
        self::assertSame('mixed', (string) $column->getReturnType());
        self::assertSame(['query', 'params', 'columnIndex', 'types'], array_map(
            static fn ($parameter): string => $parameter->getName(),
            $column->getParameters(),
        ));
        self::assertSame(0, $column->getParameters()[2]->getDefaultValue());
    }

    public function testTransactionAndStatusSignatures(): void
    {
        $transaction = new ReflectionMethod(DatabaseInterface::class, 'transaction');
        self::assertSame('callable', (string) $transaction->getParameters()[0]->getType());
        self::assertSame('mixed', (string) $transaction->getReturnType());
        self::assertSame('bool', (string) (new ReflectionMethod(DatabaseInterface::class, 'inTransaction'))->getReturnType());
        self::assertSame('string', (string) (new ReflectionMethod(DatabaseInterface::class, 'getLastInsertId'))->getReturnType());
    }
}
