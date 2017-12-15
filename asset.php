<?php

if (! function_exists('asset')) {
    /**
     * Generate the URL to an application asset.
     *
     * @param  string  $path
     * @param  bool    $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        return app('url')->assetFrom(app('apps')->root('assets'), $path, $secure);
    }
}
