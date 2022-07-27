<?php

if (!function_exists("storeAs")) {
    function storeAs($file, $path = 'img/', $context = null)
    {
        helper('text');

        if ($file->getError() != 4) {
            // Generate the file name and move to desired folder
            $fileName = date('YmjHis') . '_' . random_string('alnum', 4) . '_' . $context;
            $file->move($path, $fileName);
            return $fileName;
        } else return $fileName = null;
    }
}
