<?php

if (!function_exists('get_avatar_src')) {
    function get_avatar_src(array $data): ?string
    {
        $local = $data['avatar'] ?? $data['userAvatar'] ?? null;
        if (!empty($local)) {
            return base_url('uploads/avatars/' . $local);
        }
        $google = $data['google_avatar'] ?? $data['userGoogleAvatar'] ?? null;
        if (!empty($google)) {
            return $google;
        }
        return null;
    }
}
