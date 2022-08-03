<?php

namespace App\Controllers;

use App\Controllers\CRUDController;
use App\Models\Administrator;
use Config\Services;

class AdministratorController extends CRUDController
{
    public function __construct()
    {
        parent::__construct(new Administrator());
    }

    public function index()
    {
        $data = [
            'title' => 'Administrators',
            'administrators' => $this->model->getAdmins(),
            'storeUrl' => '/backend/administrators/ajaxStore',
            'indexUrl' => '/backend/administrators/ajaxIndex',
            'destroyUrl' => '/backend/administrators/ajaxDestroy/',
            'editUrl' => '/backend/administrators/ajaxEdit/',
            'updateUrl' => '/backend/administrators/ajaxUpdate/'
        ];

        return view('administrator/index', $data);
    }

    public function update($id = null)
    {
        if (!$this->validate([
            'nik' => 'required|is_unique[administrators.nik,nik,{nik}]',
            'nama' => 'required',
            'username' => 'required',
            'password' => 'required',
            'passconf' => 'required|matches[password]'
        ])) {
            return redirect()->back()->withInput();
        }

        $this->administrators->save([
            'admin_id' => $id,
            'nik' => $this->request->getVar('nik'),
            'nama' => $this->request->getVar('nama'),
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password')
        ]);

        return redirect()->back()->with('success', 'Administrator berhasil diubah!');
    }

    // Ajax Methods
    public function ajaxIndex()
    {
        return parent::ajaxIndex();
    }

    public function ajaxStore()
    {
        $data = [
            'nik' => $this->request->getVar('nik'),
            'nama' => $this->request->getVar('nama'),
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password')
        ];

        $this->setData($data);
        $this->setReturnRecentStoredData(true);
        return parent::ajaxStore();
    }

    public function ajaxEdit($id = null)
    {
        return parent::ajaxEdit($id);
    }

    public function ajaxUpdate($id = null)
    {
        // TODO: When validating, the is unique rule will check the id in admin table and match it with its request id field value, the problem is the requested id is still encoded, should be decoded before entering controller
        $id = base64_decode($id);

        $data = [
            'admin_id' => $id,
            'nik' => $this->request->getVar('nik'),
            'nama' => $this->request->getVar('nama'),
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password')
        ];

        $this->setData($data);

        return parent::ajaxUpdate($id);
    }

    public function ajaxDestroy($id = null)
    {
        return $this->response->setJSON(parent::ajaxDestroy($id));
    }
}
