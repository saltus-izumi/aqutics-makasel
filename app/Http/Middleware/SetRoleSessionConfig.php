<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetRoleSessionConfig
{
    public function handle(Request $request, Closure $next)
    {
        $basePath = trim($request->getBasePath(), '/');
        $adminPrefix = $basePath === '' ? 'admin' : $basePath . '/admin';
        $ownerPrefix = $basePath === '' ? 'owner' : $basePath . '/owner';

        if ($request->is($adminPrefix, $adminPrefix . '/*')) {
            $adminPath = '/' . $adminPrefix;
            config([
                'session.cookie' => config('session.admin_cookie', 'pm-admin-session'),
                'session.path' => config('session.admin_path', $adminPath),
            ]);
        } elseif ($request->is($ownerPrefix, $ownerPrefix . '/*')) {
            $ownerPath = '/' . $ownerPrefix;
            config([
                'session.cookie' => config('session.owner_cookie', 'pm-owner-session'),
                'session.path' => config('session.owner_path', $ownerPath),
            ]);
        }

        return $next($request);
    }
}
