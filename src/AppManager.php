<?php

namespace ElfSundae\Laravel\Apps;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Container\Container;

class AppManager
{
    use Macroable;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The current application identifier.
     *
     * @var string|false
     */
    protected $id = false;

    /**
     * Create a new app manager instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->container->rebinding('request', function () {
            $this->refreshId();
        });
    }

    /**
     * Get all application URLs.
     *
     * @return array
     */
    public function appUrls()
    {
        return $this->container['config']->get('apps.url', []);
    }

    /**
     * Get the root URL for the given application.
     *
     * @param  string  $app
     * @return string
     */
    public function appUrl($app = '')
    {
        return Arr::get($this->appUrls(), (string) $app)
            ?: $this->container['config']['app.url'];
    }

    /**
     * Get the root URL for the given application.
     *
     * @param  string  $app
     * @return string
     */
    public function root($app = '')
    {
        return $this->appUrl($app);
    }

    /**
     * Get the URL domain for the given application.
     *
     * @param  string  $app
     * @return string
     */
    public function domain($app = '')
    {
        return parse_url($this->appUrl($app), PHP_URL_HOST);
    }

    /**
     * Get the URL prefix for the given application.
     *
     * @param  string  $app
     * @return string
     */
    public function prefix($app = '')
    {
        return trim(parse_url($this->appUrl($app), PHP_URL_PATH), '/');
    }

    /**
     * Get or check the current application identifier.
     *
     * @return string|bool
     */
    public function id()
    {
        if ($this->id === false) {
            $this->id = $this->idForUrl($this->container['request']->getUri());
        }

        if (func_num_args() > 0) {
            return in_array($this->id, is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args());
        }

        return $this->id;
    }

    /**
     * Refresh the current application identifier.
     *
     * @return $this
     */
    public function refreshId()
    {
        $this->id = false;

        return $this;
    }

    /**
     * Get the application identifier for the given URL.
     *
     * @param  string  $url
     * @return string
     */
    public function idForUrl($url)
    {
        return collect($this->appUrls())
            ->filter(function ($root) use ($url) {
                return $this->urlHasRoot($url, $root);
            })
            ->sortByDesc(function ($root) {
                return strlen($root);
            })
            ->keys()
            ->first();
    }

    /**
     * Determine if an URL has the given root URL.
     *
     * @param  string  $url
     * @param  string  $root
     * @param  bool  $strict
     * @return bool
     */
    protected function urlHasRoot($url, $root, $strict = false)
    {
        if (! $strict) {
            $url = $this->removeScheme($url);
            $root = $this->removeScheme($root);
        }

        return (bool) preg_match('~^'.preg_quote($root, '~').'([/\?#].*)?$~i', $url);
    }

    /**
     * Remove scheme for an URL.
     *
     * @param  string  $url
     * @return string
     */
    protected function removeScheme($url)
    {
        return preg_replace('#^https?://#i', '', $url);
    }

    /**
     * Generate an absolute URL to a path for the given application.
     *
     * @param  string  $app
     * @param  string  $path
     * @param  mixed  $extra
     * @return string
     */
    public function url($app = '', $path = '', $extra = [])
    {
        return $this->root($app).$this->stringAfter(
            $this->container['url']->to($path, $extra),
            $this->container['url']->to('')
        );
    }

    /**
     * Return the remainder of a string after a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    protected function stringAfter($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * Register routes for each sub application.
     *
     * @param  array  $attributes
     * @return void
     */
    public function routes(array $attributes = [])
    {
        foreach ($this->appUrls() as $id => $url) {
            if (! file_exists($file = base_path("routes/$id.php"))) {
                continue;
            }

            $this->container['router']->group(
                $this->getRouteGroupAttributes($id, Arr::get($attributes, $id, [])),
                function ($router) use ($file) {
                    require $file;
                }
            );
        }
    }

    /**
     * Get route group attributes for the given application.
     *
     * @param  string  $app
     * @param  array  $attributes
     * @return array
     */
    protected function getRouteGroupAttributes($app, array $attributes = [])
    {
        $attr = [
            'domain' => $this->domain($app),
            'namespace' => $this->getRootControllerNamespace().'\\'.Str::studly($app),
            'middleware' => $this->container['router']->hasMiddlewareGroup($app) ? $app : 'web',
        ];

        if ($prefix = $this->prefix($app)) {
            $attr['prefix'] = $prefix;
        }

        return array_merge($attr, $attributes);
    }

    /**
     * Get the root controller namespace.
     *
     * @return string
     */
    protected function getRootControllerNamespace()
    {
        if ($this->container['url']->hasMacro('getRootControllerNamespace')) {
            $namespace = $this->container['url']->getRootControllerNamespace();
        }

        return isset($namespace) ? $namespace : 'App\Http\Controllers';
    }

    /**
     * Register macros.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @return void
     */
    public static function registerMacros($app)
    {
        $app['url']->macro('getRootControllerNamespace', function () {
            /* @var $this \Illuminate\Routing\UrlGenerator */
            return $this->rootNamespace;
        });
    }
}
