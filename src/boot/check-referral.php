<?php
include 'config.php';

$response = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['referral_code'])) {
    $referral_code = $_POST['referral_code'];

    // Ambil data referral berdasarkan kode referral
    $query = "SELECT * FROM referrals WHERE referral_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $referral_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $referral = $result->fetch_assoc();

        // Ambil tanggal referral
        $referral_date = $referral['date']; // Tanggal referral dari database
        $current_date = date('Y-m-d'); // Tanggal saat ini

        // Bandingkan tanggal referral dengan tanggal saat ini
        if ($current_date > $referral_date) {
            $response['success'] = false;
            $response['message'] = 'Kode Referral sudah melewati batas waktu.';
        } else {
            $response['success'] = true;
            $response['title'] = $referral['title']; // Mengirim judul untuk tugas
            $response['date'] = $referral_date; // Mengirim tanggal
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Kode Referral tidak ditemukan.';
    }

    $stmt->close();
    $conn->close();
} else {
    $response['success'] = false;
    $response['message'] = 'Parameter tidak valid.';
}

// Mengembalikan respons sebagai JSON
echo json_encode($response);
?>
