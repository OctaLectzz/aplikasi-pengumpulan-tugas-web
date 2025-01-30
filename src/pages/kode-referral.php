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
$user_id = $user['id'];

if (isset($_GET['referral_code'])) {
  $referral_code = $_GET['referral_code'];

  // Get the referral_id using referral_code
  $query = "SELECT id, title FROM referrals WHERE referral_code = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('s', $referral_code);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Referral exists, fetch the referral_id
    $referral = $result->fetch_assoc();
    $referral_id = $referral['id'];

    // Get tasks along with user data based on referral_id
    $query = "SELECT tasks.id, users.nim, users.username, tasks.date, tasks.score 
                FROM tasks
                JOIN users ON tasks.user_id = users.id
                WHERE tasks.referral_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $referral_id);
    $stmt->execute();
    $task_result = $stmt->get_result();
    $tasks = $task_result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
  } else {
    echo "Kode referral tidak ditemukan.";
    header("Location: /data-ruang");
    exit;
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
      <h2 class="text-3xl font-semibold my-3"><i class="fas fa-code bg-black text-white p-3 rounded-lg text-2xl mx-3"></i> Kode Referral : <?= htmlspecialchars($referral_code); ?></h2>

      <!-- Alert -->
      <?php if (!empty($message)) : ?>
        <div class="text-sm <?= $status === 'success' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'; ?> p-4 rounded-lg">
          <?= htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <!-- Profile -->
      <div class="my-5">
        <p class="text-xl my-2">Nama : <?= htmlspecialchars($user['username']); ?></p>
        <p class="text-xl my-2">NIK : <?= htmlspecialchars($user['NIM']); ?></p>
        <p class="text-xl my-2">Judul : <?= htmlspecialchars($referral['title']); ?></p>
      </div>

      <!-- Table -->
      <table class="min-w-full divide-y divide-gray-200 border border-gray-400 my-5">
        <thead>
          <tr>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">No</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">NIM</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nama</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tanggal</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nilai</th>
            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($tasks)) : ?>
            <?php foreach ($tasks as $index => $task) : ?>
              <tr class="odd:bg-white even:bg-gray-100">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?= $index + 1; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($task['nim']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($task['username']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($task['date']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($task['score'] ?? '-'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium flex items-center">
                  <a href="/tambah-nilai?task_id=<?= urlencode($task['id']); ?>" class="cursor-pointer mx-1 inline-flex items-center gap-x-2 text-black-600 hover:text-black-800">
                    <i class="fas fa-circle-plus text-lg"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="6" class="text-center py-4 text-gray-600">Belum ada tugas di kode referral ini.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

    </div>
  </div>
</body>

</html>