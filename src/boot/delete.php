<?php
include './config.php';

// Cek apakah ada ID yang dikirimkan melalui POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
  $referral_id = $_POST['id'];

  // Query untuk menghapus data berdasarkan ID
  $query = "DELETE FROM referrals WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $referral_id);

  if ($stmt->execute()) {
    $message = "Kode referral berhasil dihapus!";
    $status = 'success';
  } else {
    $message = "Gagal menghapus kode referral.";
    $status = 'failed';
  }

  $stmt->close();
  $conn->close();

  // Mengarahkan kembali ke halaman sebelumnya setelah operasi selesai
  header("Location: /data-ruang");
  exit();
}
