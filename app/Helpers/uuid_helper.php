<?php

/**
 * UUID Helper
 *
 * Membangkitkan UUID v4 yang aman secara kriptografis menggunakan random_bytes().
 * Pengganti aman untuk pembangkitan berbasis mt_rand.
 */

if (! function_exists('generate_uuid')) {
    function generate_uuid(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant RFC 4122

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
