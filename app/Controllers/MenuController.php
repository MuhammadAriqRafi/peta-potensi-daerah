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
            'menus' => $this->menus->select('title, url, target, menu_id')->orderBy('menu_id', 'DESC')->findAll(),
            'validation' => Services::validation(),
            'targets' => [
                'Self' => '_self',
                'Blank' => '_blank'
            ]
        ];

        return view('menu/index', $data);
    }

    // Ajax Methods
    public function ajaxShow()
    {
        $id = base64_decode($this->request->getVar('id'));
        $menu = $this->menus->select('title, url, target, menu_id')->find($id);

        // ? Encode menu_id
        foreach ($menu as $key => $value) {
            if ($key == 'menu_id') $menu[$key] = (string)base64_encode($value);
        }

        return $this->response->setJSON($menu);
    }

    public function ajaxUpdate($id = null)
    {
        $rules = $this->menus->getMenuValidationRules();

        if (!$this->validate($rules)) {
            return $this->response->setJSON(_validate($rules));
        }

        $id = base64_decode($id);
        $menu = $this->menus->find($id);
        $data = [
            'menu_id' => $id,
            'title' => $this->request->getVar('title'),
            'url' => $this->request->getVar('url'),
            'target' => $this->request->getVar('target')
        ];

        if ($this->menus->save($data)) {
            $menu = $this->menus->select('title, url, target')->find($id);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Menu berhasil diubah!',
                'data' => $menu
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Menu gagal diubah!',
            ]);
        }
    }

    public function ajaxStore()
    {
        $rules = $this->menus->getMenuValidationRules();

        if (!$this->validate($rules)) {
            return $this->response->setJSON(_validate($rules));
        }

        $data = [
            'title' => $this->request->getVar('title'),
            'url' => $this->request->getVar('url'),
            'target' => $this->request->getVar('target')
        ];

        if ($newMenu = $this->menus->save($data)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Menu berhasil ditambahkan',
                'data' => $this->menus->find($this->menus->getInsertID())
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Menu gagal ditambahkan'
            ]);
        }
    }
}
