<?php

namespace App\Http\Middleware\WebooshCore;

use Closure;
use Illuminate\Support\Facades\Redirect;

class CheckRole
{
    public function handle($request, Closure $next){
        // Get Required roles from route
        $roles = $this->GetRequiredRoleForRoute($request->route());

        if (empty($roles)) return $next($request);
        if (empty($request->user())) return redirect( $this->GetRedirectForRoute($request->route()) );

        // Check Rules
        $allowed = false;
        foreach($request->user()->roles as $role){
            if(in_array($role->name, $roles)){
                $allowed = true;
            }
        }


        if ($allowed) return $next($request);
        else return redirect( $this->GetRedirectForRoute($request->route()) );
    }

    private function GetRequiredRoleForRoute($route){
        $actions = $route->getAction();
        return isset($actions['roles']) ? $actions['roles'] : null;
    }
    private function GetRedirectForRoute($route){
        $actions = $route->getAction();
        return isset($actions['redirect']) ? $actions['redirect'] : '/';
    }
}
