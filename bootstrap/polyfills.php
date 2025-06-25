<?php
// Polyfills for removed PHP functions used by legacy libraries (e.g. FPDF)
if (!function_exists('get_magic_quotes_runtime')) {
    function get_magic_quotes_runtime()
    {
        return false;
    }
}

if (!function_exists('set_magic_quotes_runtime')) {
    function set_magic_quotes_runtime($newSetting)
    {
        // No-op – magic quotes removed from PHP 8
        return false;
    }
} 