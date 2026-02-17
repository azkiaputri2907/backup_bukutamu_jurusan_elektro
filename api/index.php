<?php

// 1. Inisialisasi file database di folder sementara Vercel
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
}

// 2. Jalankan Laravel melalui public/index.php
require __DIR__ . '/../public/index.php';