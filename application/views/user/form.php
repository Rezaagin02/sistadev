<?php // application/views/cv/form.php ?>
<?php
// helper kecil buat amanin output
function v($arr, $key, $default=''){ return isset($arr[$key]) ? htmlspecialchars($arr[$key], ENT_QUOTES, 'UTF-8') : $default; }
?>
<style>
  .req::after { content:" *"; color:#dc3545; font-weight:600; }
  .hint-file { font-size:.825rem; color:#6c757d; }
  .item-sep { border:0; border-top:1px dashed #2c343dff; margin:.5rem 0 1rem; }
</style>

<div class="container py-3">
  <div class="row g-3">
    <div class="d-none d-lg-block col-lg-3">
      <?php $this->load->view('templates/sidebar'); ?>
    </div>

    <div class="col-12 col-lg-9">
      <div class="card">
        <div class="card-header">
          <h3>Form Input CV</h3>
        </div>

        <div class="card-body">
          <form method="post" action="<?= base_url('user/save') ?>" enctype="multipart/form-data">
            <?php if (isset($this->security)) : ?>
              <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                     value="<?= $this->security->get_csrf_hash(); ?>">
            <?php endif; ?>

            <ul class="nav nav-tabs" id="cvTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="umum-tab" data-bs-toggle="tab" data-bs-target="#umum" type="button" role="tab">Informasi Umum</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="pendidikan-tab" data-bs-toggle="tab" data-bs-target="#pendidikan" type="button" role="tab">Pendidikan</button>
              </li>
              <li class="nav-item"><a class="nav-link" id="nonformal-tab" data-bs-toggle="tab" href="#nonformal" role="tab">Pelatihan</a></li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="pengalaman-tab" data-bs-toggle="tab" data-bs-target="#pengalaman" type="button" role="tab">Pengalaman</button>
              </li>
             
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="bahasa-tab" data-bs-toggle="tab" data-bs-target="#bahasa" type="button" role="tab">Bahasa</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="sertifikasi-tab" data-bs-toggle="tab" data-bs-target="#sertifikasi" type="button" role="tab">Sertifikat Keahlian</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="lampiran-tab" data-bs-toggle="tab" data-bs-target="#lampiran" type="button" role="tab">Lampiran</button>
              </li>
            </ul>

            <div class="tab-content pt-3" id="cvTabContent">
              <!-- Informasi Umum -->
             <div class="tab-pane fade show active" id="umum" role="tabpanel">
              <!-- Data Pribadi -->
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0">Data Pribadi</h6>
                <!-- <small class="text-muted">Lengkapi identitas dan domisili</small> -->
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label req">Nama Lengkap</label>
                  <input type="text" name="nama" class="form-control" required
                        value="<?= set_value('nama', v($cv ?? [],'nama')) ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label req">Posisi yang Diusulkan</label>
                  <input type="text" name="posisi" class="form-control" required
                        value="<?= set_value('posisi', v($cv ?? [],'posisi')) ?>">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label">Perusahaan</label>
                  <input type="text" name="perusahaan" class="form-control"
                        value="<?= set_value('perusahaan', v($cv ?? [],'perusahaan')) ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Kewarganegaraan</label>
                  <input type="text" name="kewarganegaraan" class="form-control"
                        value="<?= set_value('kewarganegaraan', v($cv ?? [],'kewarganegaraan')) ?>">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label">Tempat Lahir</label>
                  <input type="text" name="tempat_lahir" class="form-control"
                        value="<?= set_value('tempat_lahir', v($cv ?? [],'tempat_lahir')) ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Tanggal Lahir</label>
                  <input type="date" name="tanggal_lahir" class="form-control"
                        value="<?= set_value('tanggal_lahir', v($cv ?? [],'tanggal_lahir')) ?>">
                </div>
              </div>

              <!-- 🔥 Lokasi Domisili User -->
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label req">Negara / Wilayah</label>
                  <input type="text" name="domisili_negara" class="form-control" required
                        placeholder="mis. Indonesia"
                        value="<?= set_value('domisili_negara', v($cv ?? [], 'domisili_negara')) ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label req">Kota</label>
                  <input type="text" name="domisili_kota" class="form-control" required
                        placeholder="mis. Bandung, Jawa Barat"
                        value="<?= set_value('domisili_kota', v($cv ?? [], 'domisili_kota')) ?>">
                </div>
              </div>

              <hr class="my-3">

              <!-- Pekerjaan Saat Ini -->
              <h6 class="fw-bold mb-2">Pekerjaan Saat Ini</h6>
              <div class="row g-3 mb-3">
                <div class="col-md-3">
                  <label class="form-label">Mulai Bekerja <span class="text-danger">*</span></label>
                  <input type="date" name="employment_from" class="form-control" required
                        value="<?= set_value('employment_from', v($cv ?? [], 'employment_from')) ?>">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Sampai</label>
                  <input type="date" name="employment_to" class="form-control"
                        value="<?= set_value('employment_to', v($cv ?? [], 'employment_to')) ?>">
                  <small class="text-muted">Kosongkan jika masih bekerja</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Perusahaan/Instansi <span class="text-danger">*</span></label>
                  <input type="text" name="employer" class="form-control" required
                        value="<?= set_value('employer', v($cv ?? [], 'employer')) ?>">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label">Posisi/Jabatan <span class="text-danger">*</span></label>
                  <input type="text" name="employment_position" class="form-control" required
                        value="<?= set_value('employment_position', v($cv ?? [], 'employment_position')) ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Deskripsi Pekerjaan</label>
                  <textarea name="employment_desc" class="form-control" rows="2"><?= set_value('employment_desc', v($cv ?? [], 'employment_desc')) ?></textarea>
                </div>
              </div>
            </div>


              <!-- Pendidikan Formal -->
              <div class="tab-pane fade" id="pendidikan" role="tabpanel">
                <div id="pendidikan-wrapper">
                  <?php if (!empty($pendidikan_formal)):
                        $__last = count($pendidikan_formal)-1;
                        foreach ($pendidikan_formal as $__i => $r): ?>
                    <div class="row mb-3 repeat-row">
                      <div class="col-md-2">
                        <label class="req">Tingkat</label>
                        <input type="text" name="tingkat[]" class="form-control" placeholder="(S1/S2/S3)" required value="<?= v($r,'tingkat') ?>">
                      </div>
                      <div class="col-md-3">
                        <label class="req">Institusi</label>
                        <input type="text" name="institusi[]" class="form-control" required value="<?= v($r,'institusi') ?>">
                      </div>
                      <div class="col-md-2">
                        <label class="req">Jurusan</label>
                        <input type="text" name="jurusan[]" class="form-control" required value="<?= v($r,'jurusan') ?>">
                      </div>
                      <div class="col-md-2">
                        <label class="req">Tahun Lulus</label>
                        <input type="text" name="tahun_lulus[]" class="form-control" required value="<?= v($r,'tahun_lulus') ?>">
                      </div>
                      <div class="col-md-3">
                        <label>Ijazah (pdf/jpg/png)</label>
                        <input type="file" name="ijazah_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                      </div>
                    </div>
                    <?php if ($__i !== $__last): ?><hr class="item-sep"><?php endif; ?>
                  <?php endforeach; else: ?>
                    <div class="row mb-3 repeat-row">
                      <div class="col-md-2">
                        <label class="req">Tingkat</label>
                        <input type="text" name="tingkat[]" class="form-control" placeholder="(S1/S2/S3)" required>
                      </div>
                      <div class="col-md-3">
                        <label class="req">Institusi</label>
                        <input type="text" name="institusi[]" class="form-control" required placeholder="Nama Institusi">
                      </div>
                      <div class="col-md-2">
                        <label class="req">Jurusan</label>
                        <input type="text" name="jurusan[]" class="form-control" required placeholder="Jurusan">
                      </div>
                      <div class="col-md-2">
                        <label class="req">Tahun Lulus</label>
                        <input type="text" name="tahun_lulus[]" class="form-control" required placeholder="Tahun Lulus">
                      </div>
                      <div class="col-md-3">
                        <label>Ijazah (pdf/jpg/png)</label>
                        <input type="file" name="ijazah_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Pelatihan -->
              <div class="tab-pane fade" id="nonformal" role="tabpanel">
                <div id="nonformal-wrapper">
                  <?php if (!empty($pendidikan_nonformal)):
                        $__last = count($pendidikan_nonformal)-1;
                        foreach ($pendidikan_nonformal as $__i => $r): ?>
                    <div class="row mb-3 repeat-row">
                      <div class="col-md-4">
                        <label class="req">Nama Pelatihan</label>
                        <input type="text" name="nama_pelatihan[]" class="form-control" required value="<?= v($r,'nama_pelatihan') ?>">
                      </div>
                      <div class="col-md-3">
                        <label class="req">Penyelenggara</label>
                        <input type="text" name="penyelenggara[]" class="form-control" required value="<?= v($r,'penyelenggara') ?>">
                      </div>
                      <div class="col-md-2">
                        <label class="req">Tahun</label>
                        <input type="text" name="tahun_pelatihan[]" class="form-control" required value="<?= v($r,'tahun') ?>">
                      </div>
                      <div class="col-md-3">
                        <label>Sertifikat (pdf/jpg/png)</label>
                        <input type="file" name="sertifikat_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                      </div>
                    </div>
                    <?php if ($__i !== $__last): ?><hr class="item-sep"><?php endif; ?>
                  <?php endforeach; else: ?>
                    <div class="row mb-3 repeat-row">
                      <div class="col-md-4"><label class="req">Nama Pelatihan</label><input type="text" name="nama_pelatihan[]" class="form-control" required placeholder="Nama Pelatihan"></div>
                      <div class="col-md-3"><label class="req">Penyelenggara</label><input type="text" name="penyelenggara[]" class="form-control" required placeholder="Penyelenggara"></div>
                      <div class="col-md-2"><label class="req">Tahun</label><input type="text" name="tahun_pelatihan[]" class="form-control" required placeholder="Tahun"></div>
                      <div class="col-md-3"><label>Sertifikat (pdf/jpg/png)</label><input type="file" name="sertifikat_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Pengalaman -->
              <div class="tab-pane fade" id="pengalaman" role="tabpanel">
                <div id="pengalaman-wrapper">
                  <?php if (!empty($pengalaman_kerja)):
                        $__last = count($pengalaman_kerja)-1;
                        foreach ($pengalaman_kerja as $__i => $r): ?>
                    <div class="row mb-3 repeat-row">
                      <div class="col-md-6">
                        <label class="req">Nama Proyek</label>
                        <input type="text" name="nama_kegiatan[]" class="form-control" required value="<?= v($r,'nama_kegiatan') ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="req">Pemberi Pekerjaan</label>
                        <input type="text" name="pemberi_pekerjaan[]" class="form-control" required value="<?= v($r,'pemberi_pekerjaan') ?>">
                      </div>

                      <div class="col-md-6 mt-2">
                        <label>Uraian Proyek</label>
                        <input type="text" name="uraian_proyek[]" class="form-control" value="<?= v($r,'uraian_proyek') ?>">
                      </div>
                      <div class="col-md-3 mt-2">
                        <label class="req">Lokasi</label>
                        <input type="text" name="lokasi[]" class="form-control" required value="<?= v($r,'lokasi') ?>">
                      </div>
                      <div class="col-md-3 mt-2">
                        <label>Negara</label>
                        <input type="text" name="negara[]" class="form-control" value="<?= v($r,'negara') ?>">
                      </div>

                      <div class="col-md-3 mt-2">
                        <label class="req">Posisi</label>
                        <input type="text" name="posisi_pengalaman[]" class="form-control" required value="<?= v($r,'posisi') ?>">
                      </div>
                      <div class="col-md-3 mt-2">
                        <label>Status (Tetap/Kontrak)</label>
                        <input type="text" name="status_pegawai[]" class="form-control" value="<?= v($r,'status_kepegawaian') ?>">
                      </div>

                      <div class="col-md-3 mt-2">
                        <label class="req">Waktu Mulai</label>
                        <input type="date" name="waktu_mulai[]" class="form-control" required value="<?= v($r,'waktu_mulai') ?>">
                      </div>
                      <div class="col-md-3 mt-2">
                        <label class="req">Waktu Akhir</label>
                        <input type="date" name="waktu_akhir[]" class="form-control" required value="<?= v($r,'waktu_akhir') ?>">
                      </div>

                      <div class="col-md-6 mt-2">
                        <label>Uraian Tugas</label>
                        <input type="text" name="uraian_tugas[]" class="form-control" value="<?= v($r,'uraian_tugas') ?>">
                      </div>
                      <div class="col-md-6 mt-2">
                        <label>Durasi</label>
                        <input type="text" name="durasi[]" class="form-control" readonly value="<?= v($r,'durasi') ?>" placeholder="Durasi">
                      </div>

                      <div class="col-md-6 mt-2">
                        <label>Pelaksana Proyek (Perusahaan)</label>
                        <input type="text" name="pelaksana_proyek[]" class="form-control" value="<?= v($r,'pelaksana_proyek') ?>">
                      </div>
                      <div class="col-md-6 mt-2">
                        <label>Referensi (pdf/jpg/png)</label>
                        <input type="file" name="referensi_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <?php if (!empty($r['referensi_file'])): ?>
                          <div class="hint-file mt-1">File lama: <?= htmlspecialchars(basename($r['referensi_file'])) ?></div>
                        <?php endif; ?>
                      </div>
                    </div>
                    <?php if ($__i !== $__last): ?><hr class="item-sep"><?php endif; ?>
                  <?php endforeach; else: ?>
                    <div class="row mb-3 repeat-row">
                      <div class="col-md-6"><label class="req">Nama Proyek</label><input type="text" name="nama_kegiatan[]" class="form-control" required placeholder="Nama Proyek"></div>
                      <div class="col-md-6"><label class="req">Pemberi Pekerjaan</label><input type="text" name="pemberi_pekerjaan[]" class="form-control" required placeholder="Pemberi Pekerjaan"></div>
                      <div class="col-md-6 mt-2"><label>Uraian Proyek</label><input type="text" name="uraian_proyek[]" class="form-control" placeholder="Uraian Proyek"></div>
                      <div class="col-md-3 mt-2"><label class="req">Lokasi</label><input type="text" name="lokasi[]" class="form-control" required placeholder="Lokasi (Kota/Provinsi)"></div>
                      <div class="col-md-3 mt-2"><label>Negara</label><input type="text" name="negara[]" class="form-control" placeholder="Negara"></div>
                      <div class="col-md-3 mt-2"><label class="req">Posisi</label><input type="text" name="posisi_pengalaman[]" class="form-control" required placeholder="Posisi"></div>
                      <div class="col-md-3 mt-2"><label>Status</label><input type="text" name="status_pegawai[]" class="form-control" placeholder="Status (Tetap/Kontrak)"></div>
                      <div class="col-md-3 mt-2"><label class="req">Waktu Mulai</label><input type="date" name="waktu_mulai[]" class="form-control" required placeholder="Waktu Mulai"></div>
                      <div class="col-md-3 mt-2"><label class="req">Waktu Akhir</label><input type="date" name="waktu_akhir[]" class="form-control" required placeholder="Waktu Akhir"></div>
                      <div class="col-md-6 mt-2"><label>Uraian Tugas</label><input type="text" name="uraian_tugas[]" class="form-control" placeholder="Uraian Tugas"></div>
                      <div class="col-md-6 mt-2"><label>Durasi</label><input type="text" name="durasi[]" class="form-control" placeholder="Durasi" readonly></div>
                      <div class="col-md-6 mt-2"><label>Pelaksana Proyek (Perusahaan)</label><input type="text" name="pelaksana_proyek[]" class="form-control" placeholder="Pelaksana Proyek (Perusahaan)"></div>
                      <div class="col-md-6 mt-2"><label>Referensi (pdf/jpg/png)</label><input type="file" name="referensi_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              

              <!-- Bahasa -->
              <div class="tab-pane fade" id="bahasa" role="tabpanel">
                <div id="bahasa-wrapper">
                  <div class="row mb-2">
                    <div class="col-md-3"><strong>Bahasa</strong></div>
                    <div class="col-md-3"><strong>Speaking</strong></div>
                    <div class="col-md-3"><strong>Reading</strong></div>
                    <div class="col-md-3"><strong>Writing</strong></div>
                  </div>

                  <?php if (!empty($bahasa_list)):
                        $__last = count($bahasa_list)-1;
                        foreach ($bahasa_list as $__i => $r): ?>
                  <div class="row mb-3 repeat-row">
                    <div class="col-md-3"><input type="text" name="bahasa[]" class="form-control" value="<?= v($r,'bahasa') ?>" placeholder="Bahasa"></div>
                    <div class="col-md-3">
                      <?php $sp=v($r,'speaking'); ?>
                      <select name="speaking[]" class="form-control">
                        <option <?= $sp==='Baik'?'selected':''; ?>>Baik</option>
                        <option <?= $sp==='Cukup'?'selected':''; ?>>Cukup</option>
                        <option <?= $sp==='Kurang'?'selected':''; ?>>Kurang</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <?php $rd=v($r,'reading'); ?>
                      <select name="reading[]" class="form-control">
                        <option <?= $rd==='Baik'?'selected':''; ?>>Baik</option>
                        <option <?= $rd==='Cukup'?'selected':''; ?>>Cukup</option>
                        <option <?= $rd==='Kurang'?'selected':''; ?>>Kurang</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <?php $wr=v($r,'writing'); ?>
                      <select name="writing[]" class="form-control">
                        <option <?= $wr==='Baik'?'selected':''; ?>>Baik</option>
                        <option <?= $wr==='Cukup'?'selected':''; ?>>Cukup</option>
                        <option <?= $wr==='Kurang'?'selected':''; ?>>Kurang</option>
                      </select>
                    </div>
                  </div>
                  <?php if ($__i !== $__last): ?><hr class="item-sep"><?php endif; ?>
                  <?php endforeach; else: ?>
                  <div class="row mb-3 repeat-row">
                    <div class="col-md-3"><input type="text" name="bahasa[]" class="form-control" placeholder="Bahasa"></div>
                    <div class="col-md-3">
                      <select name="speaking[]" class="form-control">
                        <option>Baik</option><option>Cukup</option><option>Kurang</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <select name="reading[]" class="form-control">
                        <option>Baik</option><option>Cukup</option><option>Kurang</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <select name="writing[]" class="form-control">
                        <option>Baik</option><option>Cukup</option><option>Kurang</option>
                      </select>
                    </div>
                  </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Sertifikasi -->
              <div class="tab-pane fade" id="sertifikasi" role="tabpanel">
                <div id="sertifikasi-wrapper">
                  <?php if (!empty($sertifikasi_profesi)):
                        $__last = count($sertifikasi_profesi)-1;
                        foreach ($sertifikasi_profesi as $__i => $r): ?>
                    <div class="row mb-3 repeat-row">
                      <div class="col-md-4"><label class="req">Nama Sertifikat</label><input type="text" name="sertifikasi_nama[]" class="form-control" required value="<?= v($r,'nama') ?>"></div>
                      <div class="col-md-4"><label class="req">Penerbit</label><input type="text" name="sertifikasi_penerbit[]" class="form-control" required value="<?= v($r,'penerbit') ?>"></div>
                      <div class="col-md-4"><label class="req">Tahun</label><input type="text" name="sertifikasi_tahun[]" class="form-control" required value="<?= v($r,'tahun') ?>"></div>
                    </div>
                    <?php if ($__i !== $__last): ?><hr class="item-sep"><?php endif; ?>
                  <?php endforeach; else: ?>
                    <div class="row mb-3 repeat-row">
                      <div class="col-md-4"><label class="req">Nama Sertifikat</label><input type="text" name="sertifikasi_nama[]" class="form-control" required placeholder="Nama Sertifikat"></div>
                      <div class="col-md-4"><label class="req">Penerbit</label><input type="text" name="sertifikasi_penerbit[]" class="form-control" required placeholder="Penerbit"></div>
                      <div class="col-md-4"><label class="req">Tahun</label><input type="text" name="sertifikasi_tahun[]" class="form-control" required placeholder="Tahun"></div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Lampiran -->
              <div class="tab-pane fade" id="lampiran" role="tabpanel">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label>KTP/Passport/ID Lainnya</label>
                    <?php if (!empty($lampiran['ktp_file'])): ?><div class="hint-file">File lama: <?= htmlspecialchars(basename($lampiran['ktp_file'])) ?></div><?php endif; ?>
                    <input type="file" name="ktp_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                  </div>
                  <div class="col-md-6">
                    <label>NPWP</label>
                    <?php if (!empty($lampiran['npwp_file'])): ?><div class="hint-file">File lama: <?= htmlspecialchars(basename($lampiran['npwp_file'])) ?></div><?php endif; ?>
                    <input type="file" name="npwp_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                  </div>
                  <div class="col-md-6 mt-3">
                    <label>Bukti Pajak</label>
                    <?php if (!empty($lampiran['bukti_pajak'])): ?><div class="hint-file">File lama: <?= htmlspecialchars(basename($lampiran['bukti_pajak'])) ?></div><?php endif; ?>
                    <input type="file" name="bukti_pajak" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                  </div>
                  <div class="col-md-6 mt-3">
                    <label>Foto</label>
                    <?php if (!empty($lampiran['foto'])): ?><div class="hint-file">File lama: <?= htmlspecialchars(basename($lampiran['foto'])) ?></div><?php endif; ?>
                    <input type="file" name="foto" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                  </div>
                  <div class="col-md-12 mt-3">
                    <label>Lampiran Lainnya</label>
                    <?php if (!empty($lampiran['lainnya'])): ?><div class="hint-file">File lama: <?= htmlspecialchars(basename($lampiran['lainnya'])) ?></div><?php endif; ?>
                    <input type="file" name="lainnya" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                  </div>
                </div>
              </div>
            </div>

            <div class="text-end mt-4">
              <button type="submit" class="btn btn-primary">Simpan CV</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JS: addRow + tombol dinamis + durasi realtime -->
<script>
// Tambah row dengan pemisah otomatis
function addRow(wrapperId, html) {
  const wrapper = document.getElementById(wrapperId);
  if (!wrapper) return;
  const count = wrapper.querySelectorAll('.repeat-row').length;
  if (count > 0) {
    const hr = document.createElement('hr');
    hr.className = 'item-sep';
    wrapper.appendChild(hr);
  }
  const div = document.createElement('div');
  div.classList.add('row','mb-3','repeat-row');
  div.innerHTML = `${html}
    <div class="col-md-1">
      <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('div.row').remove()">&times;</button>
    </div>`;
  wrapper.appendChild(div);
}

document.addEventListener('DOMContentLoaded', function () {
  // ===== Pendidikan Formal
  const pendidikanHTML = `
    <div class="col-md-2"><label class="req">Tingkat</label><input type="text" name="tingkat[]" class="form-control" placeholder="(S1/S2/S3)" required></div>
    <div class="col-md-3"><label class="req">Institusi</label><input type="text" name="institusi[]" class="form-control" placeholder="Nama Institusi" required></div>
    <div class="col-md-2"><label class="req">Jurusan</label><input type="text" name="jurusan[]" class="form-control" placeholder="Jurusan" required></div>
    <div class="col-md-2"><label class="req">Tahun Lulus</label><input type="text" name="tahun_lulus[]" class="form-control" placeholder="Tahun Lulus" required></div>
    <div class="col-md-3"><label>Ijazah</label><input type="file" name="ijazah_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
  `;
  const pendidikanWrap = document.getElementById('pendidikan-wrapper');
  if (pendidikanWrap) {
    const btn = document.createElement('button');
    btn.textContent = '+ Tambah Pendidikan';
    btn.type = 'button';
    btn.className = 'btn btn-success btn-sm mb-3';
    btn.onclick = () => addRow('pendidikan-wrapper', pendidikanHTML);
    pendidikanWrap.insertAdjacentElement('afterend', btn);
  }

  // ===== Pelatihan (Nonformal)
  const nonformalHTML = `
    <div class="col-md-4"><label class="req">Nama Pelatihan</label><input type="text" name="nama_pelatihan[]" class="form-control" required placeholder="Nama Pelatihan"></div>
    <div class="col-md-3"><label class="req">Penyelenggara</label><input type="text" name="penyelenggara[]" class="form-control" required placeholder="Penyelenggara"></div>
    <div class="col-md-2"><label class="req">Tahun</label><input type="text" name="tahun_pelatihan[]" class="form-control" required placeholder="Tahun"></div>
    <div class="col-md-3"><label>Sertifikat</label><input type="file" name="sertifikat_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
  `;
  const nonformalWrap = document.getElementById('nonformal-wrapper');
  if (nonformalWrap) {
    const btn = document.createElement('button');
    btn.textContent = '+ Tambah Pelatihan';
    btn.type = 'button';
    btn.className = 'btn btn-success btn-sm mb-3';
    btn.onclick = () => addRow('nonformal-wrapper', nonformalHTML);
    nonformalWrap.insertAdjacentElement('afterend', btn);
  }

  // ===== Pengalaman
  const pengalamanHTML = `
    <div class="col-md-6"><label class="req">Nama Proyek</label><input type="text" name="nama_kegiatan[]" class="form-control" required placeholder="Nama Proyek"></div>
    <div class="col-md-6"><label class="req">Pemberi Pekerjaan</label><input type="text" name="pemberi_pekerjaan[]" class="form-control" required placeholder="Pemberi Pekerjaan"></div>
    <div class="col-md-6 mt-2"><label>Uraian Proyek</label><input type="text" name="uraian_proyek[]" class="form-control" placeholder="Uraian Proyek"></div>
    <div class="col-md-3 mt-2"><label class="req">Lokasi</label><input type="text" name="lokasi[]" class="form-control" required placeholder="Lokasi (Kota/Provinsi)"></div>
    <div class="col-md-3 mt-2"><label>Negara</label><input type="text" name="negara[]" class="form-control" placeholder="Negara"></div>
    <div class="col-md-3 mt-2"><label class="req">Posisi</label><input type="text" name="posisi_pengalaman[]" class="form-control" required placeholder="Posisi"></div>
    <div class="col-md-3 mt-2"><label>Status</label><input type="text" name="status_pegawai[]" class="form-control" placeholder="Status (Tetap/Kontrak)"></div>
    <div class="col-md-3 mt-2"><label class="req">Waktu Mulai</label><input type="date" name="waktu_mulai[]" class="form-control" required></div>
    <div class="col-md-3 mt-2"><label class="req">Waktu Akhir</label><input type="date" name="waktu_akhir[]" class="form-control" required></div>
    <div class="col-md-6 mt-2"><label>Uraian Tugas</label><input type="text" name="uraian_tugas[]" class="form-control" placeholder="Uraian Tugas"></div>
    <div class="col-md-6 mt-2"><label>Durasi</label><input type="text" name="durasi[]" class="form-control" placeholder="Durasi" readonly></div>
    <div class="col-md-6 mt-2"><label>Pelaksana Proyek (Perusahaan)</label><input type="text" name="pelaksana_proyek[]" class="form-control" placeholder="Pelaksana Proyek (Perusahaan)"></div>
    <div class="col-md-6 mt-2"><label>Referensi</label><input type="file" name="referensi_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
  `;
  const pengalamanWrap = document.getElementById('pengalaman-wrapper');
  if (pengalamanWrap) {
    const btn = document.createElement('button');
    btn.textContent = '+ Tambah Pengalaman';
    btn.type = 'button';
    btn.className = 'btn btn-success btn-sm mb-3';
    btn.onclick = () => addRow('pengalaman-wrapper', pengalamanHTML);
    pengalamanWrap.insertAdjacentElement('afterend', btn);
  }

  
  // ===== Bahasa
  const bahasaHTML = `
    <div class="col-md-3"><label class="form-label">Bahasa</label><input type="text" name="bahasa[]" class="form-control" placeholder="Bahasa"></div>
    <div class="col-md-3"><label class="form-label">Speaking</label><select name="speaking[]" class="form-control"><option>Baik</option><option>Cukup</option><option>Kurang</option></select></div>
    <div class="col-md-3"><label class="form-label">Reading</label><select name="reading[]" class="form-control"><option>Baik</option><option>Cukup</option><option>Kurang</option></select></div>
    <div class="col-md-3"><label class="form-label">Writing</label><select name="writing[]" class="form-control"><option>Baik</option><option>Cukup</option><option>Kurang</option></select></div>
  `;
  const bahasaWrap = document.getElementById('bahasa-wrapper');
  if (bahasaWrap) {
    const btn = document.createElement('button');
    btn.textContent = '+ Tambah Bahasa';
    btn.type = 'button';
    btn.className = 'btn btn-success btn-sm mb-3';
    btn.onclick = () => addRow('bahasa-wrapper', bahasaHTML);
    bahasaWrap.insertAdjacentElement('afterend', btn);
  }

  // ===== Sertifikasi (INI PENTING)
  const sertifikasiHTML = `
    <div class="col-md-4"><label class="req">Nama Sertifikat</label><input type="text" name="sertifikasi_nama[]" class="form-control" required placeholder="Nama Sertifikat"></div>
    <div class="col-md-4"><label class="req">Penerbit</label><input type="text" name="sertifikasi_penerbit[]" class="form-control" required placeholder="Penerbit"></div>
    <div class="col-md-4"><label class="req">Tahun</label><input type="text" name="sertifikasi_tahun[]" class="form-control" required placeholder="Tahun"></div>
  `;
  const sertifikasiWrap = document.getElementById('sertifikasi-wrapper');
  if (sertifikasiWrap) {
    const btn = document.createElement('button');
    btn.textContent = '+ Tambah Sertifikasi';
    btn.type = 'button';
    btn.className = 'btn btn-success btn-sm mb-3';
    btn.onclick = () => addRow('sertifikasi-wrapper', sertifikasiHTML);
    sertifikasiWrap.insertAdjacentElement('afterend', btn);
  }

  // (opsional) preview file
  document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function () {
      if (this.files && this.files[0]) {
        // console.log('File dipilih:', this.files[0].name);
      }
    });
  });
});

// ====== Required-aware Tabs Fix (Bootstrap 5) ======
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form[action*="user/save"]') || document.querySelector('form');
  if (!form) return;

  // Tandai semua field yang awalnya required
  form.querySelectorAll('[required]').forEach(el => el.dataset.required = '1');

  // Sinkronkan required: aktif cuma untuk tab yang kelihatan
  function syncRequiredByTab() {
    document.querySelectorAll('.tab-pane').forEach(pane => {
      const isActive = pane.classList.contains('active') && !pane.classList.contains('fade') || pane.classList.contains('show');
      pane.querySelectorAll('[data-required="1"]').forEach(el => { el.required = !!isActive; });
    });
  }
  syncRequiredByTab();

  // On tab change
  document.querySelectorAll('[data-bs-toggle="tab"]').forEach(btn => {
    btn.addEventListener('shown.bs.tab', syncRequiredByTab);
  });

  // Kalau ada invalid input, auto-buka tabnya & fokus
  form.addEventListener('invalid', function (e) {
    e.preventDefault(); // cegah browser nahan submit default biar kita bisa switch tab dulu
    const el = e.target;
    const pane = el.closest('.tab-pane');
    if (pane && !pane.classList.contains('active')) {
      const trigger = document.querySelector(`[data-bs-target="#${pane.id}"]`);
      if (trigger && window.bootstrap?.Tab) {
        new bootstrap.Tab(trigger).show();
        // kasih sedikit delay biar transisi kelar, baru fokus
        setTimeout(() => el.focus({preventScroll:true}), 150);
      }
    } else {
      el.focus({preventScroll:true});
    }
  }, true);

  // Sebelum submit, pastikan hidden tabs gak punya required
  form.addEventListener('submit', function () {
    document.querySelectorAll('.tab-pane:not(.active) [required]').forEach(el => el.required = false);
  });
});

// Durasi realtime
function diffIndo(startStr, endStr) {
  if (!startStr || !endStr) return '';
  const start = new Date(startStr), end = new Date(endStr);
  if (isNaN(start) || isNaN(end)) return '';
  if (end < start) return 'Tanggal akhir lebih kecil dari mulai';
  let y = end.getFullYear() - start.getFullYear();
  let m = end.getMonth() - start.getMonth();
  let d = end.getDate() - start.getDate();
  if (d < 0) { m -= 1; const daysInPrevMonth = new Date(end.getFullYear(), end.getMonth(), 0).getDate(); d += daysInPrevMonth; }
  if (m < 0) { y -= 1; m += 12; }
  const parts = [];
  if (y > 0) parts.push(`${y} tahun`);
  if (m > 0) parts.push(`${m} bulan`);
  if (d > 0 || parts.length === 0) parts.push(`${d} hari`);
  return parts.join(' ');
}
function updateDurasiForRow(rowEl) {
  const mulai = rowEl.querySelector('input[name="waktu_mulai[]"]');
  const akhir = rowEl.querySelector('input[name="waktu_akhir[]"]');
  const durasi = rowEl.querySelector('input[name="durasi[]"]');
  if (!mulai || !akhir || !durasi) return;
  const val = diffIndo(mulai.value, akhir.value);
  durasi.value = val;
  if (val === 'Tanggal akhir lebih kecil dari mulai') {
    akhir.setCustomValidity('Tanggal akhir tidak boleh lebih kecil dari tanggal mulai.');
  } else {
    akhir.setCustomValidity('');
  }
}
document.addEventListener('DOMContentLoaded', function () {
  const wrap = document.getElementById('pengalaman-wrapper');
  if (!wrap) return;
  wrap.addEventListener('input', function (e) {
    const t = e.target;
    if (t.matches('input[name="waktu_mulai[]"]') || t.matches('input[name="waktu_akhir[]"]')) {
      const row = t.closest('.repeat-row');
      if (row) updateDurasiForRow(row);
    }
  });
  // init existing
  wrap.querySelectorAll('.repeat-row').forEach(updateDurasiForRow);
});
</script>
