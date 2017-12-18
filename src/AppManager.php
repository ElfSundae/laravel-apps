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
     * @var string
     */
    protected $appId;

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
    public function urls()
    {
        return $this->container['config']->get('apps.url', []);
    }

    /**
     * Get the root URL for the application identifier.
     *
     * @param  string  $app
     * @return string
     */
    public function root($app = '')
    {
        return Arr::get($this->urls(), (string) $app)
            ?: $this->container['config']['app.url'];
    }

    /**
     * Get the URL domain for the application identifier.
     *
     * @param  string  $app
     * @return string
     */
    public function domain($app = '')
    {
        return parse_url($this->root($app), PHP_URL_HOST);
    }

    /**
     * Get the URL prefix for the application identifier.
     *
     * @param  string  $app
     * @return string
     */
    public function prefix($app = '')
    {
        return trim(parse_url($this->root($app), PHP_URL_PATH), '/');
    }

    /**
     * Get or check the current application identifier.
     *
     * @return string|bool
     */
    public function id()
    {
        if (is_null($this->appId)) {
            $this->appId = (string) $this->idForUrl($this->container['request']->getUri());
        }

        if (func_num_args() > 0) {
            return in_array($this->appId, is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args());
        }

        return $this->appId;
    }

    /**
     * Get the application identifier for the given URL.
     *
     * @param  string  $url
     * @return string|null
     */
    public function idForUrl($url)
    {
        return collect($this->urls())
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
     * Refresh the current application identifier.
     *
     * @return $this
     */
    public function refreshId()
    {
        $this->appId = null;

        return $this;
    }

    /**
     * Determine if a URL has the given root URL.
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
     * Remove scheme for a URL.
     *
     * @param  string  $url
     * @return string
     */
    protected function removeScheme($url)
    {
        return preg_replace('#^https?://#i', '', $url);
    }

    /**
     * Generate an absolute URL to a path for the given application identifier.
     *
     * @param  string  $app
     * @param  string  $path
     * @param  mixed  $parameters
     * @return string
     */
    public function url($app = '', $path = '', $parameters = [])
    {
        return $this->root($app).$this->stringAfter(
            $this->container['url']->to($path, $parameters),
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
     * Register routes for each application.
     *
     * You may call this method in `RouteServiceProvider::map()`.
     *
     * @param  array  $attributes
     * @return void
     */
    public function routes(array $attributes = [])
    {
        foreach ($this->urls() as $id => $url) {
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
            'prefix' => $this->prefix($app),
            'middleware' => $this->container['router']->hasMiddlewareGroup($app) ? $app : 'web',
            'namespace' => $this->getRootControllerNamespace($app),
        ];

        return array_filter(array_merge($attr, $attributes));
    }

    /**
     * Get the root controller namespace for the given application.
     *
     * @param  string  $app
     * @return string
     */
    protected function getRootControllerNamespace($app)
    {
        $namespace = $this->container['url']->getRootControllerNamespace()
            ?: 'App\Http\Controllers';

        return trim($namespace.'\\'.Str::studly($app), '\\');
    }
}
