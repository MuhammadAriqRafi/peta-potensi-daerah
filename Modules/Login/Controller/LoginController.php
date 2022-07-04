<?php

namespace Modules\Login\Controller;

use App\Controllers\BaseController;
use App\Models\Administrator;
use Config\Services;

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
        $administrator = $administratorModel->where('username', $this->request->getVar('username'))->first();

        $data = [
            'admin_id' => base64_encode($administrator['admin_id']),
            'username' => $administrator['username'],
            'isLoggedIn' => true,
        ];

        session()->set($data);

        return redirect()->route('backend.dashboard.index');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->route('login.index');
    }
}
