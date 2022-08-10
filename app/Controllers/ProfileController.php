<?php

namespace App\Controllers;

use App\Controllers\CRUDController;
use App\Models\Post;
use Config\Services;

class ProfileController extends CRUDController
{
    public function __construct()
    {
        parent::__construct(new Post('profil'));
    }

    public function index()
    {
        $data = [
            'title' => 'Tentang Aplikasi',
            'profiles' => $this->model->getProfiles(),
            'statuses' => ['draft', 'publish'],
            'validation' => Services::validation(),
            'storeUrl' => '/backend/profiles/store',
            'indexUrl' => '/backend/profiles/ajaxIndex',
            'destroyUrl' => '/backend/profiles/destroy/',
            'editUrl' => '/backend/profiles/edit/',
            'updateUrl' => '/backend/profiles/update/',
        ];

        return view('profile/index', $data);
    }

    // Ajax Methods
    public function ajaxIndex()
    {
        return parent::index();
    }

    public function store()
    {
        $title = $this->request->getVar('title');
        $data = [
            'post_type' => 'profil',
            'slug' => url_title($title, '-', true),
            'title' => $title,
            'date_publish' => $this->request->getVar('date_publish'),
            'content' => $this->request->getVar('content'),
            'status' => $this->request->getVar('status'),
            'description' => $this->request->getVar('description'),
        ];

        $this->setData($data);
        return parent::store();
    }

    public function destroy($id = null)
    {
        return $this->response->setJSON(parent::destroy($id));
    }

    public function edit($id = null)
    {
        $this->setData(['select' => 'title, DATE(date_publish) as date_publish, content, description, status, post_id']);
        return parent::edit($id);
    }

    public function update($id = null)
    {
        // ? Decode $id
        $id = base64_decode($id);
        $title = $this->request->getVar('title');

        $data = [
            'post_id' => $id,
            'post_type' => 'profil',
            'date_modify' => date('Y-m-d H:i:s'),
            'slug' => url_title($title, '-', true),
            'title' => $title,
            'date_publish' => $this->request->getVar('date_publish'),
            'content' => $this->request->getVar('content'),
            'status' => $this->request->getVar('status'),
            'description' => $this->request->getVar('description')
        ];

        $this->setData($data);
        return parent::update($id);
    }
}
