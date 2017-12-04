<?php

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
     * @param  string  $appId
     * @param  string  $path
     * @param  mixed  $extra
     * @return string
     */
    function app_url($appId = '', $path = '', $extra = [])
    {
        return app('apps')->url($appId, $path, $extra);
    }
}
