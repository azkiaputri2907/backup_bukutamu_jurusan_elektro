<?php

// 1. Paksa PHP menampilkan error ke layar browser
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Pastikan Laravel tidak mencoba menulis log ke file yang dilarang
putenv('LOG_CHANNEL=stderr');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');

// 3. Jalankan Laravel
require __DIR__ . '/../public/index.php';