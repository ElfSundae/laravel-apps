<?php

if (! function_exists('asset')) {
    /**
     * Generate the URL to an application asset.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        return app('apps')->asset($path, $secure);
    }
}
