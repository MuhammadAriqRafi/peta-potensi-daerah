<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Setting;

class SettingController extends BaseController
{
    protected $settings;

    public function __construct()
    {
        $this->settings = new Setting();
    }

    public function index()
    {
        $data = [
            'title' => 'Settings',
            'settings'  => $this->settings->findAll(),
        ];

        return view('setting/index', $data);
    }

    public function ajaxUpdate($id = null)
    {
        $id = base64_decode($id);
        $setting = $this->settings->select('keyword, value')->find($id);

        // TODO: Get csrf token from header for the sake of curiousity
        if ($setting['keyword'] == 'backsound') {
            // Set rules to the file
            $backsound = $this->request->getFile('value');
            $rules = $this->settings->getSettingValidationRules('uploaded[value]|max_size[value,1024]|is_image[value]|mime_in[value,image/jpg,image/jpeg,image/png]');
        } else $rules = $this->settings->getSettingValidationRules();

        // Validate the input
        if (!$this->validate($rules)) {
            return $this->response->setJSON([_validate($rules), $this->request->headers()]);
        }

        // Store the file (if input is file)
        if ($setting['keyword'] == 'backsound') {
            try {
                unlink('file/' . $setting['value']);
                $backsoundName = storeAs($backsound, 'file', 'setting');
            } catch (\Throwable $th) {
                return $this->response->setJSON($th->getMessage());
            }
        }

        // Preparing the data
        $data = [
            'setting_id' => $id,
            'value' => $this->request->getVar('value') ?? $backsoundName,
            'class' => '',
            'sort' => 0
        ];

        if ($this->settings->save($data)) {
            $data = $this->settings->select('value, type')->find($id);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Setting berhasil diubah',
                'data' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Setting gagal diubah'
            ]);
        }
    }
}
