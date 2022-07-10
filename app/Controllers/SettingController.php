<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Setting;
use Config\Services;

class SettingController extends BaseController
{
    protected $settings;

    public function __construct()
    {
        $this->settings = new Setting();
    }

    public function index()
    {
        $data = [
            'title' => 'Settings',
            'settings'  => $this->settings->findAll(),
        ];

        return view('setting/index', $data);
    }

    public function edit($id = null)
    {
        $data = [
            'title' => 'Edit Settings',
            'setting' => $this->settings->find(base64_decode($id)),
            'validation' => Services::validation()
        ];

        return view('setting/edit', $data);
    }

    public function update($id = null)
    {
        $backsound = $this->request->getFile('value');

        if (!$this->validate([
            'value' => $backsound ? 'uploaded[value]' : 'required'
        ])) {
            return redirect()->back()->withInput();
        }

        if ($backsound) $backsoundName = storeAs($backsound, 'file', 'setting');

        $this->settings->save([
            'setting_id' => $id,
            'value' => $backsoundName ?? $this->request->getVar('value'),
            'class' => '',
            'sort' => 0
        ]);

        return redirect()->back()->with('success', 'Setting berhasil diubah!');
    }

    public function ajaxUpdate($id = null)
    {
        return $this->response->setJSON($this->request->getVar());
        // $backsound = $this->request->getFile('value');

        // if (!$this->validate([
        //     'value' => $backsound ? 'uploaded[value]' : 'required'
        // ])) {
        //     return redirect()->back()->withInput();
        // }

        // if ($backsound) $backsoundName = storeAs($backsound, 'file', 'setting');

        // $this->settings->save([
        //     'setting_id' => $id,
        //     'value' => $backsoundName ?? $this->request->getVar('value'),
        //     'class' => '',
        //     'sort' => 0
        // ]);

        // return redirect()->back()->with('success', 'Setting berhasil diubah!');
    }
}
