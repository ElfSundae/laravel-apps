<?php

namespace ElfSundae\Laravel\Apps;

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
     * @return void
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
        $identifier = null;

        foreach ($this->container['config']['apps.url'] as $id => $root) {
            $root = preg_replace('~^https?://~i', '', $root);
            $pattern = '~^https?://'.preg_quote($root, '~').'([/\?#].*)?$~i';
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
}
