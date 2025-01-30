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
$user_id = $user['id'];

// Ambil data tugas dari database
$query = "SELECT tasks.id, tasks.title, tasks.date, referrals.referral_code, tasks.answer, tasks.score 
          FROM tasks 
          JOIN referrals ON tasks.referral_id = referrals.id 
          WHERE tasks.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
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
      <h2 class="text-3xl font-semibold my-3"><i class="fas fa-book text-4xl mx-3"></i> Riwayat Tugas</h2>

      <!-- Alert -->
      <?php if (!empty($message)) : ?>
        <div class="text-sm <?= $status === 'success' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'; ?> p-4 rounded-lg">
          <?= htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <!-- Profile -->
      <div class="my-5">
        <p class="text-xl my-2">Nama : <?= htmlspecialchars($user['username']); ?></p>
        <p class="text-xl my-2">NIM : <?= htmlspecialchars($user['NIM']); ?></p>
      </div>

      <!-- Table -->
      <table class="min-w-full divide-y divide-gray-200 border border-gray-400 my-5">
        <thead>
          <tr>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">No</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Judul</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Kode Referral</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nilai</th>
            <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">View Detail</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($tasks)) : ?>
            <?php foreach ($tasks as $index => $task) : ?>
              <tr class="odd:bg-white even:bg-gray-100">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?= $index + 1; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($task['title']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($task['referral_code']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($task['score'] ?? '-'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                  <button onclick="openModal(<?= $task['id']; ?>, '<?= addslashes(htmlspecialchars($task['title'])); ?>', '<?= addslashes(htmlspecialchars($task['date'])); ?>', '<?= addslashes(htmlspecialchars($task['answer'] ?? '-')); ?>')"
                    class="cursor-pointer inline-flex items-center gap-x-2 text-black-600 hover:text-black-800">
                    <i class="fas fa-info-circle text-lg"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="5" class="text-center py-4 text-gray-600">Belum ada tugas.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Modal Detail -->
      <div id="detailModal" class="fixed inset-0 hidden flex justify-center items-center" style="backdrop-filter: blur(5px);">
        <div class="bg-white p-6 rounded-lg shadow-lg w-146 relative">
          <p><strong>Judul :</strong> <span id="modalTitle"></span></p>

          <div class="absolute top-6 right-6">
            <p><strong>Tanggal :</strong> <span id="modalDate"></span></p>
          </div>

          <p class="my-5">
            <strong>Jawaban :</strong>
            <span id="modalAnswer"></span>
          </p>
          <div class="flex justify-end mt-4">
            <button onclick="closeModal()" class="cursor-pointer px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Tutup</button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    function openModal(id, title, date, answer) {
      document.getElementById('modalTitle').textContent = title;
      document.getElementById('modalDate').textContent = date;
      document.getElementById('modalAnswer').textContent = answer;
      document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('detailModal').classList.add('hidden');
    }
  </script>
</body>

</html>