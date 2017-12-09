<?php

if (! function_exists('apps')) {
    /**
     * Obtain the application manager instance.
     *
     * @return \ElfSundae\Laravel\Apps\AppManager
     */
    function apps()
    {
        return app('apps');
    }
}
