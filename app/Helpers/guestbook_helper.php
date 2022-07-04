<?php

use App\Models\Guestbook;

if (!function_exists("countUnreadMessages")) {
    function countUnreadMessages()
    {
        $guestbooks = new Guestbook();
        return $guestbooks->where('status', 'unread')->countAllResults();
    }
}
