<?php

declare(strict_types=1);

namespace JustSteveKing\Support\Macroable;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;

trait Macroable
{
    /**
     * An array of stored mixins
     *
     * @var array
     */
    protected static array $stored = [];

    /**
     * Return an array of all public or protected methods on a given class
     *
     * @param object $mixin
     * @return array
     * @throws ReflectionException
     */
    public static function getMethods(object $mixin): array
    {
        return (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );
    }

    /**
     * Check to see if a Macro has been registered
     *
     * @param string $name
     * @return bool
     */
    public static function macroRegistered(string $name): bool
    {
        return isset(static::$stored[$name]);
    }

    /**
     * Register a new macro
     *
     * @param string $name
     * @param object|callable $macro
     */
    public static function registerMacro(string $name, $macro): void
    {
        static::$stored[$name] = $macro;
    }

    /**
     * Extend a class by allowing to to invoke another class that has been mixed in
     *
     * @param object $mixin
     */
    public static function mixin(object $mixin): void
    {
        $methods = static::getMethods($mixin);

        foreach ($methods as $method) {
//            $method->setAccessible(true);

            static::registerMacro($method->getName(), $method->invoke($mixin));
        }
    }

    /**
     * Forward a static method call through to a registered macro
     *
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws ReflectionException
     */
    public static function __callStatic($method, $arguments)
    {
        if (! static::macroRegistered($method)) {
            throw new ReflectionException(
                "Method {$method} does not exist on this class."
            );
        }

        $macro = static::$stored[$method];

        if ($macro instanceof Closure) {
            return call_user_func_array(
                Closure::bind(
                    $macro,
                    null,
                    static::class
                ), $arguments);
        }

        return call_user_func_array($macro, $arguments);
    }

    /**
     * Forward a method call through to a registered macro
     *
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws ReflectionException
     */
    public function __call($method, $parameters)
    {
        if (! static::macroRegistered($method)) {
            throw new ReflectionException("Method {$method} does not exist.");
        }

        $macro = static::$stored[$method];

        if ($macro instanceof Closure) {
            return call_user_func_array(
                $macro->bindTo(
                    $this,
                    static::class
                ), $parameters);
        }

        return call_user_func_array($macro, $parameters);
    }
}
