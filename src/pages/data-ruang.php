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

// Ambil data tugas dari database
$query = "SELECT referrals.id, referrals.title, referrals.referral_code, referrals.date 
          FROM referrals 
          JOIN users ON referrals.user_id = users.id 
          WHERE referrals.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$referrals = $result->fetch_all(MYSQLI_ASSOC);

// Create Data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $date = $_POST['date'];
  $referral_code = $_POST['referral_code'];

  // Query untuk menyimpan data ke tabel referrals
  $query = "INSERT INTO referrals (user_id, title, referral_code, date) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('isss', $user_id, $title, $referral_code, $date);

  if ($stmt->execute()) {
    $message = "Kode referral berhasil ditambahkan!";
    $status = 'success';
    header("Location: /data-ruang");
  } else {
    $message = "Gagal menambahkan kode referral.";
    $status = 'failed';
  }
}

$stmt->close();
$conn->close();
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
      <h2 class="text-3xl font-semibold my-3"><i class="fas fa-code bg-black text-white p-3 rounded-lg text-2xl mx-3"></i> Data Ruang</h2>

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
      </div>

      <!-- Create Data -->
      <button onclick="openModal()" class="cursor-pointer px-8 py-2 my-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Tambah Kode</button>

      <!-- Table -->
      <table class="min-w-full divide-y divide-gray-200 border border-gray-400 my-5">
        <thead>
          <tr>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">No</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Judul</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Kode Referral</th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tanggal</th>
            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($referrals)) : ?>
            <?php foreach ($referrals as $index => $referral) : ?>
              <tr class="odd:bg-white even:bg-gray-100">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?= $index + 1; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($referral['title']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($referral['referral_code']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($referral['date'] ?? '-'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium flex items-center">
                  <a href="/kode-referral?referral_code=<?= urlencode($referral['referral_code']); ?>" class="cursor-pointer mx-1 inline-flex items-center gap-x-2 text-yellow-600 hover:text-yellow-800">
                    <i class="fas fa-edit text-lg"></i>
                  </a>

                  <form action="src/boot/delete.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                    <input type="hidden" name="id" value="<?= $referral['id']; ?>" />
                    <button type="submit" class="cursor-pointer m-1 inline-flex items-center gap-x-2 text-red-600 hover:text-red-800">
                      <i class="fas fa-trash text-lg"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="5" class="text-center py-4 text-gray-600">Belum ada kode referral.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Create Modal -->
      <div id="detailModal" class="fixed inset-0 hidden flex justify-center items-center" style="backdrop-filter: blur(5px);">
        <div class="bg-white p-6 rounded-lg shadow-lg w-165 relative">
          <form method="POST" class="my-3">
            <div class="flex items-center">
              <!-- Title -->
              <div class="w-2/4 m-4">
                <label for="title" class="block mb-2 text-sm font-medium text-gray-900">Judul : </label>
                <input type="text" id="title" name="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
              </div>

              <!-- date -->
              <div class="w-2/4 m-4">
                <label for="date" class="block mb-2 text-sm font-medium text-gray-900">Tanggal : </label>
                <input type="date" id="date" name="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
              </div>
            </div>

            <div class="flex items-center">
              <!-- Referral Code -->
              <div class="w-2/4 m-4">
                <label for="referral_code" class="block mb-2 text-sm font-medium text-gray-900">Kode Referral : </label>
                <input type="text" id="referral_code" name="referral_code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" readonly>
              </div>

              <div class="w-2/4 m-4">
                <button onclick="closeModal()" class="cursor-pointer px-8 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 mt-6 mx-3">Batal</button>
                <button type="submit" class="cursor-pointer px-8 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 mt-6 mx-3">Tambah</button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>

  <script>
    // Get random referral code
    function generateReferralCode() {
      let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      let result = '';
      for (let i = 0; i < 6; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
      }
      document.getElementById('referral_code').value = result;
    }

    // Open & Close Modal
    function openModal() {
      generateReferralCode();
      document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('detailModal').classList.add('hidden');
    }
  </script>
</body>

</html>