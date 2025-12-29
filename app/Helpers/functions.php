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

if (!function_exists('formatDate')) {
    /**
     * Format a date using the application's default date format (dd-mm-yyyy)
     *
     * @param mixed $date
     * @param string|null $format
     * @return string
     */
    function formatDate($date, $format = null)
    {
        if (empty($date)) {
            return '';
        }
        
        $defaultFormat = config('app.date_format', 'd-m-Y');
        $format = $format ?? $defaultFormat;
        
        if ($date instanceof \Carbon\Carbon || $date instanceof \DateTime) {
            return $date->format($format);
        }
        
        if (is_string($date)) {
            try {
                $carbon = \Carbon\Carbon::parse($date);
                return $carbon->format($format);
            } catch (\Exception $e) {
                return $date;
            }
        }
        
        return '';
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format a date with time using the application's default format
     *
     * @param mixed $date
     * @param string|null $format
     * @return string
     */
    function formatDateTime($date, $format = null)
    {
        if (empty($date)) {
            return '';
        }
        
        $defaultFormat = config('app.date_format', 'd-m-Y') . ' H:i:s';
        $format = $format ?? $defaultFormat;
        
        if ($date instanceof \Carbon\Carbon || $date instanceof \DateTime) {
            return $date->format($format);
        }
        
        if (is_string($date)) {
            try {
                $carbon = \Carbon\Carbon::parse($date);
                return $carbon->format($format);
            } catch (\Exception $e) {
                return $date;
            }
        }
        
        return '';
    }
}

