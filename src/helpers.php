<?php

if (! function_exists('is_app')) {
    /**
     * Determine if a given identifier matches the current application identifier.
     *
     * @param  string  $identifiers
     * @return bool
     */
    function is_app(...$identifiers)
    {
        $request = app('request');
        $currentUrl = $request->getHttpHost().$request->getBaseUrl().$request->getPathInfo();

        foreach ($identifiers as $identifier) {
            if ($url = config('apps.url.'.$identifier)) {
                $url = preg_replace('#^https?://#', '', $url, 1);
                $pattern = '#^'.preg_quote($url, '#').'([\?/].*)?$#';
                if (preg_match($pattern, $currentUrl) === 1) {
                    return true;
                }
            }
        }

        return false;
    }
}
