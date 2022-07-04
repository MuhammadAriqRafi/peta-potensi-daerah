<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Guestbook;

class GuestbookController extends BaseController
{
    protected $guestbooks;

    public function __construct()
    {
        $this->guestbooks = new Guestbook();
    }

    public function index()
    {
        $data = [
            'title' => 'Guestbook',
            'guestbooks' => $this->guestbooks->findAll(),
        ];

        return view('guestbook/index', $data);
    }

    public function show($id = null)
    {
        $guestbook = $this->guestbooks->find(base64_decode($id));

        $data = [
            'title' => $guestbook['title'],
            'guestbook' => $guestbook
        ];

        // TODO: Add helper in the template to set status to read
        return view('guestbook/detail', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'name' =>  'required',
            'email' => 'required|valid_email',
            'title' => 'required',
            'messages' => 'required'
        ])) {
            return redirect()->back()->withInput();
        }

        $this->guestbooks->save([
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'title' => $this->request->getVar('title'),
            'messages' => $this->request->getVar('messages')
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil terkirim!');
    }

    public function destroy($id = null)
    {
        $this->guestbooks->delete($id);
        return redirect()->back()->with('success', 'Pesan berhasil dihapus!');
    }
}
