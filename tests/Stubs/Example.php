<?php

declare(strict_types=1);

namespace JustSteveKing\Tests\Support\Macroable\Stubs;

use JustSteveKing\Support\Macroable\Macroable;

class Example
{
    use Macroable;

    /**
     * @var string
     */
    private string $privateVariable = 'privateValue';

    /**
     * @return string
     */
    private static function getPrivateStatic(): string
    {
        return 'privateStaticValue';
    }
}
