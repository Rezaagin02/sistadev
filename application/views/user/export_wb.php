<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CV - Format APBN</title>
    <style>
        @page {
            margin: 120px 20mm 70px 20mm;
            /* footer: html_customfooter;  <-- di-nonaktifkan agar tidak error jika footer tidak didefinisikan */
        }

        body {
            font-family: verdana, sans-serif;
            font-size: 12pt;
            line-height: 1.6; /* semula 2, bikin rapet sedikit biar hemat halaman */
            margin: 0;
            padding: 0;
            background: #fff;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 100px;
            text-align: right;
        }

        header img { width: 200px; }

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

        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #000; padding: 8px; vertical-align: top; }

        .bold { font-weight: bold; }
        .italic { font-style: italic; }
        .underline { text-decoration: underline; }

        hr { border: none; border-top: 1px solid #000; margin: 15px 0; }
        .center { text-align: center; }

        .signature-line { border-top: 1px solid #000; width: 300px; display: inline-block; margin-top: 40px; }
        .small-italic { font-size: 12px; font-style: italic; }

        .signature-container { display: flex; justify-content: space-between; margin-top: 40px; }
        .signature-box { text-align: center; }
        /* Izinkan tabel terpecah antar halaman */
        .two-col { page-break-inside: auto; }
        .two-col tr   { page-break-inside: avoid; page-break-after: auto; }
        /* Jangan pakai display:inline-block/float untuk konten panjang di dalam sel */
        .exp-item { /* blok biasa saja */
        /* kosong aja, pentingnya dia elemen block */
        }
        /* (opsional) kalau masih ada baris yang super panjang, izinkan pecah di kata */
        td, div { word-wrap: break-word; }
    </style>
</head>
<body>
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
    <table border="1">
  <tr>
    <th>Certification Name</th>
    <th>Issuing Body</th>
    <th>Year Obtained</th>
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
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr><td colspan="3">-</td></tr>
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
            <a href="<?= base_url('uploads/cv/' . $t['sertifikat_file']) ?>" target="_blank">View</a>
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
                echo !empty($row['from'])
                  ? date('F Y', strtotime($row['from']))
                  : '-';
              ?>
            </td>
            <td>
              <?php
                echo ($row['to'] === null)
                  ? 'Present'
                  : date('F Y', strtotime($row['to']));
              ?>
            </td>
            <td><?= htmlspecialchars($row['employer'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <?=
                !empty($row['positions'])
                  ? htmlspecialchars(implode(', ', $row['positions']), ENT_QUOTES, 'UTF-8')
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
      <tr><td></td><td>-</td></tr>
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
