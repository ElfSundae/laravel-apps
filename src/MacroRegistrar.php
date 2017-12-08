<?php

namespace ElfSundae\Laravel\Apps;

class MacroRegistrar
{
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
