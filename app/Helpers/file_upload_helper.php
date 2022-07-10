<?php

if (!function_exists("storeAs")) {
    function storeAs($file, $path = 'img/', $context = null): string
    {
        helper('text');

        // Generate the file name and move to desired folder
        $fileName = date('YmjHis') . '_' . random_string('alnum', 4) . '_' . $context;
        $file->move($path, $fileName);
        return $fileName;
    }
}
