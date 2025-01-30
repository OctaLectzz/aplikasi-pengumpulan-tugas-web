<?php
include 'src/boot/config.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $query = "SELECT * FROM users WHERE username = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    $_SESSION['role'] = $user['role'];
    header('Location: /data-ruang');
    exit;
  } else {
    $message = "Invalid username or password.";
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
  <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
    <h1 class="text-2xl font-bold text-gray-700 text-center mb-6">Login</h1>

    <!-- Alert -->
    <?php if (!empty($message)) : ?>
      <div class="mb-4 text-sm text-red-600 bg-red-100 p-4 rounded-lg">
        <?= htmlspecialchars($message); ?>
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

      <!-- Submit Button -->
      <button type="submit"
        class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition cursor-pointer">
        Login
      </button>
    </form>

    <!-- Register -->
    <p class="text-center text-sm text-gray-500 mt-4">
      Don't have an account?
      <a href="/register" class="text-blue-500 hover:underline">Register here</a>
    </p>
  </div>
</body>

</html>