<?php

namespace App\Controllers;

use App\Controllers\CRUDController;
use App\Models\Popup;
use Config\Services;

class PopupController extends CRUDController
{
    public function __construct()
    {
        parent::__construct(new Popup());
    }

    public function index()
    {
        $data = [
            'title' => 'Pop Up Manager',
            'popups' => $this->model->orderBy('popup_id', 'DESC')->findAll(),
            'validation' => Services::validation(),
            'currentActivePopup' => $this->model->where('status', 'active')->first(),
            'storeUrl' => '/backend/popups/store',
            'indexUrl' => '/backend/popups/ajaxIndex',
            'destroyUrl' => '/backend/popups/destroy/',
            'editUrl' => '/backend/popups/edit/',
            'updateUrl' => '/backend/popups/update/',
            'updateActivePopupUrl' => '/backend/popups/updateActivePopup',
        ];

        $data['popups'] = encodeId($data['popups'], 'popup_id');

        return view('popup/index', $data);
    }

    // Ajax Method
    public function ajaxIndex()
    {
        return parent::index();
    }

    public function store()
    {
        $data = [
            'value' => '',
            'title' => $this->request->getVar('title'),
            'image_file' => $this->request->getFile('image'),
            'image_path' => 'img/',
            'image_context' => 'popup',
            'validation_options' => '|uploaded[image]'
        ];

        $this->setData($data);
        return parent::store();
    }

    public function edit($id = null)
    {
        return parent::edit($id);
    }

    public function update($id = null)
    {
        $id = base64_decode($this->request->getVar('id'));
        $oldImage = $this->request->getVar('oldImage');
        $newImage = $this->request->getFile('image');

        $data = [
            'value' => $oldImage,
            'popup_id' => $id,
            'title' => $this->request->getVar('title'),
        ];

        // ? If the client send image
        if ($newImage->getError() != 4) {
            $file = [
                'file' => $newImage,
                'file_old' => $oldImage,
                'file_path' => 'img/',
                'file_context' => 'popup'
            ];
            $data = array_merge($data, $file);
        }

        $this->setData($data);
        $this->setReturnRecentStoredData([
            'status' => true,
            'selected_fields' => 'title, value, status, popup_id'
        ]);

        return parent::update($id);
    }

    public function destroy($id = null)
    {
        $data = [
            'image_name' => $this->model->find(base64_decode($id))['value'],
            'image_path' => 'img/',
            'image_context' => 'Pop Up'
        ];

        $this->setData($data);
        $response = parent::destroy($id);
        $response['isActivePopupExist'] = $this->model->isActivePopupExist();
        return $this->response->setJSON($response);
    }

    public function updateActivePopup()
    {
        if (!$this->validate(['id' => 'required'])) {
            return $this->response->setJSON(_validate(['id' => 'required']));
        }

        if ($this->request->getVar('oldActivePopup')) {
            $oldActivePopupId = base64_decode($this->request->getVar('oldActivePopup'));
            $updateOldActivePopup = $this->model->save([
                'popup_id' => $oldActivePopupId,
                'status' => 'non_active'
            ]);
        } else {
            $updateOldActivePopup = true;
        }

        $newActivePopupId = base64_decode($this->request->getVar('id'));
        $updateNewActivePopup = $this->model->save([
            'popup_id' => $newActivePopupId,
            'status' => 'active'
        ]);

        if ($updateOldActivePopup && $updateNewActivePopup) {
            $updatedActivePopup = $this->model->select('popup_id, value, title')->find($newActivePopupId);
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
