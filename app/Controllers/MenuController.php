<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Menu;
use Config\Services;

class MenuController extends BaseController
{
    protected $menus;

    public function __construct()
    {
        $this->menus = new Menu();
    }

    public function index()
    {
        $data = [
            'title' => 'Menu',
            'menus' => $this->menus->findAll(),
            'validation' => Services::validation()
        ];

        return view('menu/index', $data);
    }

    public function edit($id = null)
    {
        $menu = $this->menus->find($id);
        return $this->response->setJSON($menu);
    }

    public function update($id = null)
    {
        if (!$this->validate([
            'title' => 'required',
            'url' => 'required'
        ])) {
            return redirect()->back()->withInput();
        }

        $this->menus->save([
            'menu_id' => base64_decode($id),
            'title' => $this->request->getVar('title'),
            'url' => $this->request->getVar('url')
        ]);

        return redirect()->back()->with('success', 'Menu berhasil diubah!');
    }
}
