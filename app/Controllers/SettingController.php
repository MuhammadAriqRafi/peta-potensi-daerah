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

    public function edit($id = null)
    {
    }

    public function update($id = null)
    {
    }
}
