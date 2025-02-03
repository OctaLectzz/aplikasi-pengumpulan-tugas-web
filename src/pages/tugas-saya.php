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

$status = "";
$message = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve data from form
  $user_id = $_SESSION['user']['id'];
  $referral_code = $_POST['referral_code'];
  $date = $_POST['date'];
  $title = $_POST['title'];
  $answer = $_POST['answer'];
  $file_name = null;

  // Validate Referral Code
  $query = "SELECT * FROM referrals WHERE referral_code = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('s', $referral_code);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $referral = $result->fetch_assoc();

    // Check if the user has already used this referral code
    $check_user_task_query = "SELECT * FROM tasks WHERE user_id = ? AND referral_id = ?";
    $check_user_task_stmt = $conn->prepare($check_user_task_query);
    $check_user_task_stmt->bind_param('ii', $user_id, $referral['id']);
    $check_user_task_stmt->execute();
    $check_user_task_result = $check_user_task_stmt->get_result();

    if ($check_user_task_result->num_rows > 0) {
      // Referral code has already been used by the student
      $status = "failed";
      $message = "Anda sudah mengumpulkan tugas";
    } else {
      // Set Title Based on Referral Data
      $title = $referral['title'];

      // Process File Upload if present
      if (!empty($_FILES['file']['name'])) {
        $allowed_extensions = ['pdf', 'docx', 'jpg', 'png'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = basename($_FILES['file']['name']);
        $file_size = $_FILES['file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $upload_dir = 'public/tasks/';

        // Check File Extension
        if (!in_array($file_ext, $allowed_extensions)) {
          $status = "failed";
          $message = "Format file tidak diizinkan. Hanya PDF, DOCX, JPG, dan PNG yang diperbolehkan.";
        }
        // Check File Size
        elseif ($file_size > 5 * 1024 * 1024) {
          $status = "failed";
          $message = "Ukuran file terlalu besar. Maksimal 5MB.";
        } else {
          // Create Folder if Not Exists
          if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
          }

          // Create Unique File Name
          $new_file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $file_name);
          $file_path = $upload_dir . $new_file_name;

          // Move File to Upload Directory
          if (move_uploaded_file($file_tmp, $file_path)) {
            $file_name = $new_file_name;
          } else {
            $status = "failed";
            $message = "Gagal mengunggah file.";
          }
        }
      }

      // Save Data to Database
      if (empty($message)) {
        $insert_query = "INSERT INTO tasks (user_id, referral_id, date, title, answer, file, score, comment) VALUES (?, ?, ?, ?, ?, ?, NULL, NULL)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param('iissss', $user_id, $referral['id'], $date, $title, $answer, $file_name);

        if ($insert_stmt->execute()) {
          $status = "success";
          $message = "Tugas berhasil dikumpulkan.";
        } else {
          $status = "failed";
          $message = "Terjadi kesalahan saat mengirim tugas.";
        }
      }
    }
    $check_user_task_stmt->close();
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
  <title>Dashboard Mahasiswa</title>
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
      <h2 class="text-3xl font-semibold my-3"><i class="fas fa-circle-plus text-4xl mx-3"></i> Buat Tugas Baru</h2>

      <!-- Alert -->
      <?php if (!empty($message)) : ?>
        <div class="text-sm <?= $status === 'success' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'; ?> p-4 rounded-lg my-2">
          <?= htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>
      <div id="message-div">
        <?php if (!empty($_SESSION['message'])) : ?>
          <div class="text-sm <?= $status === 'success' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'; ?> p-4 rounded-lg my-2">
            <?= htmlspecialchars($_SESSION['message']); ?>
          </div>
          <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
      </div>

      <form method="POST" enctype="multipart/form-data" class="space-y-6 my-3">
        <div class="flex items-center space-x-4 my-8">
          <!-- Profile -->
          <div class="w-3/4">
            <p class="text-xl my-2">Nama : <?= htmlspecialchars($user['username']); ?></p>
            <p class="text-xl my-2">NIM : <?= htmlspecialchars($user['NIM']); ?></p>
          </div>

          <!-- Date -->
          <input type="date" id="date" name="date" class="w-1/4 px-4 py-1 mt-1 border rounded-sm focus:ring-blue-400" required>
        </div>

      <!-- Referral Code and Title in one row -->
        <!-- Referral Code -->
        <div class="flex items-center w-1/2 space-x-4">
          <label for="referral_code" class="text-sm font-medium text-gray-600 w-1/2">Kode Referral : </label>
          <input type="text" id="referral_code" name="referral_code" class="w-2/4 px-4 py-2 border rounded-sm" required>
          <button type="button" onclick="searchReferral()" class="cursor-pointer bg-blue-500 text-white py-2 px-6 rounded-sm hover:bg-blue-600">Cari</button>
        </div>

        <!-- Title -->
        <div class="flex items-center w-1/2 space-x-4">
          <label for="title" class="text-sm font-medium text-gray-600 w-1/2 ">Judul :</label>
          <input type="text" id="title" name="title" class="w-3/4 px-4 py-2 border rounded-sm" required readonly>
        </div>

        <!-- Answer -->
        <div class="relative my-8">
          <textarea id="answer" name="answer" rows="6" class="w-full px-4 py-2 border rounded-sm" required></textarea>
        </div>


        
        <div class="flex justify-end space-x-6 my-8">
          <!-- Upload File -->
          <div id="file-name" class="text-sm text-gray-600" style="display: none;"></div>


          <label for="file" class="cursor-pointer bg-blue-500 text-white py-2 px-6 rounded-sm hover:bg-blue-600">
            Upload File
          </label>
          <input type="file" id="file" name="file" class="hidden">

          <!-- Submit Button -->
          <button type="submit" class="cursor-pointer bg-blue-500 text-white py-2 px-6 rounded-sm hover:bg-blue-600">
            Selesai
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
  function searchReferral() {
  const referralCode = document.getElementById('referral_code').value;
  const messageDiv = document.getElementById('message-div');
  const dateInput = document.getElementById('date');

  if (referralCode) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'src/boot/check-referral.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
      if (xhr.readyState == 4) {
        if (xhr.status == 200) {
          try {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
              document.getElementById('title').value = response.title;
              dateInput.value = response.date;
              messageDiv.innerHTML = `<div class="text-sm text-green-600 bg-green-100 p-4 rounded-lg">Referral ditemukan! Judul otomatis terisi dan tanggal diperbarui.</div>`;
            } else {
              messageDiv.innerHTML = `<div class="text-sm text-red-600 bg-red-100 p-4 rounded-lg">${response.message}</div>`;
            }
          } catch (e) {
            messageDiv.innerHTML = `<div class="text-sm text-red-600 bg-red-100 p-4 rounded-lg">Terjadi kesalahan: Response tidak valid.</div>`;
          }
        } else {
          messageDiv.innerHTML = `<div class="text-sm text-red-600 bg-red-100 p-4 rounded-lg">Terjadi kesalahan saat memproses permintaan.</div>`;
        }
      }
    };
    xhr.send('referral_code=' + encodeURIComponent(referralCode));
  } else {
    messageDiv.innerHTML = `<div class="text-sm text-red-600 bg-red-100 p-4 rounded-lg">Masukkan kode referral terlebih dahulu.</div>`;
  }
}

document.getElementById('file').addEventListener('change', function (event) {
  const fileNameInput = document.getElementById('file-name');
  const fileInput = event.target;

  // Jika file dipilih
  if (fileInput.files && fileInput.files[0]) {
    const fileName = fileInput.files[0].name;
    fileNameInput.textContent = fileName;  // Update nama file
    fileNameInput.style.display = "block";  // Tampilkan nama file
  } else {
    fileNameInput.textContent = '';  // Clear nama file
    fileNameInput.style.display = "none";  // Sembunyikan jika tidak ada file yang dipilih
  }
});


  </script>
</body>

</html>