<?php
include 'src/boot/config.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user'])) {
  header('Location: /login');
  exit;
}
if ($_SESSION['role'] != "Mahasiswa") {
  header('Location: /data-ruang');
  exit;
}

$user = $_SESSION['user'];

$id = isset($_GET['id']) ? $_GET['id'] : null;

$status = "";
$message = "";

if ($id) {
  $query = "SELECT * FROM tasks WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $task = $result->fetch_assoc();

    $referral_id = $task['referral_id'];

    $referral_query = "SELECT referral_code FROM referrals WHERE id = ?";
    $referral_stmt = $conn->prepare($referral_query);
    $referral_stmt->bind_param('i', $referral_id);
    $referral_stmt->execute();
    $referral_result = $referral_stmt->get_result();

    if ($referral_result->num_rows > 0) {
      $referral = $referral_result->fetch_assoc();
      $referral_code = $referral['referral_code'];
    } else {
      $status = "failed";
      $message = "Kode Referral tidak ditemukan di table referrals.";
    }

    $date = $task['date'];
    $title = $task['title'];
    $answer = $task['answer'];
    $file_name = $task['file'];
  } else {
    $status = "failed";
    $message = "Data tugas tidak ditemukan.";
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user']['id'];
  $referral_code = $_POST['referral_code'];
  $date = $_POST['date'];
  $title = $_POST['title'];
  $answer = $_POST['answer'];
  $file_name = null;

  $query = "SELECT * FROM referrals WHERE referral_code = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('s', $referral_code);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $referral = $result->fetch_assoc();

    $title = $referral['title'];

    if (!empty($_FILES['file']['name'])) {
      $allowed_extensions = ['pdf', 'docx', 'jpg', 'png'];
      $file_tmp = $_FILES['file']['tmp_name'];
      $file_name = basename($_FILES['file']['name']);
      $file_size = $_FILES['file']['size'];
      $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
      $upload_dir = 'public/tasks/';

      if (!in_array($file_ext, $allowed_extensions)) {
        $status = "failed";
        $message = "Format file tidak diizinkan. Hanya PDF, DOCX, JPG, dan PNG yang diperbolehkan.";
      }
      elseif ($file_size > 5 * 1024 * 1024) {
        $status = "failed";
        $message = "Ukuran file terlalu besar. Maksimal 5MB.";
      } else {
        if (!file_exists($upload_dir)) {
          mkdir($upload_dir, 0777, true);
        }

        $new_file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $file_name);
        $file_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
          $file_name = $new_file_name;
        } else {
          $status = "failed";
          $message = "Gagal mengunggah file.";
        }
      }
    }

    if (empty($message)) {
        $update_query = "UPDATE tasks SET answer = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('si', $answer, $id);
        $update_stmt->execute();
        

      if ($update_stmt->execute()) {
        $status = "success";
        $message = "Tugas berhasil diperbarui.";
      } else {
        $status = "failed";
        $message = "Terjadi kesalahan saat memperbarui tugas.";
      }
    }
  } else {
    $status = "failed";
    $message = "Kode Referral tidak ditemukan.";
  }

  $stmt->close();
  $conn->close();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Tugas</title>
  <link rel="stylesheet" href="public/css/style.css">
  <link rel="stylesheet" href="public/css/font-awesome/css/all.min.css">
</head>

<body class="bg-gray-100">
  <div class="flex">
    <!-- Sidebar -->
    <div class="w-1/4 bg-gray-800 min-h-screen text-white flex flex-col">
      <ul class="flex-1">
        <li><a href="/tugas-saya" class="block py-4 pl-15 border border-gray-700 hover:bg-gray-500"><i class="fas fa-circle-plus mx-1"></i> Tugas Saya</a></li>
        <li><a href="/riwayat-tugas" class="block py-4 pl-15 border border-gray-700 hover:bg-gray-500"><i class="fas fa-book mx-1"></i> Riwayat Tugas</a></li>
      </ul>

      <div class="mt-auto m-10">
        <a href="/src/boot/logout.php" class="block text-center py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-sm">
          Keluar
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <h2 class="text-3xl font-semibold my-3"><i class="fas fa-circle-plus text-4xl mx-3"></i> Edit Tugas</h2>

      <!-- Alert -->
      <?php if (!empty($message)) : ?>
        <div class="text-sm <?= $status === 'success' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'; ?> p-4 rounded-lg my-2">
          <?= htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="space-y-6 my-3">
        <div class="flex items-center space-x-4 my-8">
          <!-- Profile -->
          <div class="w-3/4">
            <p class="text-xl my-2">Nama : <?= htmlspecialchars($user['username']); ?></p>
            <p class="text-xl my-2">NIM : <?= htmlspecialchars($user['NIM']); ?></p>
          </div>

          <!-- Date -->
          <input disabled    type="date" id="date" name="date" class="w-1/4 px-4 py-1 mt-1 border rounded-sm focus:ring-blue-400" value="<?= htmlspecialchars($date); ?>" required>
        </div>

        <!-- Referral Code -->
        <div class="flex items-center space-x-4 my-8">
          <label for="referral_code" class="text-sm font-medium text-gray-600 w-1/4">Kode Referral : </label>
          <input disabled type="text" id="referral_code" name="referral_code" class="w-3/4 px-4 py-2 border rounded-sm" value="<?= htmlspecialchars($referral_code); ?>" required>
        </div>

        <!-- Title -->
        <div class="flex items-center space-x-4 my-8">
          <label for="title" class="text-sm font-medium text-gray-600 w-1/4">Judul : </label>
          <input disabled type="text" id="title" name="title" class="w-3/4 px-4 py-2 border rounded-sm" value="<?= htmlspecialchars($title); ?>" required readonly>
        </div>

        <!-- Answer -->
        <div class="relative my-8">
          <textarea id="answer" name="answer" rows="6" class="w-full px-4 py-2 border rounded-sm" required><?= htmlspecialchars($answer); ?></textarea>
        </div>

        <div class="flex justify-end space-x-6 my-8">
          <!-- Upload File -->
          <label for="file" class="cursor-pointer bg-blue-500 text-white py-2 px-6 rounded-sm hover:bg-blue-600">
            Upload File
          </label>
          <input type="file" id="file" name="file" class="hidden">

          <!-- Submit Button -->
          <button type="submit" class="cursor-pointer bg-blue-500 text-white py-2 px-6 rounded-sm hover:bg-blue-600">
            Update
          </button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>
