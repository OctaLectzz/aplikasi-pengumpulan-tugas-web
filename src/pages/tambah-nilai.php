<?php
include 'src/boot/config.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user'])) {
  header('Location: /login');
  exit;
}
if ($_SESSION['role'] != "Dosen") {
  header('Location: /tugas-saya');
  exit;
}
$user = $_SESSION['user'];

if (isset($_GET['task_id'])) {
  $task_id = $_GET['task_id'];

  // Query to get task details based on task_id
  $query = "SELECT tasks.id, users.nim, users.username, tasks.date, referrals.referral_code, tasks.title, tasks.score, tasks.comment, tasks.file 
  FROM tasks 
  JOIN users ON tasks.user_id = users.id 
  JOIN referrals ON tasks.referral_id = referrals.id 
  WHERE tasks.id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $task_id);
  $stmt->execute();
  $task_result = $stmt->get_result();

  if ($task_result->num_rows > 0) {
    $task = $task_result->fetch_assoc();
    $stmt->close();
  } else {
    echo "Task tidak ditemukan.";
    header("Location: /data-ruang");
    exit;
  }

  // Handle the form submission to update score and comment
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $score = $_POST['score'];
    $comment = $_POST['comment'];

    // Update the task with the new score and comment
    $update_query = "UPDATE tasks SET score = ?, comment = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ssi', $score, $comment, $task_id);

    if ($update_stmt->execute()) {
      $message = "Nilai dan komentar berhasil diperbarui!";
      $status = 'success';
      header("Location: /kode-referral?referral_code=" . urlencode($task['referral_code']));
      exit();
    } else {
      $message = "Gagal memperbarui nilai dan komentar.";
      $status = 'failed';
    }
  }
} else {
  header("Location: /data-ruang");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Dosen</title>
  <link rel="stylesheet" href="public/css/style.css">
  <link rel="stylesheet" href="public/css/font-awesome/css/all.min.css">
</head>

<body class="bg-gray-100">
  <div class="flex">
    <!-- Sidebar -->
    <div class="w-1/4 bg-gray-800 min-h-screen text-white flex flex-col">
      <ul class="flex-1">
        <li><a href="/data-ruang" class="block py-4 pl-15 border border-gray-700 hover:bg-gray-500"><i class="fas fa-code bg-black text-white p-1 rounded-sm mx-1"></i> Data Ruang</a></li>
      </ul>

      <div class="mt-auto m-10">
        <a href="/src/boot/logout.php" class="block text-center py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-sm">
          Keluar
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <h2 class="text-3xl font-semibold my-3"><i class="fas fa-circle-plus text-4xl mx-3"></i> Tambah Nilai</h2>

      <!-- Alert -->
      <?php if (!empty($message)) : ?>
        <div class="text-sm <?= $status === 'success' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'; ?> p-4 rounded-lg">
          <?= htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="space-y-6 my-3">
        <div class="flex items-center space-x-4 my-8">
          <!-- Profile -->
          <div class="w-3/4">
            <p class="text-xl my-2">Nama : <?= htmlspecialchars($task['username']); ?></p>
            <p class="text-xl my-2">NIM : <?= htmlspecialchars($task['nim']); ?></p>
            <p class="text-xl my-2">Kode Referral : <?= htmlspecialchars($task['referral_code']); ?></p>
          </div>

          <!-- Date -->
          <p class="w-1/4 text-lg  my-2">Tanggal : <?= htmlspecialchars($task['date']); ?></p>
        </div>

        <!-- Score -->
        <div class="float-right flex items-center space-x-4">
          <label for="score" class="text-sm font-medium text-gray-600 w-1/4">Nilai : </label>
          <input type="number" id="score" name="score" value="<?= htmlspecialchars($task['score']); ?>" placeholder="Masukkan Nilai" class="w-3/4 px-4 py-2 border rounded-sm" required max="100" oninput="validateScore(this)">
        </div>


        <!-- Title -->
        <p class="w-2/4 text-lg">Judul : <?= htmlspecialchars($task['title']); ?></p>

        <!-- Comment -->
        <div class="relative my-8">
          <textarea id="comment" name="comment" rows="6" class="w-full px-4 py-2 border rounded-sm" required><?= htmlspecialchars($task['comment']); ?></textarea>
        </div>

        <div class="flex justify-end space-x-6 my-8">
          <!-- Download File -->
          <?php if (!empty($task['file']) && file_exists('public/tasks/' . $task['file'])): ?>
            <a href="/public/tasks/<?= urlencode($task['file']); ?>" download class="cursor-pointer bg-blue-500 text-white py-2 px-8 rounded-sm hover:bg-blue-600">
              Download File
            </a>
          <?php endif; ?>

          <!-- Upload File -->
          <a href="/kode-referral?referral_code=<?= urlencode($task['referral_code']); ?>" class="cursor-pointer bg-gray-500 text-white py-2 px-8 rounded-sm hover:bg-gray-600">
            Batal
          </a>

          <!-- Submit Button -->
          <button type="submit" class="cursor-pointer bg-green-500 text-white py-2 px-6 rounded-sm hover:bg-green-600">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
  <script>
  function validateScore(input) {
    if (input.value > 100) {
      input.setCustomValidity('Nilai tidak boleh lebih dari 100');
    } else {
      input.setCustomValidity('');
    }
  }
</script>
</body>

</html>