<?php

declare(strict_types=1);

namespace WilyCode\Database\Driver;

/** Optional capability for executors; names follow PDO driver names. */
interface PlatformProviderInterface
{
    public function getPlatformName(): string;
}
