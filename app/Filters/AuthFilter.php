<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter — redirects unauthenticated requests to /login.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): mixed
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): mixed
    {
        return null;
    }
}
