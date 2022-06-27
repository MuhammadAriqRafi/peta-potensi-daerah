<?php

use App\Models\Visitor;
use Config\Services;

if (!function_exists("checkSessionId")) {
    function checkSessionId()
    {
        $visitor = new Visitor();
        $clientIP = Services::request()->getIPAddress();
        $clientUserAgent = Services::request()->getUserAgent()->getAgentString();

        // ? Check if visitor session id available or not, if false, insert the data to visitors table
        if (!$visitor->hasSessionId(session_id())) {
            $visitor->save([
                'session_id' => session_id(),
                'ip' => $clientIP,
                'user_agent' => $clientUserAgent
            ]);
        }
    }
}
