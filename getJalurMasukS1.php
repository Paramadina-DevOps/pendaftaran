<?php
            require 'config.php';
            
              $jenjang = isset($_POST['jenjang']) ? $_POST['jenjang'] : '';
              $id_prodi = isset($_POST['id_prodi']) ? $_POST['id_prodi'] : '';
              $lokasi = isset($_POST['lokasi']) ? $_POST['lokasi'] : '';
              $periode_pendaftaran = isset($_POST['periode_pendaftaran']) ? $_POST['periode_pendaftaran'] : '';
              $jenis_pendaftaran = isset($_POST['jenis_pendaftaran']) ? $_POST['jenis_pendaftaran'] : '';
              $now = date('Y-m-d');

              if ($jenis_pendaftaran === 'Pindahan') {
                $sql_jenis_pendaftaran = "and psd.jalur_pendaftaran = 'Pindahan'";
              } else if ($jenis_pendaftaran === "Alih Jenjang (D3 ke S1)") {
                $sql_jenis_pendaftaran = "and psd.jalur_pendaftaran = 'Alih Jenjang (D3 ke S1)'";
              } else {
                $sql_jenis_pendaftaran = "and psd.jalur_pendaftaran != 'Pindahan' and psd.jalur_pendaftaran != 'Alih Jenjang (D3 ke S1)'";
              }
             
              $sql = "
                select *
                from program_studi_dibukas psd
                join periode_pendaftaran pp on pp.id_sevima = psd.id_periode_pendaftaran
                where psd.jenjang_program_studi = '$jenjang'
                and psd.id_program_studi = $id_prodi
                and psd.sistem_kuliah LIKE '%$lokasi%'
                and psd.nama_periode_pendaftaran like '%$periode_pendaftaran%'
                and pp.status_periode_pendaftaran = 'Aktif'
                and pp.tanggal_akhir_pendaftaran >= '$now'
                $sql_jenis_pendaftaran
              ";
              // var_dump($sql);
              $result = mysqli_query($conn, $sql);
                // Jika query berhasil
                if ($result) {
                    // Ambil semua hasil sebagai array multidimensi
                    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    // Cetak hasil
                    echo json_encode($rows); // Mengembalikan dalam format JSON jika ingin digunakan di JavaScript
                } else {
                    // Jika query gagal
                    echo "Query Error: " . mysqli_error($conn);
                }
               mysqli_close($conn);
            ?>