<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CRUDController extends BaseController
{
    protected object $model;
    protected $returnRecentStoredData = false;
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
    /*
        ! The model should implement DatatableInterface
        ! The model should implement CRUDInterface
    */
    protected function index()
    {
        helper('utilities');
        $draw = $this->request->getVar('draw');
        $start = $this->request->getVar('start');
        $length = $this->request->getVar('length');
        $search = $this->request->getVar('search')['value'];
        $orderDirection = $this->request->getVar('order')[0]['dir'];
        $orderColumnIndex = $this->request->getVar('order')[0]['column'];
        $orderColumn = $this->request->getVar('columns')[$orderColumnIndex]['name'];
        $total = $this->model->getTotalRecords();

        // ? Preparing response
        $response = [
            'length' => $length,
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        ];

        // ? If client search something
        if ($search != '') {
            $list = $this->model->getRecordSearch($search, $start, $length, $orderColumn, $orderDirection);
            $total_search = $this->model->getTotalRecordSearch($search);
            $response['recordsFiltered'] = $total_search;
        } else $list = $this->model->getRecords($start, $length, $orderColumn, $orderDirection);

        // ? Encode id
        foreach ($list as $key => $value) {
            $list[$key][$this->model->primaryKey] = base64_encode($value[$this->model->primaryKey]);
        }

        $response['data'] = $list;

        return $this->response->setJSON($response);
    }

    /*
        Available Properties:
        * $this->data['image_name']: string
        * $this->data['image_path']: string
        * $this->data['image_context']: string
        * $this->data['validation_options'] : string
        * returnRecentStoredData: bool
    */
    protected function store()
    {
        // ? Validation
        $rules = $this->model->fetchValidationRules($this->data['validation_options'] ?? null);

        if (!$this->validate($rules)) {
            return $this->response->setJSON(_validate($rules));
        }

        // ? Remove the validation_options if after validation passed, the validation_options exist
        if (array_key_exists('validation_options', $this->data)) {
            unset($this->data['validation_options']);
        }

        // ? Preparing response
        $response = [
            'status' => true,
            'message' => ucfirst($this->model->table) . ' berhasil ditambahkan'
        ];

        // ? Check if any image needs to be stored
        // ? Always put the field for the image in the first index of data variable
        if (array_key_exists('image_file', $this->data)) {
            $this->data[array_key_first($this->data)] = storeAs($this->data['image_file'], $this->data['image_path'], $this->data['image_context']);
            unset($this->data['image_file']);
            unset($this->data['image_path']);
            unset($this->data['image_context']);
        }

        // ? Saving data
        if ($this->model->save($this->data)) {
            if ($this->returnRecentStoredData['status'] ?? $this->returnRecentStoredData) {
                $response['data'] = $this->model->select($this->returnRecentStoredData['selected_fields'] ?? '*')->find($this->model->getInsertID());
                $response['data'][$this->model->primaryKey] = base64_encode($response['data'][$this->model->primaryKey]);
            }

            $this->resetClassProperty();
            return $this->response->setJSON($response);
        } else {
            $response['status'] = false;
            $response['message'] = 'gagal ditambahkan';

            return $this->response->setJSON($response);
        }
    }

    /*
        Available Properties:
        * $this->data['image_name']: string
        * $this->data['image_path']: string
        * $this->data['image_context']: string
    */
    protected function destroy($id = null)
    {
        $id = base64_decode($id);

        // ? Check if deleted record has image
        if (array_key_exists('image_name', $this->data)) $message = deleteImage($this->data['image_name'], $this->data['image_path'], $this->data['image_context']);
        else $message = ucfirst($this->model->table) . ' berhasil dihapus';

        // ? Preparing response
        $response = [
            'status' => true,
            'message' => $message
        ];

        if ($this->model->delete($id)) {
            $this->resetClassProperty();
            return $response;
        } else {
            $response['status'] = false;
            $response['message'] = 'Terjadi kesalahan pada server';

            return $response;
        }
    }

    /*
        Available Properties:
        * $this->data['select']: array
    */
    protected function edit($id = null)
    {
        // ? Decode $id
        $id = base64_decode($id);

        // ? If you only want to get some fields in the record
        if ($this->data['select'] ?? false) {
            $data = $this->model->select($this->data['select'])->find($id);
        } else {
            $data = $this->model->find($id);
        }

        $data[$this->model->primaryKey] = base64_encode($data[$this->model->primaryKey]);
        $this->resetClassProperty();
        return $this->response->setJSON($data);
    }

    /*
        Available Properties:
        * $this->data['file']: string
        * $this->data['file_path']: string
        * $this->data['file_context']: string
        * returnRecentStoredData: bool
        * returnRecentStoredData['status']: bool
        * returnRecentStoredData['selected_fields']: string
    */
    protected function update($id = null)
    {
        // ? Validation
        $rules = $this->model->fetchValidationRules($this->data['validation_options'] ?? null);

        if (!$this->validate($rules)) {
            return $this->response->setJSON(_validate($rules));
        }

        // ? Remove the validation_options if after validation passed, the validation_options exist
        if (array_key_exists('validation_options', $this->data)) {
            unset($this->data['validation_options']);
        }

        // ? Preparing response
        $response = [
            'status' => true,
            'message' => ucfirst($this->model->table) . ' berhasil diubah',
        ];

        // ? Check if any image needs to be stored
        // ? Always put the field for the image in the first index of data variable
        if (array_key_exists('file', $this->data)) {
            if ($this->data['file_old']) deleteImage($this->data['file_old'], $this->data['file_path'], $this->data['file_context']);
            $this->data[array_key_first($this->data)] = storeAs($this->data['file'], $this->data['file_path'], $this->data['file_context']);

            unset($this->data['file']);
            unset($this->data['file_old']);
            unset($this->data['file_path']);
            unset($this->data['file_context']);
        }

        // ? Updating data
        if ($this->model->save($this->data)) {
            if ($this->returnRecentStoredData['status'] ?? $this->returnRecentStoredData) {
                $response['data'] = $this->model->select($this->returnRecentStoredData['selected_fields'] ?? '*')->find($id);
                $response['data'][$this->model->primaryKey] = base64_encode($response['data'][$this->model->primaryKey]);
            }

            $this->resetClassProperty();
            return $this->response->setJSON($response);
        } else {
            $response['status'] = false;
            $response['message'] = ucfirst($this->model->table) . 'gagal diubah';

            return $this->response->setJSON($response);
        };
    }
}
