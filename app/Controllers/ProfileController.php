<?php

namespace App\Controllers;

use App\Controllers\PostController;
use App\Models\Post;
use Config\Services;

class ProfileController extends PostController
{
    protected $profiles;

    public function __construct()
    {
        $this->profiles = new Post();
    }

    public function index()
    {
        $data = [
            'title' => 'Tentang Aplikasi',
            'profiles' => $this->profiles->getProfiles(),
            'statuses' => ['draft', 'publish'],
            'validation' => Services::validation()
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

    public function update($id = null)
    {
        if (!$this->validate([
            'title' => 'required',
            'date_publish' => 'required',
            'content' => 'required',
            'status' => 'required',
            'description' => 'required'
        ])) {
            return redirect()->back()->withInput();
        }

        $title = $this->request->getVar('title');

        $this->profiles->save([
            'post_id' => $id,
            'post_type' => 'profil',
            'date_modify' => date('Y-m-d H:i:s'),
            'slug' => url_title($title, '-', true),
            'title' => $title,
            'date_publish' => $this->request->getVar('date_publish'),
            'content' => $this->request->getVar('content'),
            'status' => $this->request->getVar('status'),
            'description' => $this->request->getVar('description')
        ]);

        return redirect()->back()->with('success', 'Tentang Aplikasi berhasil diubah!');
    }

    // Ajax Methods
    public function ajaxIndex()
    {
        $uri = service('uri');

        // ? Call method ajaxGetDataDataTables from PostController to display data
        return $this->ajaxGetDataDataTables($uri->getSegment(2), $this->profiles);
    }

    public function ajaxStore()
    {
        $rules = $this->profiles->getProfileValidationRules();

        if (!$this->validate($rules)) {
            return $this->response->setJSON($this->profiles->_validate($rules));
        }

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

        if ($this->profiles->save($data)) return $this->response->setJSON([
            'status' => true,
            'message' => 'Profil berhasil ditambahkan!'
        ]);
        else return $this->response->setJSON([
            'status' => false,
            'message' => 'Profil gagal ditambahkan!'
        ]);
    }
}
