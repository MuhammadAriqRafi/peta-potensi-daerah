<?php

use App\Models\Guestbook;

if (!function_exists("countUnreadMessages")) {
    function countUnreadMessages(): int
    {
        $guestbooks = new Guestbook();
        return $guestbooks->where('status', 'unread')->countAllResults();
    }
};

if (!function_exists("setStatusRead")) {
    function setStatusRead($id = null): void
    {
        $guestbooks = new Guestbook();
        $guestbook = $guestbooks->find($id);
        if ($guestbook['status'] != 'read') $guestbooks->update($id, ['status' => 'read']);
    }
};
