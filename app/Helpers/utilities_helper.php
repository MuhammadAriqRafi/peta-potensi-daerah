<?php

use Config\Services;


if (!function_exists("editDeleteBtn")) {
    function editDeleteBtn(bool $context = true): string
    {
        if ($context)
            return '
        <button class="btn btn-sm btn-secondary" onclick="edit(`$id`)">Ubah</button>
        <button class="btn btn-sm btn-error" onclick="destroy(`$id`,`$context`)">Hapus</button>';
        else return '
        <button class="btn btn-sm btn-secondary" onclick="edit(`$id`)">Ubah</button>
        <button class="btn btn-sm btn-error" onclick="destroy(`$id`)">Hapus</button>';
    }
}

if (!function_exists("_validate")) {
    function _validate(array $rules): array
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
    function encodeId($records, string $idFieldName): array
    {
        foreach ($records as $key => $record) {
            $records[$key][$idFieldName] = base64_encode($record[$idFieldName]);
        }

        return $records;
    }
}

if (!function_exists("deleteImage")) {
    function deleteImage(string $imageName, string $folderPath = 'img/', string $context = null): string
    {
        $message = ucfirst($context) . ' berhasil dihapus';

        try {
            unlink($folderPath . $imageName);
        } catch (\Throwable $th) {
            return $message = $th->getMessage();
        }

        return $message;
    }
}

if (!function_exists("getCurrentEpochTime")) {
    function getCurrentEpochTime()
    {
        return $milliseconds = round(microtime(true) * 1000);
    }
}

if (!function_exists("convertEpochTimeToDate")) {
    function convertEpochTimeToDate($epochTime)
    {
        return date("Y-m-d H:i:s", substr($epochTime, 0, 10));
    }
}
