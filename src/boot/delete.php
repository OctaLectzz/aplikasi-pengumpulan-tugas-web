<?php
include './config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
  $referral_id = $_POST['id'];

  // Hapus semua tasks yang terkait
  $deleteTasksQuery = "DELETE FROM tasks WHERE referral_id = ?";
  $stmtTasks = $conn->prepare($deleteTasksQuery);
  $stmtTasks->bind_param('i', $referral_id);
  $stmtTasks->execute();
  $stmtTasks->close();

  // Hapus referral setelah semua tasks dihapus
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

  header("Location: /data-ruang");
  exit();
}
