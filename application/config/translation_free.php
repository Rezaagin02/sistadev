<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['translator_free'] = [
    // daftar public instance LibreTranslate (tanpa API key)
    'libre_endpoints' => [
        'https://libretranslate.com',        // utama
        'https://translate.astian.org',      // backup umum
    ],
    'timeout'      => 12,   // detik
    'default_to'   => 'en',
    'default_from' => 'auto', // 'auto' biar LT deteksi bahasa
    'use_mymemory_fallback' => true, // true = fallback jika LT gagal
];
