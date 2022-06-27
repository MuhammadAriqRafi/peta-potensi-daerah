<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Administrator;
use Config\Services;

class AdministratorController extends BaseController
{
    protected $administrators;

    public function __construct()
    {
        $this->administrators = new Administrator();
    }

    public function index()
    {
        $data = [
            'title' => 'Administrators',
            'administrators' => $this->administrators->getAdmins(),
            'validation' => Services::validation()
        ];

        return view('administrator/index', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'nik' => 'required|is_unique[administrators.nik]',
            'nama' => 'required',
            'username' => 'required',
            'password' => 'required',
            'passconf' => 'required|matches[password]'
        ])) {
            return redirect()->back()->withInput();
        }

        $this->administrators->save([
            'nik' => $this->request->getVar('nik'),
            'nama' => $this->request->getVar('nama'),
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password')
        ]);

        return redirect()->back()->with('success', 'Administrator berhasil ditambahkan!');
    }

    public function edit($id = null)
    {
        $data = [
            'title' => 'Ubah Administrator',
            'administrator' => $this->administrators->find(base64_decode($id)),
            'validation' => Services::validation()
        ];

        return view('administrator/edit', $data);
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

    public function destroy($id = null)
    {
        $this->administrators->delete($id);
        return redirect()->back()->with('success', 'Administrator berhasil dihapus!');
    }
}
