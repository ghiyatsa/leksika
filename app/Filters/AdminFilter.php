<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminFilter — restricts routes to admin-role users only.
 */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): mixed
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to(base_url('/'))->with('error', 'Akses ditolak. Halaman ini hanya untuk Admin.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): mixed
    {
        return null;
    }
}
