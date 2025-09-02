<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityAudit
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->user()) {
            $suspiciousPatterns = [
                'script', 'javascript:', 'data:', 'vbscript:', 'onload=', 'onerror=',
                '<script', '</script>', 'eval(', 'setTimeout(', 'setInterval('
            ];

            $inputData = $request->all();
            $inputString = json_encode($inputData);

            foreach ($suspiciousPatterns as $pattern) {
                if (stripos($inputString, $pattern) !== false) {
                    activity()
                        ->causedBy($request->user())
                        ->withProperties([
                            'suspicious_input' => $inputString,
                            'detected_pattern' => $pattern,
                            'ip' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'route' => $request->route()?->getName()
                        ])
                        ->log('Suspicious input detected');
                    
                    break;
                }
            }
        }

        return $response;
    }
}