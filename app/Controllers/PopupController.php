<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Popup;
use BackedEnum;
use Config\Services;

class PopupController extends BaseController
{
    protected $popup;

    public function __construct()
    {
        $this->popup = new Popup();
    }

    public function index()
    {
        $data = [
            'title' => 'Pop Up Manager',
            'popups' => $this->popup->findAll()
        ];

        return view('popup/index', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'title' => 'required'
        ])) {
            $validation = Services::validation();
            return redirect()->back()->withInput()->with('validation', $validation);
        }
    }

    public function edit($id = null)
    {
        $data = [
            'title' => 'Edit Popup',
            'popup' => $this->popup->where('popup_id', base64_decode($id))->first()
        ];

        return view('popup/edit', $data);
    }

    public function update($id = null)
    {
        dd($this->request->getVar());
    }

    public function destroy($id = null)
    {
        // TODO: Delete data here
        session()->setFlashdata('success', 'Pop Up berhasil dihapus!');
        return redirect()->back();
    }
}
