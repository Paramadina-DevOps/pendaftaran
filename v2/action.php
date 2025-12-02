<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Pastikan path ini sesuai autoload Composer


function sendRegistrationSuccessEmail($toEmail, $toName) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply2@paramadina.ac.id';
        $mail->Password   = 'emnernfyllripnkl'; // Gunakan App Password Gmail
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('noreply2@paramadina.ac.id', 'Portal Pendaftaran Universitas Paramadina');
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Pendaftaran Berhasil';

        // HTML Email Template Profesional
        $mailContent = '
<html>
<head>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(90deg, #003366, #336699); /* warna biru Paramadina */
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header img {
            max-width: 80px;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            color: #333333;
            font-size: 16px;
            line-height: 1.6;
        }
        .content p {
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            margin-top: 20px;
            color: #ffffff;
            background: linear-gradient(90deg, #003366, #336699); /* tombol biru */
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn:hover {
            background: linear-gradient(90deg, #0055a5, #004080); /* hover biru lebih terang */
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #999999;
            padding: 20px;
        }
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
    <p>Kami mohon agar Anda mempersiapkan diri dan mengikuti seluruh arahan serta prosedur selanjutnya yang akan diberikan oleh <strong>Bagian Pendaftaran</strong> maupun <strong>Humas Universitas Paramadina</strong>. Pastikan untuk selalu memeriksa email dan informasi resmi dari pihak universitas agar tidak terlewatkan pengumuman penting terkait proses pendaftaran, verifikasi data, serta jadwal kegiatan akademik.</p>
    <p>Kami ucapkan terima kasih atas perhatian dan kerja sama Anda. Semoga proses pendaftaran berjalan lancar dan Anda dapat segera menjadi bagian dari keluarga besar <strong>Universitas Paramadina</strong>.</p>
</div>
        <div class="footer">
            &copy; ' . date('Y') . ' Portal Pendaftaran Universitas Paramadina.
        </div>
    </div>
</body>
</html>
';

        $mail->Body = $mailContent;
        $mail->AltBody = "Halo $toName,\n\nPendaftaran Anda berhasil. Silakan login di https://example.com/login";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Email gagal dikirim. Error: {$mail->ErrorInfo}";
    }
}

// Contoh pemanggilan
$data = $_POST; // sama seperti request()->all()
$result = sendRegistrationSuccessEmail('muhammad.pawit@paramadina.ac.id', $data['nama_lengkap']);
if ($result === true) {
    // Redirect ke halaman lain dengan query param notif
    header("Location: https://admission.paramadina.ac.id/v2/?status=success&msg=" . urlencode("Email berhasil dikirim!"));
    exit;
} else {
    // Tampilkan error
    echo "Gagal mengirim email: " . htmlspecialchars($result);
}

// $data = $_POST; // sama seperti request()->all()

// echo "<pre>";
// print_r($data);
// echo "</pre>";
