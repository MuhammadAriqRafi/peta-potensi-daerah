<?php

use Config\Services;

if (!function_exists("editDeleteBtn")) {
    function editDeleteBtn($context = true): string
    {
        if ($context)
            return '
        <a href="#" class="btn btn-sm btn-outline-warning" onclick="update(`$id`)">Ubah</a>
        <a href="#" class="btn btn-sm btn-outline-danger" onclick="destroy(`$id`,`$context`)">Hapus</a>';
        else return '
        <a href="#" class="btn btn-sm btn-outline-warning" onclick="update(`$id`)">Ubah</a>
        <a href="#" class="btn btn-sm btn-outline-danger" onclick="destroy(`$id`)">Hapus</a>';
    }
}

if (!function_exists("_validate")) {
    function _validate($rules): array
    {
        $validation = Services::validation();

        $data = [];
        $data['status'] = false;
        $data['input_error'] = [];

        foreach ($rules as $key => $rule) {
            if ($validation->hasError($key)) {
                $data['input_error'][] = [
                    'input_name' => $key,
                    'error_message' => $validation->getError($key)
                ];
            }
        }

        return $data;
    }
}
