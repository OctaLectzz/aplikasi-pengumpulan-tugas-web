<?php
include 'src/boot/config.php';

$status = "";
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $nim = $_POST['nim'];
  $role = $_POST['role'];

  $query = "INSERT INTO users (username, password, NIM, role) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('ssss', $username, $password, $nim, $role);

  if ($stmt->execute()) {
    $status = "success";
    $message = "Registration successful! <a href='/login' class='text-blue-600 hover:underline'>Login here</a>";
  } else {
    $status = "failed";
    $message = "Data yang anda masukkan masih salah";
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
  <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
    <h1 class="text-2xl font-bold text-gray-700 text-center mb-6">Register</h1>

    <!-- Alert -->
    <?php if (!empty($message)) : ?>
      <div class="mb-4 text-sm <?= $status === 'success' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'; ?> p-4 rounded-lg">
        <?= $message; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <!-- Username -->
      <div>
        <label for="username" class="block text-sm font-medium text-gray-600">Username</label>
        <input type="text" id="username" name="username"
          class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
          placeholder="Enter your username" required>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-600">Password</label>
        <input type="password" id="password" name="password"
          class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
          placeholder="Enter your password" required>
      </div>

      <!-- NIK / NIP -->
      <div>
        <label id="identifier-label" for="identifier" class="block text-sm font-medium text-gray-600">NIM</label>
        <input type="number" id="identifier" name="nim"
          class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
          placeholder="Enter your NIM" required>
      </div>

      <!-- Role -->
      <div>
        <label for="role" class="block text-sm font-medium text-gray-600">Role</label>
        <select id="role" name="role" onchange="updateIdentifierLabel()"
          class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
          required>
          <option value="Mahasiswa">Mahasiswa</option>
          <option value="Dosen">Dosen</option>
        </select>
      </div>

      <!-- Submit Buttom -->
      <button type="submit"
        class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition cursor-pointer">
        Register
      </button>
    </form>

    <!-- Login -->
    <p class="text-center text-sm text-gray-500 mt-4">
      Already have an account?
      <a href="/login" class="text-blue-500 hover:underline">Login here</a>
    </p>
  </div>

  <script>
    function updateIdentifierLabel() {
      var role = document.getElementById("role").value;
      var label = document.getElementById("identifier-label");
      var input = document.getElementById("identifier");

      if (role === "Dosen") {
        label.textContent = "NIP";
        input.placeholder = "Enter your NIP";
      } else {
        label.textContent = "NIM";
        input.placeholder = "Enter your NIM";
      }
    }
  </script>
</body>

</html>