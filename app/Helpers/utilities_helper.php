<?php

if (!function_exists("editDeleteBtn")) {
    function editDeleteBtn(): string
    {
        return '
        <a href="#" class="btn btn-sm btn-outline-warning" onclick="update(`$id`)">Edit</a>
        <a href="#" class="btn btn-sm btn-outline-danger" onclick="destroy(`$id`,`$context`)">Delete</a>';
    }
}
