<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtén la ruta actual a través de la solicitud
        $currentRoute = $request->path();
        
        // Lista de rutas permitidas (rutas accesibles directamente)
        $allowedRoutes = [,
            // Agrega aquí otras rutas permitidas
        ];
        
        // Verifica si la ruta actual está en la lista de rutas permitidas
        if (!in_array($currentRoute, $allowedRoutes)) {
            // Redirecciona al inicio o muestra un mensaje de error
            return redirect()->route('inicio')->with('error', 'Acceso no permitido');
        }
        return $next($request);
    }
}
