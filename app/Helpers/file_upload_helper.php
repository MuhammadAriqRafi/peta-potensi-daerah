<?php

if (!function_exists("storeAs")) {
    function storeAs($file, $path = 'img', $context = null): string
    {
        // Generate the file name and move to desired folder
        $fileName = date('YmjHis') . '_' . $context;
        $file->move($path, $fileName);
        return $fileName;
    }
}
