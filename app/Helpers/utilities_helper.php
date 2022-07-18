<?php

use Config\Services;

if (!function_exists("editDeleteBtn")) {
    function editDeleteBtn($context = true): string
    {
        if ($context)
            return '
        <button class="btn btn-sm btn-outline-warning" onclick="edit(`$id`)">Ubah</button>
        <button class="btn btn-sm btn-outline-danger" onclick="destroy(`$id`,`$context`)">Hapus</button>';
        else return '
        <button class="btn btn-sm btn-outline-warning" onclick="edit(`$id`)">Ubah</button>
        <button class="btn btn-sm btn-outline-danger" onclick="destroy(`$id`)">Hapus</button>';
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

if (!function_exists("encodeId")) {
    function encodeId($records, $idFieldName): array
    {
        foreach ($records as $key => $record) {
            $records[$key][$idFieldName] = base64_encode($record[$idFieldName]);
        }

        return $records;
    }
}
