<?php

namespace ElfSundae\Laravel\Apps;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Container\Container;

class Apps
{
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
     * Create a new Apps instance.
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
     * Get application identifier for the given URL.
     *
     * @param  string  $url
     * @return string
     */
    public function idForUrl($url)
    {
        return Collection::make($this->container['config']['apps.url'])
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
            $url = $this->removeSchemeForUrl($url);
            $root = $this->removeSchemeForUrl($root);
        }

        return preg_match('~^'.preg_quote($root, '~').'([/\?#].*)?$~i', $url);
    }

    /**
     * Remove scheme for an URL.
     *
     * @param  string  $url
     * @return string
     */
    protected function removeSchemeForUrl($url)
    {
        return preg_replace('#^https?://#i', '', $url);
    }

    /**
     * Get the root URL for the given application identifier.
     *
     * @param  string  $appId
     * @return string
     */
    public function rootUrl($appId = '')
    {
        $config = $this->container['config'];

        return $config["apps.url.$appId"] ?: $config['app.url'];
    }

    /**
     * Generate an absolute URL to a path for the given application identifier.
     *
     * @param  string  $appId
     * @param  string  $path
     * @param  mixed  $extra
     * @return string
     */
    public function url($appId = '', $path = '', $extra = [])
    {
        $url = $this->container['url'];

        return $this->rootUrl($appId).
            Str::replaceFirst($url->to(''), '', $url->to($path, $extra));
    }
}
