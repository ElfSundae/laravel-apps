<?php

namespace ElfSundae\Laravel\Apps\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array urls()
 * @method static string root(string $app = '')
 * @method static string domain(string $app = '')
 * @method static string prefix(string $app = '')
 * @method static string|bool id()
 * @method static string|null idForUrl(string $url)
 * @method static static refreshId()
 * @method static string url(string $app = '', string $path = '', mixed $parameters = [])
 * @method static void routes(array $attributes = [])
 *
 * @see \ElfSundae\Laravel\Apps\AppManager
 */
class Apps extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'apps';
    }
}
