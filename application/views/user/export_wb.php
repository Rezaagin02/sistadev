<?php // application/views/user/export_wb.php ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CV - Format WB</title>
    <style>
        /* --- AWAL PERUBAHAN CSS --- */

        @page {
            size: A4;
            /* HAPUS SEMUA MARGIN DARI SINI */
            margin: 0; 
        }

        body {
            font-family: verdana, sans-serif;
            /* Ukuran font disamakan dengan APBN agar pas dengan margin */
            font-size: 10pt; 
            line-height: 1.5; /* Sedikit disesuaikan dari 1.6 */
            background: #fff;

            /* Terapkan margin yang sama dengan APBN */
            margin-left: 1.9cm;
            margin-right: 1.9cm;
            margin-top: 2.54cm; 
            margin-bottom: 2.54cm;
        }
        
        /* HEADER: Disalin dari APBN */
        header {
            position: fixed;
            top: 0.5cm;
            left: 1cm;
            right: 1cm;
            height: 2cm;
        }
        header img {
            width: 200px;
        }
        header hr {
            margin-top: 0.2cm;
            border: none;
            border-top: 2px solid #000;
            width: 100%;
        }

        /* FOOTER: Ditambahkan dari APBN */
        htmlpagefooter {
            position: fixed;
            bottom: 0.5cm;
            left: 1cm;
            right: 1cm;
        }
        
        /* --- AKHIR PERUBAHAN CSS --- */

        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
            font-size: 12pt;
        }

        .indent { margin-left: 20px; }

        .cv-table { border-collapse: collapse; width: 100%; }
        .cv-table td { padding: 4px 6px; vertical-align: top; }

        .col-no { width: 3%; }
        .col-label { width: 40%; }
        .col-tahun { width: 5%; }
        .col-titik { width: 5%; }
        .col-titik-dalam { width: 1%; }
        .col-isi { width: 60%; }

        /* CSS untuk tabel konten utama */
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #000; padding: 8px; vertical-align: top; }

        .bold { font-weight: bold; }
        .italic { font-style: italic; }
        .underline { text-decoration: underline; }

        hr { border: none; border-top: 1px solid #000; margin: 15px 0; }
        .center { text-align: center; }

        .signature-line { border-top: 1px solid #000; width: 300px; display: inline-block; margin-top: 40px; }
        .small-italic { font-size: 10pt; } 

        .signature-container { display: flex; justify-content: space-between; margin-top: 40px; }
        .signature-box { text-align: center; }
        .two-col { page-break-inside: auto; }
        .two-col tr   { page-break-inside: avoid; page-break-after: auto; }
        .exp-item { /* kosong */ }
        td, div { word-wrap: break-word; }

        .table-certification th:nth-child(4),
        .table-certification td:nth-child(4) {
            width: 15%;
        }
        .table-certification th:nth-child(4),
        .table-certification td:nth-child(4) {
            width: 12%;
        }
    </style>
</head>
<body>

<!-- 
    HEADER & FOOTER DI PINDAH KE ATAS
-->
    <header>
        <img src="<?= base_url('assets/img/header.png') ?>" alt="Logo Header">
        <hr>
    </header>

    <htmlpagefooter name="customfooter">
        <div style="border-top: 1px solid #000; width: 100%; margin-bottom: 20px;"></div>
        
        <!-- MENGGANTI STRUKTUR TABEL DENGAN DIV MENGGUNAKAN FLOAT -->
        <div style="width: 100%; font-size: 8pt;">
            <!-- Konten Kiri (Logo) -->
            <div style="float: left; width: 70%; text-align: left;">
                <img src="<?= base_url('assets/img/footer1.jpeg') ?>" style="width: 60px; margin-right: 5px;">
                <img src="<?= base_url('assets/img/footer2.jpeg') ?>" style="width: 60px; margin-right: 5px;">
                <img src="<?= base_url('assets/img/footer3.jpeg') ?>" style="width: 60px; margin-right: 5px;">
                <img src="<?= base_url('assets/img/footer4.jpeg') ?>" style="width: 60px;">
            </div>
            
            <!-- Konten Kanan (Nomor Halaman) -->
            <div style="float: right; width: 30%; text-align: right;">
                <!-- Area ini dikosongkan untuk diisi oleh PHP page_script -->
            </div>
            
            <!-- Membersihkan float agar Div footer tidak collapse -->
            <div style="clear: both;"></div> 
        </div>
        <!-- AKHIR FOOTER DIV -->
    </htmlpagefooter>

<!-- KONTEN UTAMA WB ANDA MULAI DI SINI -->
<ol>
    <li><b>Name of Associate:</b> <?= htmlspecialchars($cv['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></li>
    <li><b>Proposed Position:</b> <?= htmlspecialchars($cv['posisi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></li>
    <li><b>Employer:</b> <?= htmlspecialchars($cv['perusahaan'] ?? '-', ENT_QUOTES, 'UTF-8') ?></li>
    <li>
        <b>Date of Birth:</b>
        <?php
            $dob = '-';
            if (!empty($cv['tanggal_lahir'])) {
                $ts = strtotime($cv['tanggal_lahir']);
                $dob = $ts ? date('F j, Y', $ts) : htmlspecialchars($cv['tanggal_lahir'], ENT_QUOTES, 'UTF-8');
            }
            echo $dob;
        ?>
        <b>Nationality:</b> Indonesia
    </li>

    <li><b>Education</b></li>
    <table border="1">
        <tr>
            <th>School, College and/or University Attended</th>
            <th>Degree/Certificate or Other Specialized Education Obtained</th>
            <th>Date Obtained</th>
        </tr>
        <?php if (!empty($pendidikan_formal)): ?>
            <?php foreach ($pendidikan_formal as $i => $p): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($p['institusi'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td><?= htmlspecialchars($p['tingkat'] ?? '', ENT_QUOTES, 'UTF-8') ?>,
                        <?= !empty($p['jurusan']) ? ' ' . htmlspecialchars($p['jurusan'], ENT_QUOTES, 'UTF-8') : '' ?></td>
                    <td><?= htmlspecialchars($p['tahun_lulus'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">-</td></tr>
        <?php endif; ?>
    </table>

    <li><b>Professional Certification or Membership in Professional Associations:</b></li>
    <table border="1" class="table-certification">
    <tr>
        <th>Certification Name</th>
        <th>Issuing Body</th>
        <th>Year Obtained</th>
        <th>Certificate</th>   
    </tr>

    <?php if (!empty($sertifikasi)): ?>
        <?php foreach ($sertifikasi as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($s['penerbit'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php
                        // kolom YEAR(4) → amanin output 4 digit, fallback ke raw kalau ada kasus aneh
                        $yr = $s['tahun'] ?? '';
                        echo (preg_match('/^\d{4}$/', (string)$yr)) ? $yr : htmlspecialchars((string)$yr, ENT_QUOTES, 'UTF-8');
                    ?>
                </td>
                <td>
                <?php if (!empty($s['file_sertifikat'])): ?>
                <a href="<?= base_url('' . $s['file_sertifikat']) ?>" target="_blank">View</a>
                <?php else: ?>
                    -
                <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4">-</td></tr>
    <?php endif; ?>

    </table>
    <li><b>Other Relevant Training:</b></li>
    <table border="1">
    <tr>
        <th>Training Name</th>
        <th>Organizer</th>
        <th>Year</th>
        <th>Certificate</th>
    </tr>

    <?php if (!empty($pendidikan_nonformal)): ?>
        <?php foreach ($pendidikan_nonformal as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['nama_pelatihan'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($t['penyelenggara'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php
                        $yr = $t['tahun'] ?? '';
                        echo (preg_match('/^\d{4}$/', (string)$yr)) ? $yr : htmlspecialchars((string)$yr, ENT_QUOTES, 'UTF-8');
                    ?>
                </td>
                <td>
                    <?php if (!empty($t['sertifikat_file'])): ?>
                        <a href="<?= base_url('' . $t['sertifikat_file']) ?>" target="_blank">View</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4">-</td></tr>
    <?php endif; ?>


    </table>
    <li><b>Country of Work Experience:</b></li>
<?php if (!empty($country_experience)): ?>
    <?php
        // kalau $country_experience sudah berupa array string ["Indonesia","Malaysia",...]
        echo implode(', ', array_map(
            fn($s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8'),
            array_values(array_unique(array_filter(array_map('trim', $country_experience))))
        ));
    ?>
<?php else: ?>
    -
<?php endif; ?>
    <li><b>Employment Record :</b></li>

    <table border="1">
        <tr>
            <th style="width:18%;">From</th>
            <th style="width:18%;">To</th>
            <th>Employer</th>
            <th style="width:35%;">Position held</th>
        </tr>

        <?php if (!empty($employment_record)): ?>
            <?php foreach ($employment_record as $row): ?>
                <tr>
                    <td>
                        <?php
                            // Ambil nilai 'from', jika tidak ada gunakan null
                            $date_from = $row['from'] ?? null; 
                            
                            echo !empty($date_from)
                                ? date('F Y', strtotime($date_from))
                                : '-';
                        ?>
                    </td>
                    <td>
                        <?php
                            // Ambil nilai 'to', jika tidak ada gunakan null
                            $date_to = $row['to'] ?? null; 
                            
                            // Cek apakah nilai 'to' null
                            echo ($date_to === null)
                                ? 'Present'
                                : date('F Y', strtotime($date_to));
                        ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['employer'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td>
                        <?=
                            !empty($row['positions'])
                                ? htmlspecialchars(implode(', ', $row['positions'] ?? []), ENT_QUOTES, 'UTF-8')
                                : '-'
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">-</td></tr>
        <?php endif; ?>
    </table>
    <br>
    <table class="two-col">
    <tbody>
        <tr>
            <td style="width:50%; vertical-align:top;">
                <span class="bold">10. Work Undertaken that Best Illustrates Capability to Handle the Tasks Assigned</span>
            </td>
        </tr>

        <?php if (!empty($pengalaman)): ?>
            <?php foreach ($pengalaman as $exp): ?>
                <tr class="exp-row">
                    <!-- kolom kiri dikosongkan agar layout tetap dua kolom -->
                    <td>
                        <div class="exp-item">
                            <div><span class="bold">Name of assignment or project:</span> <?= htmlspecialchars($exp['nama_kegiatan'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            <div><span class="bold">Main project features:</span> <?= nl2br(htmlspecialchars($exp['uraian_proyek'] ?? '-', ENT_QUOTES, 'UTF-8')) ?></div>
                            <div><span class="bold">Name of Organization:</span> <?= nl2br(htmlspecialchars($exp['pelaksana_proyek'] ?? '-', ENT_QUOTES, 'UTF-8')) ?></div>
                            <div><span class="bold">Date:</span> <?= htmlspecialchars($exp['waktu_mulai'] ?? '', ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($exp['waktu_akhir'] ?? '', ENT_QUOTES, 'UTF-8') ?> </div>
                            <div><span class="bold">Duration:</span> <?= htmlspecialchars($exp['durasi'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            <div><span class="bold">Location:</span> <?= htmlspecialchars($exp['lokasi'] ?? '', ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($exp['negara'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            <div><span class="bold">Client:</span> <?= htmlspecialchars($exp['pemberi_pekerjaan'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            <div><span class="bold">Positions held:</span> <?= htmlspecialchars($exp['posisi'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            <div><span class="bold">Activities performed:</span><br><?= nl2br(htmlspecialchars($exp['uraian_tugas'] ?? '', ENT_QUOTES, 'UTF-8')) ?></div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td>-</td></tr>
        <?php endif; ?>
    </tbody>
    </table>

</ol>

<p>
    12. Do you currently or have you ever worked for the World Bank Group
    <span class="underline">including any</span> of the following types of appointments:
    Regular, term, ETC, ETT, STC, <span class="underline">STT</span>, <span class="underline">JPA</span>, or JPO?
    If yes, please provide details, including start/end dates of appointment.
</p>

<hr>

<p class="center bold">Certification</p>

<p>
    I certify that (1) to the best of my knowledge and belief, this CV correctly describes me, my qualifications, and my experience;
    (2) that I am available for the assignment for which I am proposed; and
    (3) that I am proposed only by one Offeror and under one proposal.
</p>

<p>
    I understand that any wilful misstatement or misrepresentation herein may lead to my disqualification or removal from the selected team undertaking the assignment.
</p>

<div class="signature-container">
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="small-italic">[Signature of staff member or authorized representative of the staff]</div>
    </div>
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="small-italic">Date / Day/Month/Year</div>
    </div>
</div>

</body>
</html>