<?php

if (!function_exists('is_active_route')) {
    function is_active_route(array|string $routes, string $activeClass = 'active bg-gradient-primary text-white', string $defaultClass = 'text-dark'): string
    {
        return request()->routeIs($routes) ? $activeClass : $defaultClass;
    }
}
