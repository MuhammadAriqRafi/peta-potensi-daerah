<?php

if (!function_exists("storeAs")) {
    function storeAs($image): string
    {
        // Generate the image name and move to public/img
        $imageName = date('YmjHis') . '_' . 'popup';
        $image->move('img', $imageName);
        return $imageName;
    }
}
