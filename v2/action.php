<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Pastikan path ini sesuai autoload Composer


function sendRegistrationSuccessEmail($toEmail, $toName) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.paramadina.ac.id';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply2@paramadina.ac.id';
        $mail->Password   = 'emnernfyllripnkl'; // Gunakan App Password Gmail
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        $mail->SMTPDebug = 2; // atau 3 untuk debug lebih detail
        $mail->Debugoutput = 'html';

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
                    background: linear-gradient(90deg, #4CAF50, #2E7D32);
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
                    background: linear-gradient(90deg, #4CAF50, #388E3C);
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                    transition: 0.3s;
                }
                .btn:hover {
                    background: linear-gradient(90deg, #66BB6A, #2E7D32);
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
                    <img src="https://via.placeholder.com/80x80.png?text=Logo" alt="Logo Aplikasi">
                    <h2>Pendaftaran Berhasil!</h2>
                </div>
                <div class="content">
                    <p>Halo <strong>' . htmlspecialchars($toName) . '</strong>,</p>
                    <p>Selamat! Pendaftaran Anda di <strong>Nama Aplikasi</strong> telah berhasil.</p>
                    <p>Anda sekarang dapat masuk dan mulai menggunakan layanan kami.</p>
                    <p style="text-align:center;">
                        <a href="https://example.com/login" class="btn">Masuk ke Aplikasi</a>
                    </p>
                    <p>Jika Anda tidak melakukan pendaftaran ini, silakan abaikan email ini.</p>
                </div>
                <div class="footer">
                    &copy; ' . date('Y') . ' Nama Aplikasi. Semua hak dilindungi.
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
if($result === true){
    echo "Email berhasil dikirim!";
} else {
    echo $result;
}


// $data = $_POST; // sama seperti request()->all()

// echo "<pre>";
// print_r($data);
// echo "</pre>";
