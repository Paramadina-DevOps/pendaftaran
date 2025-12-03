<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit;
}

require '../vendor/autoload.php'; // Sesuaikan path autoload Composer

$data = $_POST; // Data pendaftaran

// Fungsi untuk mengirim POST request JSON dan mengembalikan response array
function postJson(string $url, array $payload, array $headers = []): array {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => array_merge(["Content-Type: application/json"], $headers),
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => 15
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) return ['success' => false, 'message' => "Curl Error: $error"];

    $result = json_decode($response, true);
    return $result ?? ['success' => false, 'message' => 'Response API tidak valid'];
}

// 1. Ambil token
$tokenResponse = postJson("https://api-central-upm.paramadina.ac.id/api/auth/login", [
    "email"     => "api@testingparamadina.ac.id",
    "password"  => "Password@Paramadina",
    "scope"     => "staff",
    "login_by"  => "google"
]);

if (empty($tokenResponse['data']['auth']['access_token'])) {
    header("Location: https://admission.paramadina.ac.id/v2/?status=error&msg=" . urlencode("Gagal mendapatkan token API."));
    exit;
}

$token = $tokenResponse['data']['auth']['access_token'];

// 2. Kirim data pendaftaran
$apiResponse = postJson(
    "https://api-central-upm.paramadina.ac.id/api/admission/pendaftaran",
    $data,
    ["Authorization: Bearer $token"]
);

// 3. Redirect sesuai hasil
if (!empty($apiResponse['success']) && $apiResponse['success'] === true) {
    header("Location: https://admission.paramadina.ac.id/v2/?status=success&msg=" . urlencode("Data berhasil disubmit. Silahkan cek inbox email Anda untuk informasi selanjutnya. Terimakasih!"));
    exit;
} else {
    $msg = $apiResponse['message'] ?? 'Gagal mengirim data ke API.';
    header("Location: https://admission.paramadina.ac.id/v2/?status=error&msg=" . urlencode($msg));
    exit;
}
