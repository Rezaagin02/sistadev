<?php // application/views/cv/export_apbn.php ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CV - Format APBN</title>
    <style>
        @page {
            size: A4;
            /* HAPUS SEMUA MARGIN DARI SINI */
            margin: 0; 
        }

        body {
            font-family: verdana, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            background: #fff;

            /* PERBAIKAN: Terapkan margin 1 inci (2.54cm) di sini */
            margin-left: 1.9cm;
            margin-right: 1.9cm;

            /* PERBAIKAN: Beri margin atas & bawah yang LEBIH BESAR
            untuk memberi ruang bagi header dan footer
            */
            margin-top: 2.54cm; 
            margin-bottom: 2.54cm;
        }
        
        /* HEADER: Diposisikan di Pojok Kiri Atas Kertas (Mengabaikan Margin) */
        header {
            position: fixed;
            /* Atur posisi TEPAT di pojok kiri atas */
            top: 0.5cm; /* Jarak 1cm dari TEPI ATAS kertas */
            left: 1cm; /* Jarak 1cm dari TEPI KIRI kertas */
            right: 1cm; /* Jarak 1cm dari TEPI KANAN kertas */
            height: 2cm; /* Ketinggian area header (sesuaikan jika perlu) */
        }
        header img {
            width: 200px; /* Ukuran logo dikecilkan */
        }
        header hr {
            margin-top: 0.2cm; /* Sesuaikan agar pas di bawah logo */
            border: none;
            border-top: 2px solid #000;
            width: 100%;
        }

        /* FOOTER: Diposisikan di Pojok Bawah Kertas (Mengabaikan Margin) */
        htmlpagefooter {
            position: fixed;
            bottom: 0.5cm; /* Jarak 1cm dari TEPI BAWAH kertas */
            left: 1cm; /* Jarak 1cm dari TEPI KIRI kertas */
            right: 1cm; /* Jarak 1cm dari TEPI KANAN kertas */
        }
        
        /* (CSS Konten Utama sisanya biarkan sama) */
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
            font-size: 12pt;
        }
        .indent {
            margin-left: 20px;
        }
        .cv-table {
            border-collapse: collapse;
            width: 100%;
        }
        .cv-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .col-no { width: 3%; }
        .col-label { width: 40%; }
        .col-tahun { width: 5%; }
        .col-titik { width: 5%; }
        .col-titik-dalam { width: 1%; }
        .col-isi { width: 60%; }
    </style>
    </head>
<body>
    <header>
        <img src="<?= base_url('assets/img/header.png') ?>" alt="Logo Header">
        <hr>
    </header>

        <htmlpagefooter name="customfooter">

        <div style="border-top: 1px solid #000; width: 100%; margin-bottom: 20px;"></div>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 70%; text-align: left; vertical-align: top; font-size: 8pt;">
                    <img src="<?= base_url('assets/img/footer1.jpeg') ?>" style="width: 60px; margin-right: 5px;">
                    <img src="<?= base_url('assets/img/footer2.jpeg') ?>" style="width: 60px; margin-right: 5px;">
                    <img src="<?= base_url('assets/img/footer3.jpeg') ?>" style="width: 60px; margin-right: 5px;">
                    <img src="<?= base_url('assets/img/footer4.jpeg') ?>" style="width: 60px;">
                </td>
                
                <td style="width: 30%; text-align: right; vertical-align: top; font-size: 8pt;">
                    
                </td>
            </tr>
        </table>
    </htmlpagefooter>

    <div class="title">DAFTAR RIWAYAT HIDUP</div>
    <table class="cv-table">
        <tr>
            <td class="col-no">1.</td>
            <td class="col-label">Posisi Yang Diusulkan</td>
            <td class="col-titik">:</td>
            <td colspan="3" class="col-isi"><?= $cv['posisi'] ?></td>
        </tr>
        <tr>
            <td class="col-no">2.</td>
            <td class="col-label">Nama Perusahaan</td>
            <td class="col-titik">:</td>
            <td colspan="3" class="col-isi"><?= $cv['perusahaan'] ?></td>
        </tr>
        <tr>
            <td class="col-no">3.</td>
            <td class="col-label">Nama Personel</td>
            <td class="col-titik">:</td>
            <td colspan="3" class="col-isi"><?= $cv['nama'] ?></td>
        </tr>
        <tr>
            <td class="col-no">4.</td>
            <td class="col-label">Tempat/ Tanggal Lahir</td>
            <td class="col-titik">:</td>
            <td colspan="3" class="col-isi"><?= $cv['tempat_lahir'] ?>, <?= date('d-m-Y', strtotime($cv['tanggal_lahir'])) ?></td>
        </tr>

        <?php foreach ($pendidikan_formal as $i => $p): ?>
            <tr>
                <?php if ($i == 0): ?>
                    <td class="col-no" rowspan="<?= count($pendidikan_formal) ?>">5.</td>
                    <td class="col-label" rowspan="<?= count($pendidikan_formal) ?>">
                    Pendidikan (Lembaga pendidikan, tempat dan tahun tamat belajar, dilampirkan rekaman ijazah)
                    </td>
                    <td class="col-titik" rowspan="<?= count($pendidikan_formal) ?>">:</td>
                <?php endif; ?>
                <td class="col-tahun"><span><?= $p['tahun_lulus'] ?></span></td>
                <td class="col-titik-dalam"><span>:</span></td>
                <td class="col-isi"><span><?= $p['tingkat'] ?> <?= $p['jurusan'] ?>, <?= $p['institusi'] ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($pendidikan_nonformal as $i => $p): ?>
            <tr>
                <?php if ($i == 0): ?>
                    <td class="col-no" rowspan="<?= count($pendidikan_nonformal) ?>">6.</td>
                    <td class="col-label" rowspan="<?= count($pendidikan_nonformal) ?>">
                        Pendidikan Non Formal
                    </td>
                    <td class="col-titik" rowspan="<?= count($pendidikan_nonformal) ?>">:</td>
                <?php endif; ?>
                <td class="col-tahun"><span><?= $p['tahun'] ?></span></td>
                <td class="col-titik-dalam"><span>:</span></td>
                <td class="col-isi"><span><?= $p['penyelenggara'] ?>, <?= $p['nama_pelatihan'] ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($bahasa ?? [] as $i => $p): ?>
            <tr>
                <?php if ($i == 0): ?>
                    <td class="col-no" rowspan="<?= count($bahasa ?? []) ?>">7.</td>
                    <td class="col-label" rowspan="<?= count($bahasa ?? []) ?>">
                        Penguasaan Bahasa
                    </td>
                    <td class="col-titik" rowspan="<?= count($bahasa ?? []) ?>">:</td>
                <?php endif; ?>
                
                <td class="col-tahun"><span><?= $p['bahasa'] ?? '-' ?></span></td>
                <td class="col-titik-dalam"><span>:</span></td>
                <td class="col-isi"><span><?= $p['speaking'] ?? '-' ?></span></td>
            </tr>
        <?php endforeach; ?>
        
        <tr>
            <td class="col-no">8.</td>
            <td class="col-label">Pengalaman Kerja</td>
            <td class="col-titik">:</td>
            <td colspan="3" class="col-isi"></td> 
        </tr> 
        
        <?php foreach ($pengalaman ?? [] as $x => $exp): ?>
        
        <tr>
            <td></td>             
            <td colspan="5"><b>Tahun <?= $exp['tahun'] ?? '-' ?></b></td>         
        </tr>
        <tr><td></td><td class="col-label">a. Nama Kegiatan</td><td class="col-titik">:</td><td colspan="3" class="col-isi"><?= $exp['nama_kegiatan'] ?? '-' ?></td></tr>
        <tr><td></td><td class="col-label">b. Lokasi Kegiatan</td><td class="col-titik">:</td><td colspan="3" class="col-isi"><?= $exp['lokasi'] ?? '-' ?></td></tr>
        <tr><td></td><td class="col-label">c. Pemberi Pekerjaan</td><td class="col-titik">:</td><td colspan="3" class="col-isi"><?= $exp['pemberi_pekerjaan'] ?? '-' ?></td></tr>
        <tr><td></td><td class="col-label">d. Nama Perusahaan</td><td class="col-titik">:</td><td colspan="3" class="col-isi"><?= $exp['pelaksana_proyek'] ?? '-' ?></td></tr>
        <tr><td></td><td class="col-label">e. Uraian Tugas</td><td class="col-titik">:</td><td colspan="3" class="col-isi"><?= $exp['uraian_tugas'] ?? '-' ?></td></tr>
        
        <tr>
            <td></td>
            <td class="col-label">f. Waktu Pelaksanaan</td>
            <td class="col-titik">:</td>
            <td colspan="3" class="col-isi">
                <?= date('d M Y', strtotime($exp['waktu_mulai'] ?? '')) ?> - 
                <?php
                    if (empty($exp['waktu_akhir'])) {
                        echo 'Sekarang';
                    } else {
                        echo date('d M Y', strtotime($exp['waktu_akhir']));
                    }
                ?>
            </td>
        </tr>
        
        <tr><td></td><td class="col-label">g. Posisi Penugasan</td><td class="col-titik">:</td><td colspan="3" class="col-isi"><?= $exp['posisi'] ?? '-' ?></td></tr>
        <tr><td></td><td class="col-label">h. Status Kepegawaian</td><td class="col-titik">:</td><td colspan="3" class="col-isi"><?= $exp['status_kepegawaian'] ?? '-' ?></td></tr>
        <tr><td></td><td class="col-label">i. Surat Referensi</td><td class="col-titik">:</td><td colspan="3" class="col-isi"><?= $exp['referensi_file'] ?? '-' ?></td></tr>
        
        <?php endforeach; ?>
        <tr>
            <td class="col-no">9.</td>
            <td class="col-label">Status kepegawaian pada perusahaan ini</td>
            <td class="col-titik">:</td>
            <td colspan="3" class="col-isi"><?= $cv['status_kepegawaian'] ?></td>
        </tr>
    </table>


</body>
</html>