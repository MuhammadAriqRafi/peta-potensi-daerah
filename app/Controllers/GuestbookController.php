<?php

namespace App\Controllers;

use App\Controllers\CRUDController;
use App\Models\Guestbook;

class GuestbookController extends CRUDController
{
    public function __construct()
    {
        parent::__construct(new Guestbook());
    }

    public function index()
    {
        $data = [
            'title' => 'Guestbook',
            'indexUrl' => '/backend/guestbooks/ajaxIndex',
            'destroyUrl' => '/backend/guestbooks/destroy/',
            'showUrl' => '/backend/guestbooks/show/',
        ];

        return view('guestbook/index', $data);
    }

    public function ajaxIndex()
    {
        return parent::index();
    }

    public function show($id = null)
    {
        $guestbook = $this->model->find(base64_decode($id));

        $data = [
            'title' => $guestbook['title'],
            'guestbook' => $guestbook
        ];

        return view('guestbook/detail', $data);
    }

    public function store()
    {
        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'title' => $this->request->getVar('title'),
            'messages' => $this->request->getVar('messages')
        ];

        $this->setData($data);
        return parent::store();
    }

    public function destroy($id = null)
    {
        return $this->response->setJSON(parent::destroy($id));
    }
}
