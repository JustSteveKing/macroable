<?php

declare(strict_types=1);

namespace JustSteveKing\Tests\Support\Macroable\Stubs;

class Mixable
{
    private function secret(): string
    {
        return 'secret';
    }

    public function mixinMethodA()
    {
        return function ($value) {
            return $this->mixinMethodB($value);
        };
    }

    public function mixinMethodB()
    {
        return function ($value) {
            return $this->privateVariable . '-' . $value;
        };
    }
}
