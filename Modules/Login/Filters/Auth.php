<?php

namespace Modules\Login\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('session_management');
        $jwt = $request->getCookie('X-PPD-SESSION');

        if (!$jwt) return redirect()->route('login.index');
        if (validateJWT($jwt)) return true;

        return redirect()->route('login.index');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
