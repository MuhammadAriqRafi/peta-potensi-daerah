<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Popup;
use Config\Services;

class PopupController extends BaseController
{
    protected $popup;

    public function __construct()
    {
        $this->popup = new Popup();
    }

    public function index()
    {
        $data = [
            'title' => 'Pop Up Manager',
            'popups' => $this->popup->orderBy('popup_id', 'DESC')->findAll(),
            'validation' => Services::validation(),
            'currentActivePopup' => $this->popup->where('status', 'active')->first(),
            'storeUrl' => '/backend/popups/ajaxStore'
        ];

        return view('popup/index', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'title' => 'required',
            'image' => [
                'rules' => 'uploaded[image]|max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Image is required'
                ]
            ]
        ])) {
            return redirect()->back()->withInput();
        }

        // Retrieve image file from input field\
        $imageName = storeAs($this->request->getFile('image'), 'img', 'popup');

        $this->popup->save([
            'title' => $this->request->getVar('title'),
            'value' => $imageName
        ]);

        return redirect()->back()->with('success', 'Pop up berhasil ditambahkan!');
    }

    public function edit($id = null)
    {
        $data = [
            'title' => 'Edit Popup',
            'popup' => $this->popup->find(base64_decode($id)),
            'validation' => Services::validation()
        ];

        return view('popup/edit', $data);
    }

    public function update($id = null)
    {
        if (!$this->validate([
            'title' => 'required',
            'image' => 'max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
        ])) {
            return redirect()->back()->withInput();
        }

        $image = $this->request->getFile('image');
        $oldImage = $this->request->getVar('oldImage');

        if ($image->getError() != 4) {
            $imageName = storeAs($image, 'img', 'popup');
            unlink('img/' . $oldImage);
        } else {
            $imageName = $oldImage;
        }

        $this->popup->save([
            'popup_id' => $id,
            'title' => $this->request->getVar('title'),
            'value' => $imageName
        ]);

        return redirect()->back()->with('success', 'Pop up berhasil diubah!');
    }

    public function destroy($id = null)
    {
        // Find current popup record
        $popup = $this->popup->find($id);

        // Delete image file of the current record
        try {
            unlink('img/' . $popup['value']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        $this->popup->delete($id);
        return redirect()->back()->with('success', 'Pop Up berhasil dihapus!');
    }

    public function update_status()
    {
        if (!$this->validate([
            'status' => 'required'
        ])) {
            return redirect()->back()->withInput();
        }

        $this->popup->save([
            'popup_id' => base64_decode($this->request->getVar('status')),
            'status' => 'active'
        ]);

        $this->popup->save([
            'popup_id' => base64_decode($this->request->getVar('oldActivePopup')),
            'status' => 'non_active'
        ]);

        return redirect()->back()->with('success', 'Berhasil update pop up active!');
    }

    // Ajax Method
    public function ajaxStore()
    {
        $rules = $this->popup->getPopupValidationRules();

        if (!$this->validate($rules)) {
            return $this->response->setJSON(_validate($rules));
        }

        $imageName = storeAs($this->request->getFile('image'), 'img', 'popup');

        $data = [
            'title' => $this->request->getVar('title'),
            'value' => $imageName
        ];

        if ($this->popup->save($data)) {
            $newPopup = $this->popup->find($this->popup->getInsertID());
            $newPopup['actions'] = strtr(editDeleteBtn(false), ['$id' => $newPopup['popup_id']]);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Popup berhasil ditambahkan',
                'data' => $newPopup
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Popup gagal ditambahkan'
            ]);
        }
    }
}
