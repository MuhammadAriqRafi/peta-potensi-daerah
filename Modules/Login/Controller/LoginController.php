<?php

namespace Modules\Login\Controller;

use App\Controllers\BaseController;
use App\Models\Administrator;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LoginController extends BaseController
{
    private $loginModulePath = 'Modules\Login\Views\\';
    private $administrator;

    public function __construct()
    {
        $this->administrator = new Administrator();
    }

    public function index()
    {
        $data = [
            'validation' => Services::validation()
        ];

        return view($this->loginModulePath . 'index', $data);
    }

    private function generateJwt($payload)
    {
        return JWT::encode($payload, Administrator::$SECRET_KEY, 'HS256');
    }

    private function getJwtPayload($jwt)
    {
        return JWT::decode($jwt, new Key(Administrator::$SECRET_KEY, 'HS256'));
    }

    private function validationRules()
    {
        return $rules = [
            'username' => 'required',
            'password' => [
                'rules' => 'required|validate[username,password]',
                'errors' => [
                    'validate' => 'Email or Password don\'t match'
                ]
            ]
        ];
    }

    public function authenticate()
    {
        $rules = $this->validationRules();
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput();
        }

        $administrator = $this->administrator->select('nama, admin_id')
            ->where('username', $this->request->getVar('username'))
            ->first();
        $payload = [
            'session_id' => session_id(),
            'admin_id' => base64_encode($administrator['admin_id']),
            'nama' => $administrator['nama']
        ];
        $administratorNewStatus = [
            'admin_id' => $administrator['admin_id'],
            'status' => session_id()
        ];

        $jwt = $this->generateJwt($payload);

        if ($this->administrator->save($administratorNewStatus)) return $this->response->setCookie('X-PPD-SESSION', $jwt, 10800, '', '', '', '', true, '')->redirect('/backend');
        else redirect()->back();
    }

    public function logout()
    {
        $jwt = $this->request->getCookie('X-PPD-SESSION');
        $payload = $this->getJwtPayload($jwt);
        $administrator = $this->administrator->select('admin_id')
            ->find(base64_decode($payload->admin_id))['admin_id'];
        $administratorNewStatus = [
            'admin_id' => $administrator,
            'status' => null
        ];

        if ($this->administrator->save($administratorNewStatus)) return $this->response->setCookie('X-PPD-SESSION', '')->redirect('/backend/login');
        else redirect()->back();
    }
}
