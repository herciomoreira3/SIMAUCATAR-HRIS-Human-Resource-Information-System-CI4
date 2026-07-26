<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Adds conservative headers without imposing a CSP on the existing UI. */
final class SecurityHeaders implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->setHeader('Permissions-Policy', 'microphone=(), payment=(), usb=()');
        $response->setHeader('X-Permitted-Cross-Domain-Policies', 'none');

        // Health probes must stay side-effect free: do not initialize a session
        // just to decide their cache policy.
        $path = trim($request->getUri()->getPath(), '/');
        if (! in_array($path, ['health/live', 'health/ready'], true) && session()->get('isLoggedIn') === true) {
            $response->setHeader('Cache-Control', 'private, no-store');
            $response->setHeader('Pragma', 'no-cache');
        }

        return $response;
    }
}
