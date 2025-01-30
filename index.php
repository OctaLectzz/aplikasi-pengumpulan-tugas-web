<?php
include 'src/boot/config.php';

session_start();

// Fungsi sederhana untuk routing
function route($path, $callback)
{
  // Cek jika URL yang diminta cocok dengan path, termasuk parameter query
  if ($_SERVER['REQUEST_URI'] === $path || strpos($_SERVER['REQUEST_URI'], $path . '?') === 0) {
    $callback();
    exit;
  }
}

// Define routes
route('/', function () {
  include 'src/pages/login.php';
});

route('/login', function () {
  include 'src/pages/login.php';
});

route('/register', function () {
  include 'src/pages/register.php';
});

route('/data-ruang', function () {
  if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
  }
  include 'src/pages/data-ruang.php';
});

route('/kode-referral', function () {
  if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
  }
  include 'src/pages/kode-referral.php';
});

route('/tambah-nilai', function () {
  if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
  }
  include 'src/pages/tambah-nilai.php';
});

route('/tugas-saya', function () {
  if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
  }
  include 'src/pages/tugas-saya.php';
});

route('/riwayat-tugas', function () {
  if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
  }
  include 'src/pages/riwayat-tugas.php';
});

// Default 404 Not Found
http_response_code(404);
echo "404 Page Not Found";
