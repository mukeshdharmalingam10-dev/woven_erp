<?php

use App\Helpers\DebugHelper;

if (!function_exists('debug_log')) {
    /**
     * Simple debug logging with index
     * 
     * @param mixed $data
     * @param string $index
     * @return void
     */
    function debug_log($data, $index = 'DEBUG')
    {
        DebugHelper::debug($data, $index, 'log');
    }
}

if (!function_exists('debug_dd')) {
    /**
     * Debug and die with index
     * 
     * @param mixed $data
     * @param string $index
     * @return void
     */
    function debug_dd($data, $index = 'DEBUG')
    {
        DebugHelper::debug($data, $index, 'dd');
    }
}

if (!function_exists('debug_dump')) {
    /**
     * Debug dump with index
     * 
     * @param mixed $data
     * @param string $index
     * @return void
     */
    function debug_dump($data, $index = 'DEBUG')
    {
        DebugHelper::debug($data, $index, 'dump');
    }
}

if (!function_exists('debug_context')) {
    /**
     * Debug with context (file, line, function)
     * 
     * @param mixed $data
     * @param string $index
     * @return void
     */
    function debug_context($data, $index = 'DEBUG')
    {
        DebugHelper::debugWithContext($data, $index);
    }
}

if (!function_exists('debug_permissions')) {
    /**
     * Debug user permissions
     * 
     * @param \App\Models\User $user
     * @param string|null $form
     * @param string $index
     * @return void
     */
    function debug_permissions($user, $form = null, $index = 'PERMISSIONS')
    {
        DebugHelper::debugPermissions($user, $form, $index);
    }
}

