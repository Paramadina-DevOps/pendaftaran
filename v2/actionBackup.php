<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit;
}

require '../vendor/autoload.php'; // Sesuaikan path autoload Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Dapatkan token dari API Central
 */
function getToken(): string
{
    $url = "https://api-central-upm.paramadina.ac.id/api/auth/login";
    $payload = [
        "email"     => "api@testingparamadina.ac.id",
        "password"  => "Password@Paramadina",
        "scope"     => "staff",
        "login_by"  => "google"
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => 15
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        die("Curl Token Error: " . $error);
    }

    $data = json_decode($response, true);
    if (!isset($data['data']['auth']['access_token'])) {
        die("Gagal mendapatkan token. Response: " . $response);
    }

    return $data['data']['auth']['access_token'];
}

/**
 * Kirim data pendaftaran ke API
 */
function sendData(array $postData, string $token): array
{
    $url = "https://api-central-upm.paramadina.ac.id/api/admission/pendaftaran";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Content-Type: application/json",
            "Authorization: Bearer " . $token
        ],
        CURLOPT_POSTFIELDS     => json_encode($postData),
        CURLOPT_TIMEOUT        => 15
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['success' => false, 'message' => "Curl API Error: " . $error];
    }

    $result = json_decode($response, true);
    return $result ?? ['success' => false, 'message' => 'Response API tidak valid'];
}

/**
 * Kirim email konfirmasi pendaftaran
 */
function sendRegistrationSuccessEmail(string $toEmail, string $toName): bool|string
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply2@paramadina.ac.id';
        $mail->Password   = 'emnernfyllripnkl'; // Gunakan App Password Gmail
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('noreply2@paramadina.ac.id', 'Portal Pendaftaran Universitas Paramadina');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Pendaftaran Berhasil';

        $mailContent = '
        <html>
        <head>
        <style>
            body { font-family: "Segoe UI", sans-serif; background-color: #f0f2f5; margin:0; padding:0; }
            .email-container { max-width:600px; margin:30px auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(90deg, #003366, #336699); padding:30px; text-align:center; color:#fff; }
            .header img { max-width:80px; margin-bottom:10px; }
            .header h2 { margin:0; font-size:24px; }
            .content { padding:30px; color:#333; font-size:16px; line-height:1.6; }
            .btn { display:inline-block; padding:12px 25px; margin-top:20px; color:#fff; background:linear-gradient(90deg, #003366, #336699); text-decoration:none; border-radius:5px; font-weight:bold; transition:0.3s; }
            .btn:hover { background:linear-gradient(90deg, #0055a5, #004080); }
            .footer { text-align:center; font-size:12px; color:#999; padding:20px; }
        </style>
        </head>
        <body>
            <div class="email-container">
                <div class="header">
                    <img src="https://assets.siakadcloud.com/uploads/paramadina/logoaplikasi/1572.jpg" alt="Logo Universitas Paramadina">
                    <h2>Pendaftaran Berhasil!</h2>
                </div>
                <div class="content">
                    <p>Halo <strong>' . htmlspecialchars($toName) . '</strong>,</p>
                    <p>Selamat! Data pendaftaran Anda telah berhasil kami terima dan tercatat secara resmi dalam sistem administrasi <strong>Universitas Paramadina</strong>.</p>
                    <p>Silakan ikuti arahan selanjutnya dari Bagian Pendaftaran dan Humas. Pastikan selalu memeriksa email resmi.</p>
                    <p>Terima kasih atas perhatian dan kerja sama Anda.</p>
                </div>
                <div class="footer">&copy; ' . date('Y') . ' Portal Pendaftaran Universitas Paramadina.</div>
            </div>
        </body>
        </html>';

        $mail->Body = $mailContent;
        $mail->AltBody = "Halo $toName, Pendaftaran Anda berhasil. Silakan cek email resmi universitas.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Email gagal dikirim. Error: {$mail->ErrorInfo}";
    }
}

// MAIN PROCESS
$data = $_POST;

// 1. Ambil token
$token = getToken();

// 2. Kirim data ke API
$apiResponse = sendData($data, $token);

if (!empty($apiResponse['success']) && $apiResponse['success'] === true) {
    // 3. Kirim email
    $emailResult = sendRegistrationSuccessEmail($data['email'] ?? 'muhammad.pawit@paramadina.ac.id', $data['nama_lengkap'] ?? 'Peserta');
    
    if ($emailResult === true) {
        header("Location: https://admission.paramadina.ac.id/v2/?status=success&msg=" . urlencode("Data berhasil disubmit. Silakan cek email Anda."));
        exit;
    } else {
        header("Location: https://admission.paramadina.ac.id/v2/?status=error&msg=" . urlencode($emailResult));
        exit;
    }
} else {
    $msg = $apiResponse['message'] ?? 'Gagal mengirim data ke API.';
    header("Location: https://admission.paramadina.ac.id/v2/?status=error&msg=" . urlencode($msg));
    exit;
}
?>