<?php

// 1. Inisialisasi database SQLite di folder writable Vercel
$db = '/tmp/database.sqlite';
if (!file_exists($db)) {
    touch($db);
}

// 2. Load file index Laravel yang asli
require __DIR__ . '/../public/index.php';