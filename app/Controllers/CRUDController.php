<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CRUDController extends BaseController
{
    // TODO: store and update method more or less similar, consider merging them in one method
    protected object $model;
    protected bool $returnRecentStoredData = false;
    protected array $data = [];

    protected function __construct($model)
    {
        $this->model = $model;
    }

    // Accessor
    protected function setData($data)
    {
        $this->data = $data;
    }

    protected function setReturnRecentStoredData($returnRecentStoredData)
    {
        $this->returnRecentStoredData = $returnRecentStoredData;
    }

    private function resetClassProperty()
    {
        $this->returnRecentStoredData = false;
        $this->data = [];
    }

    // CRUD
    protected function ajaxIndex()
    {
        helper('utilities');
        $draw = $this->request->getVar('draw');
        $start = $this->request->getVar('start');
        $length = $this->request->getVar('length');
        $search = $this->request->getVar('search')['value'];
        $total = $this->model->getTotalRecords();

        // ? Preparing response
        $response = [
            'length' => $length,
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ];

        // ? If client search something
        if ($search != '') {
            $list = $this->model->getRecordSearch($search, $start, $length);
            $total_search = $this->model->getTotalRecordSearch($search);
            $response = [
                'recordsTotal' => $total_search,
                'recordsFiltered' => $total_search
            ];
        } else $list = $this->model->getRecords($start, $length);

        // ? Encode id
        foreach ($list as $key => $value) {
            $list[$key][$this->model->primaryKey] = base64_encode($value[$this->model->primaryKey]);
        }

        $response['data'] = $list;

        return $this->response->setJSON($response);
    }

    protected function ajaxStore()
    {
        // ? Validation
        $rules = $this->model->fetchValidationRules();

        if (!$this->validate($rules)) {
            return $this->response->setJSON(_validate($rules));
        }

        // ? Preparing response
        $response = [
            'status' => true,
            'message' => 'berhasil ditambahkan'
        ];

        // ? Saving data
        if ($this->model->save($this->data)) {
            if ($this->returnRecentStoredData) {
                $response['data'] = $this->model->find($this->model->getInsertID());
            }
            $this->resetClassProperty();
            return $this->response->setJSON($response);
        } else {
            $response['status'] = false;
            $response['message'] = 'gagal ditambahkan';

            return $this->response->setJSON($response);
        }
    }

    protected function ajaxDestroy($id = null)
    {
        $id = base64_decode($id);
        if ($this->data) $message = deleteImage($this->data['image_name'], $this->data['image_path'], $this->data['image_context']);
        else $message = ucfirst($this->model->table) . ' berhasil dihapus';

        if ($this->model->delete($id)) {
            $this->resetClassProperty();
            return json_encode([
                'message' => $message,
                'idDeletedPopup' => base64_encode($id)
            ]);
        } else {
            return $this->response->setJSON(['message' => 'Terjadi kesalahan pada server']);
        }
    }

    protected function ajaxEdit($id = null)
    {
        $data = $this->model->find(base64_decode($id));
        return $this->response->setJSON($data);
    }

    protected function ajaxUpdate($id = null)
    {
        // ? Validation
        $rules = $this->model->fetchValidationRules();

        if (!$this->validate($rules)) {
            return $this->response->setJSON(_validate($rules));
        }

        // ? Preparing response
        $response = [
            'status' => true,
            'message' => ucfirst($this->model->table) . ' berhasil ditambahkan'
        ];

        // ? Updating data
        if ($this->administrators->save($this->data)) {
            $this->resetClassProperty();
            return $this->response->setJSON($response);
        } else {
            $response['status'] = false;
            $response['message'] = 'gagal ditambahkan';

            return $this->response->setJSON($response);
        };
    }
}
