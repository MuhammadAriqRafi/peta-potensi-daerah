<?php

namespace Modules\Login\Controller;

use App\Controllers\BaseController;
use App\Models\Administrator;
use Config\Services;
use Firebase\JWT\JWT;

class LoginController extends BaseController
{
    private $loginModulePath = 'Modules\Login\Views\\';

    public function index()
    {
        $data = [
            'validation' => Services::validation()
        ];

        return view($this->loginModulePath . 'index', $data);
    }

    public function authenticate()
    {
        if (!$this->validate([
            'username' => 'required',
            'password' => [
                'rules' => 'required|validate[username,password]',
                'errors' => [
                    'validate' => 'Email or Password don\'t match'
                ]
            ]
        ])) {
            return redirect()->back()->withInput();
        }

        $administratorModel = new Administrator();
        $administrator = $administratorModel->select('nama, admin_id')
            ->where('username', $this->request->getVar('username'))
            ->first();

        $payload = [
            'admin_id' => base64_encode($administrator['admin_id']),
            'nama' => $administrator['nama']
        ];

        $jwt = JWT::encode($payload, Administrator::$SECRET_KEY, 'HS256');

        return $this->response->setCookie('X-PPD-SESSION', $jwt, '', '', '', '', '', true, '')->redirect('/backend');
    }

    public function logout()
    {
        return $this->response->setCookie('X-PPD-SESSION', '')->redirect('/backend/login');
    }
}
