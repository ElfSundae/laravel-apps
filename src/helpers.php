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
        $currentUrl = app('request')->getUri();

        foreach ($identifiers as $identifier) {
            if ($url = config("apps.url.$identifier")) {
                $url = preg_replace('#^https?://#', '', $url);
                $pattern = '#^https?://'.preg_quote($url, '#').'([\?/].*)?$#';
                if (preg_match($pattern, $currentUrl)) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (! function_exists('app_url')) {
    /**
     * Generate an absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $query
     * @param  mixed  $identifier
     * @return string
     */
    function app_url($path = '', $query = [], $identifier = '')
    {
        if (is_string($query)) {
            list($query, $identifier) = [$identifier, $query];
        }

        if ($path = ltrim($path, '/')) {
            $path = '/'.$path;
        }

        if ($query && $query = http_build_query($query)) {
            $path .= (str_contains($path, ['?', '&', '#']) ? '&' : '?').$query;
        }

        return config("apps.url.$identifier", config('app.url')).$path;
    }
}
