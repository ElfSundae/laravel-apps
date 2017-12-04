<?php

if (! function_exists('app_id')) {
    /**
     * Get or check the current application identifier.
     *
     * @return string|bool
     */
    function app_id()
    {
        return call_user_func_array([app('apps'), 'id'], func_get_args());
    }
}
