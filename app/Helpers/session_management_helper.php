<?php

use App\Models\Administrator;
use App\Models\Visitor;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

if (!function_exists("validateJWT")) {
    function validateJWT(string $jwt): bool
    {
        try {
            $payload = JWT::decode($jwt, new Key(Administrator::$SECRET_KEY, 'HS256'));
            $administratorModel = new Administrator();
            $administrators = $administratorModel->select('status')
                ->where('status', $payload->session_id)
                ->get()->getRowArray();

            if ($administrators) return true;
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

if (!function_exists("getJWTPayload")) {
    function getJWTPayload(string $jwt): object
    {
        try {
            $payload = JWT::decode($jwt, new Key(Administrator::$SECRET_KEY, 'HS256'));
            return $payload;
        } catch (\Throwable $th) {
            return [];
        }
    }
}
