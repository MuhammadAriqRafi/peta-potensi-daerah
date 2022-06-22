<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdministratorController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Administrators'
        ];

        return view('administrator/index', $data);
    }
}
