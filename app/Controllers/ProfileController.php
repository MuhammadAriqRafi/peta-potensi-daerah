<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Post;
use Config\Services;

class ProfileController extends BaseController
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

    public function store()
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
            'post_type' => 'profil',
            'slug' => url_title($title, '-', true),
            'title' => $title,
            'date_publish' => $this->request->getVar('date_publish'),
            'content' => $this->request->getVar('content'),
            'status' => $this->request->getVar('status'),
            'description' => $this->request->getVar('description')
        ]);

        return redirect()->back()->with('success', 'Tentang Aplikasi berhasil ditambahkan');
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
        dd($this->request->getVar());
        return "Update Profile";
    }

    public function destroy($id = null)
    {
        return "Destroy Profile";
    }
}
