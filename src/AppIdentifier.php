<?php

namespace ElfSundae\Laravel\Apps;

class AppIdentifier
{
    /**
     * The key registered in the container for the application identifier.
     */
    const IDENTIFIER_KEY = 'apps.identifier';

    /**
     * Get the current application identifier.
     *
     * @return string
     */
    public static function get()
    {
        if (app()->bound(static::IDENTIFIER_KEY)) {
            return app()->make(static::IDENTIFIER_KEY);
        }

        return app()->instance(
            static::IDENTIFIER_KEY,
            static::getIdentifierForUrl(app('request')->getUri())
        );
    }

    /**
     * Get application identifier for the given URL.
     *
     * @param  string  $url
     * @return string
     */
    protected static function getIdentifierForUrl($url)
    {
        $identifier = null;

        foreach (config('apps.url') as $id => $root) {
            $root = preg_replace('#^https?://#', '', $root);
            $pattern = '#^https?://'.preg_quote($root, '#').'([\?/].*)?$#';
            if (preg_match($pattern, $url)) {
                $len = strlen($root);
                if (! isset($length) || $length < $len) {
                    $length = $len;
                    $identifier = $id;
                }
            }
        }

        return $identifier;
    }

    /**
     * Refresh the current application identifier.
     *
     * @return void
     */
    public static function refresh()
    {
        app()->forgetInstance(static::IDENTIFIER_KEY);
    }
}
