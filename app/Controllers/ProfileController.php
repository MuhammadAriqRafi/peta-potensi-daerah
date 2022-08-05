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
            'storeUrl' => '/backend/profiles/ajaxStore',
            'indexUrl' => '/backend/profiles/ajaxIndex',
            'destroyUrl' => '/backend/profiles/ajaxDestroy/',
            'editUrl' => '/backend/profiles/ajaxEdit/',
            'updateUrl' => '/backend/profiles/ajaxUpdate/',
        ];

        return view('profile/index', $data);
    }

    public function edit($id = null)
    {
        $data = [
            'title' => 'Edit Tentang Aplikasi',
            'validation' => Services::validation(),
            'profile' =>  $this->profiles->find(base64_decode($id)),
            'statuses' => ['draft', 'publish']
        ];

        return view('profile/edit', $data);
    }

    // Ajax Methods
    public function ajaxIndex()
    {
        return parent::ajaxIndex();
    }

    public function ajaxStore()
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
        return parent::ajaxStore();
    }

    public function ajaxDestroy($id = null)
    {
        return $this->response->setJSON(parent::ajaxDestroy($id));
    }

    public function ajaxEdit($id = null)
    {
        $this->setData(['select' => 'title, DATE(date_publish) as date_publish, content, description, status, post_id']);
        return parent::ajaxEdit($id);
    }

    public function ajaxUpdate($id = null)
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
        return parent::ajaxUpdate($id);
    }
}
