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
            'storeUrl' => '/backend/administrators/store',
            'indexUrl' => '/backend/administrators/ajaxIndex',
            'destroyUrl' => '/backend/administrators/destroy/',
            'editUrl' => '/backend/administrators/edit/',
            'updateUrl' => '/backend/administrators/update/'
        ];

        return view('administrator/index', $data);
    }

    // Ajax Methods
    public function ajaxIndex()
    {
        return parent::index();
    }

    public function store()
    {
        $data = [
            'nik' => $this->request->getVar('nik'),
            'nama' => $this->request->getVar('nama'),
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password')
        ];

        $this->setData($data);
        $this->setReturnRecentStoredData(true);
        return parent::store();
    }

    public function edit($id = null)
    {
        return parent::edit($id);
    }

    public function update($id = null)
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

        return parent::update($id);
    }

    public function destroy($id = null)
    {
        return $this->response->setJSON(parent::destroy($id));
    }
}
