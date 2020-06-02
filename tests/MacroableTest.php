<?php

declare(strict_types=1);

namespace JustSteveKing\Tests\Support\Macroable;

use PHPUnit\Framework\TestCase;
use JustSteveKing\Tests\Support\Macroable\Stubs\Example;
use JustSteveKing\Tests\Support\Macroable\Stubs\Mixable;
use ReflectionException;

class MacroableTest extends TestCase
{
    protected object $class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->class =  new Example();
    }

    public function testClassIsTheCorrectInstance()
    {
        $this->assertInstanceOf(
            Example::class,
            $this->class
        );
    }

    public function testANewMethodCanBeAddedToAClassAndCalled()
    {
        $this->class::registerMacro('example', fn() => 'example');

        $this->assertEquals(
            'example',
            $this->class->example()
        );
    }

    public function testANewMethodCanBeAddedAndCalledStatically()
    {
        $this->class::registerMacro('static', fn() => 'static');

        $this->assertEquals(
            'static',
            $this->class::static()
        );
    }

    public function testAClassCanBeRegisteredAsAMacroAnInvokedWhenCalled()
    {
        $this->class::registerMacro('invokeMe', new class () {
            public function __invoke(): string
            {
                return 'invokeMe';
            }
        });

        $this->assertEquals(
            'invokeMe',
            $this->class->invokeMe()
        );

        $this->assertEquals(
            'invokeMe',
            $this->class::invokeMe()
        );
    }

    public function testItAcceptsAndPassesParametersAsExpected()
    {
        $this->class::registerMacro('parameters', fn(...$strings) => implode('&', $strings));

        $this->assertEquals(
            'a&b&c',
            $this->class->parameters('a', 'b', 'c')
        );
    }

    public function testAnyMethodsAreBoundToTheClass()
    {
        $this->class::registerMacro('getPrivateVariable', fn() => $this->privateVariable);

        $this->assertEquals(
            'privateValue',
            $this->class->getPrivateVariable()
        );
    }

    public function testAMacroCanBeAddedToCallPrivateMethods()
    {
        $this->class::registerMacro('callPrivateMethod', fn() => $this::getPrivateStatic());

        $this->assertEquals(
            'privateStaticValue',
            $this->class->callPrivateMethod()
        );
    }

    public function testPublicMethodsFromAnotherClassAreMerged()
    {
        $this->class::mixin(new Mixable());

        $this->assertEquals(
            'privateValue-test',
            $this->class->mixinMethodA('test')
        );
    }

    public function testAnExceptionIsThrownIfTheMethodDoesNotExist()
    {
        $this->expectException(ReflectionException::class);

        $this->class->throwException();
    }

    public function testAnExceptionIsThrownIfAStaticMethodDoesNotExist()
    {
        $this->expectException(ReflectionException::class);

        $this->class::staticThrowingAnException();
    }
}
