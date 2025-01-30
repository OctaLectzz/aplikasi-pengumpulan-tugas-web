<?php
include './config.php';

if (isset($_POST['referral_code'])) {
  $referral_code = $_POST['referral_code'];

  $query = "SELECT * FROM referrals WHERE referral_code = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('s', $referral_code);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $referral = $result->fetch_assoc();
    echo json_encode(['success' => true, 'title' => $referral['title']]);
  } else {
    echo json_encode(['success' => false]);
  }

  $stmt->close();
  $conn->close();
}
