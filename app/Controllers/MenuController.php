<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Menu;

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
            'menus' => $this->menus->findAll()
        ];

        return view('menu/index', $data);
    }

    public function edit($id = null)
    {
    }

    public function update($id = null)
    {
        dd($this->request->getVar());

        $this->menu->save([
            'menu_id' => $id,
            'title' => $this->request->getVar('title'),
            'url' => $this->request->getVar('url'),
            'target' => $this->request->getVar('target')
        ]);
    }
}
