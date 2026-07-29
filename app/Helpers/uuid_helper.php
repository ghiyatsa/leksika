<?php

/**
 * UUID Helper
 *
 * Generates a cryptographically secure UUID v4 using random_bytes().
 * Safe replacement for mt_rand-based generation.
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
