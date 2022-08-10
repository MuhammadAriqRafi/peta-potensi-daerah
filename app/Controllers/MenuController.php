<?php

namespace App\Controllers;

use App\Controllers\CRUDController;
use App\Models\Menu;
use Config\Services;

class MenuController extends CRUDController
{
    public function __construct()
    {
        parent::__construct(new Menu());
    }

    public function index()
    {
        $data = [
            'title' => 'Menu',
            'menus' => $this->model->select('title, url, target, menu_id')->orderBy('menu_id', 'DESC')->findAll(),
            'validation' => Services::validation(),
            'targets' => ['_self', '_blank'],
            'storeUrl' => '/backend/menus/store',
            'editUrl' => '/backend/menus/edit/',
            'updateUrl' => '/backend/menus/update/',
            'destroyUrl' => '/backend/menus/destroy/',
        ];

        foreach ($data['menus'] as $key => $menu) {
            $data['menus'][$key]['menu_id'] = base64_encode($menu['menu_id']);
        }

        return view('menu/index', $data);
    }

    // Ajax Methods
    public function edit($id = null)
    {
        $this->setData(['select' => 'title, url, target, menu_id']);
        return parent::edit($id);
    }

    public function update($id = null)
    {
        // ? Decode $id
        $id = base64_decode($id);

        $data = [
            'menu_id' => $id,
            'title' => $this->request->getVar('title'),
            'url' => $this->request->getVar('url'),
            'target' => $this->request->getVar('target')
        ];

        $this->setData($data);
        $this->setReturnRecentStoredData([
            'status' => true,
            'selected_fields' => 'menu_id, title, url, target'
        ]);

        return parent::update($id);
    }

    public function store()
    {
        $data = [
            'title' => $this->request->getVar('title'),
            'url' => $this->request->getVar('url'),
            'target' => $this->request->getVar('target')
        ];

        $this->setData($data);
        $this->setReturnRecentStoredData([
            'status' => true,
            'selected_fields' => 'menu_id, title, url, target'
        ]);

        return parent::store();
    }

    public function destroy($id = null)
    {
        return $this->response->setJSON(parent::destroy($id));
    }
}
