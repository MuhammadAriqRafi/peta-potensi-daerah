<?php

namespace App\Controllers;

use App\Controllers\CRUDController;
use App\Models\Setting;

class SettingController extends CRUDController
{
    public function __construct()
    {
        parent::__construct(new Setting());
    }

    public function index()
    {
        $data = [
            'title' => 'Settings',
            'settings'  => $this->model->findAll(),
            'updateUrl' => '/backend/settings/update/',
        ];

        return view('setting/index', $data);
    }

    public function update($id = null)
    {
        // ? Decode $id
        $id = base64_decode($id);
        $setting = $this->model->select('keyword, value')->find($id);

        // ? Preparing the data
        $data = [
            'value' => $this->request->getVar('value'),
            'setting_id' => $id,
            'class' => '',
            'sort' => 0,
        ];

        // ? If the keyword is backsound, supply the data variable with the new file and old file
        if ($setting['keyword'] == 'backsound') {
            $backsound = $this->request->getFile('value');

            if ($backsound) {
                $file = [
                    'file' => $backsound,
                    'file_old' => $setting['value'],
                    'file_path' => 'file/',
                    'file_context' => 'setting',
                    'validation_options' => 'uploaded[value]|max_size[value,1024]',
                ];
                $data = array_merge($data, $file);
            } else {
                $data['value'] = $setting['value'];
            }
        }

        $this->setData($data);
        $this->setReturnRecentStoredData([
            'status' => true,
            'selected_fields' => 'keyword, value, setting_id, type'
        ]);
        return parent::update($id);
    }
}
