<?php

namespace Modules\Login\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Guest implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('session_management');
        $jwt = $request->getCookie('X-PPD-SESSION');

        if ($jwt) {
            if (validateJWT($jwt)) return redirect()->route('backend.dashboard.index');
            return true;
        }

        return true;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
