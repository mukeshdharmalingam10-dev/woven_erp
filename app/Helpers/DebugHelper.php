<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class DebugHelper
{
    /**
     * Debug with index/identifier for easy tracking
     * 
     * @param mixed $data Data to debug
     * @param string $index Unique identifier for this debug point
     * @param string $type Type of debug: 'log', 'dd', 'dump', 'var_dump'
     * @return void
     */
    public static function debug($data, $index = 'DEBUG', $type = 'log')
    {
        $message = "[{$index}] " . print_r($data, true);
        
        switch ($type) {
            case 'dd':
                dd($data);
                break;
            case 'dump':
                dump($data);
                break;
            case 'var_dump':
                var_dump($data);
                break;
            case 'log':
            default:
                Log::debug($message);
                break;
        }
    }

    /**
     * Debug with context (file, line, function)
     * 
     * @param mixed $data
     * @param string $index
     * @return void
     */
    public static function debugWithContext($data, $index = 'DEBUG')
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $backtrace[1] ?? $backtrace[0];
        
        $context = [
            'file' => $caller['file'] ?? 'unknown',
            'line' => $caller['line'] ?? 'unknown',
            'function' => $caller['function'] ?? 'unknown',
            'class' => $caller['class'] ?? 'unknown',
        ];
        
        $message = "[{$index}] " . json_encode([
            'context' => $context,
            'data' => $data
        ], JSON_PRETTY_PRINT);
        
        Log::debug($message);
    }

    /**
     * Debug SQL queries
     * 
     * @param string $index
     * @return void
     */
    public static function debugQueries($index = 'SQL')
    {
        if (config('app.debug')) {
            \DB::enableQueryLog();
            $queries = \DB::getQueryLog();
            self::debug($queries, $index);
        }
    }

    /**
     * Debug request data
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $index
     * @return void
     */
    public static function debugRequest($request, $index = 'REQUEST')
    {
        $data = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'params' => $request->all(),
            'headers' => $request->headers->all(),
        ];
        
        self::debug($data, $index);
    }

    /**
     * Debug user permissions
     * 
     * @param \App\Models\User $user
     * @param string $form
     * @param string $index
     * @return void
     */
    public static function debugPermissions($user, $form = null, $index = 'PERMISSIONS')
    {
        $data = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'is_super_admin' => $user->isSuperAdmin(),
            'roles' => $user->roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->map(function($perm) {
                        return [
                            'form' => $perm->form_name,
                            'read' => $perm->pivot->read ?? false,
                            'write' => $perm->pivot->write ?? false,
                            'delete' => $perm->pivot->delete ?? false,
                        ];
                    })
                ];
            }),
        ];
        
        if ($form) {
            $data['check_form'] = $form;
            $data['has_read'] = $user->hasPermission($form, 'read');
            $data['has_write'] = $user->hasPermission($form, 'write');
            $data['has_delete'] = $user->hasPermission($form, 'delete');
        }
        
        self::debug($data, $index);
    }

    /**
     * Performance timer
     * 
     * @param string $index
     * @return \Closure
     */
    public static function timer($index = 'TIMER')
    {
        $start = microtime(true);
        $memoryStart = memory_get_usage();
        
        return function() use ($index, $start, $memoryStart) {
            $end = microtime(true);
            $memoryEnd = memory_get_usage();
            
            $data = [
                'time' => round(($end - $start) * 1000, 2) . 'ms',
                'memory' => round(($memoryEnd - $memoryStart) / 1024, 2) . 'KB',
            ];
            
            self::debug($data, $index);
        };
    }
}

