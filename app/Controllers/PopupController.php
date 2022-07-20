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
            'storeUrl' => '/backend/popups/ajaxStore',
            'indexUrl' => '/backend/popups/ajaxIndex',
            'destroyUrl' => '/backend/popups/ajaxDestroy/',
            'editUrl' => '/backend/popups/ajaxEdit/',
            'updateUrl' => '/backend/popups/ajaxUpdate/',
            'updateActivePopupUrl' => '/backend/popups/ajaxUpdateActivePopup',
        ];

        $data['popups'] = encodeId($data['popups'], 'popup_id');

        return view('popup/index', $data);
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

    // Ajax Method
    public function ajaxIndex()
    {
        $popups = $this->popup->findAll();
        $popups = encodeId($popups, 'popup_id');

        return $this->response->setJSON($popups);
    }

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
            $newPopup['popup_id'] = base64_encode($newPopup['popup_id']);
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

    public function ajaxEdit($id = null)
    {
        $id = base64_decode($id);
        $popup = $this->popup->select('title, value, popup_id')->find($id);
        $popup['popup_id'] = base64_encode($popup['popup_id']);

        return $this->response->setJSON($popup);
    }

    public function ajaxUpdate()
    {
        $rules = $this->popup->getPopupValidationRules();
        $rules['image']['rules'] = str_replace('uploaded[image]|', '', $rules['image']['rules']);

        if (!$this->validate($rules)) {
            return $this->response->setJSON(_validate($rules));
        }

        $id = base64_decode($this->request->getVar('id'));
        $oldImage = $this->request->getVar('oldImage');
        $newImage = $this->request->getFile('image');

        if ($newImage->getError() != 4) {
            $message = deleteImage($oldImage, 'img/', 'Pop Up');
            $imageName = storeAs($this->request->getFile('image'), 'img/', 'popup');
        } else {
            $imageName = $oldImage;
        }

        $data = [
            'popup_id' => $id,
            'title' => $this->request->getVar('title'),
            'value' => $imageName
        ];

        if ($this->popup->save($data)) {
            $updatedPopup = $this->popup->select('title, value, status')->find($id);

            return $this->response->setJSON([
                'status' => true,
                'message' => $message,
                'data' => $updatedPopup
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Pop Up gagal diubah',
            ]);
        }
    }

    public function ajaxDestroy($id = null)
    {
        $id = base64_decode($id);
        $popup = $this->popup->find($id);
        $message = deleteImage($popup['value'], 'img/', 'Pop Up');

        if ($this->popup->delete($id)) {
            return $this->response->setJSON([
                'message' => $message,
                'idDeletedPopup' => base64_encode($id),
                'isActivePopupExist' => $this->popup->isActivePopupExist()
            ]);
        } else {
            return $this->response->setJSON(['message' => 'Terjadi kesalahan pada server']);
        }
    }

    public function ajaxUpdateActivePopup()
    {
        if (!$this->validate(['id' => 'required'])) {
            return $this->response->setJSON(_validate(['id' => 'required']));
        }

        if ($this->request->getVar('oldActivePopup')) {
            $oldActivePopupId = base64_decode($this->request->getVar('oldActivePopup'));
            $updateOldActivePopup = $this->popup->save([
                'popup_id' => $oldActivePopupId,
                'status' => 'non_active'
            ]);
        } else {
            $updateOldActivePopup = true;
        }

        $newActivePopupId = base64_decode($this->request->getVar('id'));
        $updateNewActivePopup = $this->popup->save([
            'popup_id' => $newActivePopupId,
            'status' => 'active'
        ]);

        if ($updateOldActivePopup && $updateNewActivePopup) {
            $updatedActivePopup = $this->popup->select('popup_id, value, title')->find($newActivePopupId);
            $updatedActivePopup['popup_id'] = base64_encode($updatedActivePopup['popup_id']);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Pop Up Active berhasil diubah',
                'data' => $updatedActivePopup
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Pop Up Active gagal diubah'
            ]);
        }
    }
}
