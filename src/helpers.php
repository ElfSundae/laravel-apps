<?php

if (! function_exists('apps')) {
    /**
     * Obtain the application manager instance.
     *
     * @return \ElfSundae\Laravel\Apps\AppManager
     */
    function apps()
    {
        return app('apps');
    }
}

if (! function_exists('app_id')) {
    /**
     * Get or check the current application identifier.
     *
     * @return string|bool
     */
    function app_id()
    {
        return app('apps')->id(...func_get_args());
    }
}

if (! function_exists('app_url')) {
    /**
     * Generate an absolute URL to a path for the given application identifier.
     *
     * @param  string  $app
     * @param  string  $path
     * @param  mixed  $parameters
     * @return string
     */
    function app_url($app = '', $path = '', $parameters = [])
    {
        return app('apps')->url($app, $path, $parameters);
    }
}
