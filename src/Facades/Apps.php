<?php

namespace ElfSundae\Laravel\Apps\Facades;

use Illuminate\Support\Facades\Facade;

/**
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
