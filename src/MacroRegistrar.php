<?php

namespace ElfSundae\Apps;

use Illuminate\Contracts\Container\Container;

class MacroRegistrar
{
    /**
     * Register needed macros.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function registerMacros(Container $container)
    {
        $this->register(
            $container['url'],
            'getRootControllerNamespace',
            function () {
                /* @var $this \Illuminate\Routing\UrlGenerator */
                return $this->rootNamespace;
            }
        );

        $this->register(
            $container['router'],
            'hasMiddlewareGroup',
            function ($name) {
                /* @var $this \Illuminate\Routing\Router */
                return array_key_exists($name, $this->middlewareGroups);
            }
        );
    }

    /**
     * Register a macro to the class.
     *
     * @param  string|object  $class
     * @param  string  $method
     * @param  object|callable  $macro
     * @return void
     */
    public function register($class, $method, $macro)
    {
        if (! method_exists($class, $method)) {
            $class = is_object($class) ? get_class($class) : $class;

            call_user_func_array([$class, 'macro'], [$method, $macro]);
        }
    }
}
