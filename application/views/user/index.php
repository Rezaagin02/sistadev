<?php
// ========== util kecil biar aman kalau h() belum ada ==========
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('initials')) {
  function initials($name){
    $t = trim((string)$name);
    if ($t==='') return 'U';
    $parts = preg_split('/\s+/', $t);
    $ini = strtoupper(mb_substr($parts[0],0,1));
    if (count($parts)>1) $ini .= strtoupper(mb_substr(end($parts),0,1));
    return $ini;
  }
}
if (!function_exists('asset_url')) {
  function asset_url($path){
    if (!$path) return '';
    if (preg_match('~^https?://~i', $path)) return $path;
    return base_url(ltrim($path,'/'));
  }
}
// =============================================================
?>

<style>
/* ======= People cards v3 (responsive) ======= */
.people-grid .person-card{
  border:1px solid #eef0f2; border-radius:16px; background:#fff;
  box-shadow:0 4px 16px rgba(16,24,40,.05); overflow:hidden;
  transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease;
  height:100%;
}
@media (hover:hover) and (pointer:fine){
  .people-grid .person-card:hover{
    transform:translateY(-2px);
    border-color:#e6e9ee;
    box-shadow:0 10px 28px rgba(16,24,40,.10);
  }
}

/* Cover pakai aspect ratio biar konsisten */
.people-grid .person-cover{
  width:100%;
  aspect-ratio: 16 / 9;              /* modern browser */
  object-fit:cover; background:#e9ecef;
}
/* fallback untuk browser lama (tinggalin height fixed) */
@supports not (aspect-ratio: 1) {
  .people-grid .person-cover{ height: 128px; }
}

.people-grid .person-body{ padding:14px 16px 16px; position:relative; }
.people-grid .avatar-wrap{
  position:absolute; top:-28px; left:16px; width:64px; height:64px;
  border-radius:50%; border:3px solid #fff; box-shadow:0 6px 16px rgba(16,24,40,.12);
  background:#98a2b3; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700;
  overflow:hidden;
}
.people-grid .avatar-wrap img{ width:100%; height:100%; object-fit:cover; }

.people-grid .person-name{
  margin-left:84px; font-weight:600; font-size:1rem; line-height:1.2;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.people-grid .person-title{
  margin-left:84px; color:#667085; font-size:.9rem;
  display:flex; gap:.25rem; align-items:center;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.people-grid .person-title .text-truncate{ min-width:0; } /* biar flex-truncate jalan */
.people-grid .person-title .dot{ color:#98a2b3; }
.people-grid .person-loc{
  margin-left:84px; color:#98a2b3; font-size:.86rem;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}

/* Meta chips bisa wrap */
.people-grid .meta{
  display:flex; align-items:center; gap:8px; flex-wrap:wrap;
  margin-top:8px; margin-left:84px; color:#98a2b3; font-size:.84rem;
}
.people-grid .meta .chip{
  padding:.12rem .5rem; border-radius:999px; background:#f3f4f6;
  color:#475467; border:1px solid #eceff3; font-size:.78rem;
}

/* Actions: mobile fullwidth, tablet 2 kolom, desktop inline */
.people-grid .person-actions{
  display:grid; gap:8px; margin-top:12px;
  grid-template-columns: 1fr;              /* mobile */
}
@media (min-width: 576px){
  .people-grid .person-actions{ grid-template-columns: 1fr 1fr; } /* tablet kecil */
}
@media (min-width: 992px){
  .people-grid .person-actions{
    display:flex; gap:8px;                 /* desktop inline */
  }
}

.people-grid .btn-soft{
  border:1px solid #e7e7ea; background:#fff; color:#344054; font-weight:500; padding:.45rem .7rem;
}
.people-grid .btn-soft:hover{ background:#f8fafc; border-color:#dfe3e7; }
.people-grid .btn-primary{ padding:.45rem .8rem; }

/* Avatar & spacing scale down in small screens */
@media (max-width: 575.98px){
  .people-grid .avatar-wrap{ width:56px; height:56px; top:-24px; }
  .people-grid .person-name,
  .people-grid .person-title,
  .people-grid .person-loc,
  .people-grid .meta{ margin-left:78px; }
}
</style>

<div class="container py-3">
  <div class="row g-4">
    <!-- Sidebar kiri (tetap) -->
    <div class="col-md-3 d-none d-md-block">
      <?php $this->load->view('templates/sidebar'); ?>
    </div>

    

    <!-- Main -->
    <div class="col-md-9">
      <?php
$cvProgress = isset($cv_progress) ? max(0, min(100, (int)$cv_progress)) : null;
$missing    = isset($missing_sections) && is_array($missing_sections) ? $missing_sections : [];
$isComplete = ($cvProgress === 100);
?>

<!-- CV Nudge Card (Top) -->
<div class="card border-0 shadow-sm rounded-4 mb-3">
  <div class="card-body p-3 p-sm-4">
    <div class="d-flex align-items-start gap-3 flex-wrap">
      <!-- Icon -->
      <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3"
           style="width:56px;height:56px;background:#eef4ff;">
        <i class="bi bi-file-earmark-person fs-4 text-primary"></i>
      </div>

      <!-- Text -->
      <div class="flex-grow-1">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
          <div>
            <?php if ($isComplete): ?>
              <h5 class="mb-1">Mantap! CV kamu udah 100% ✅</h5>
              <p class="mb-0 text-muted">Biar tetep paling update & relevan, cek lagi data terbaru (proyek/sertifikat/pekerjaan).</p>
            <?php else: ?>
              <h5 class="mb-1">Lengkapi CV kamu biar makin standout ✨</h5>
              <p class="mb-0 text-muted">Lengkapi data penting biar profilmu gampang di-review. Sisa dikit lagi kok!</p>
            <?php endif; ?>
          </div>
          <div class="d-flex gap-2">
            <!-- CTA utama -->
            <a href="<?= base_url('user/form') ?>" class="btn btn-primary">
              <i class="bi bi-pencil-square me-1"></i><?= $isComplete ? 'Perbarui CV' : 'Isi / Update CV' ?>
            </a>
            <!-- CTA sekunder: buka panduan -->
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cvGuideModal">
              <i class="bi bi-list-check me-1"></i> Panduan
            </button>
          </div>
        </div>

        <?php if ($cvProgress !== null): ?>
          <!-- Progress (animated + counter) -->
          <div class="d-flex align-items-center gap-2 mt-3">
            <div class="progress flex-grow-1" style="height:10px;">
              <div
                id="cvProgressBar"
                class="progress-bar progress-bar-striped progress-bar-animated<?= $isComplete ? ' bg-success' : '' ?>"
                role="progressbar"
                aria-valuemin="0" aria-valuemax="100"
                aria-label="Progress kelengkapan CV"
                data-progress="<?= $cvProgress ?>"
                style="width:0%; transition: width .9s ease;">
              </div>
            </div>
            <small id="cvProgressLabel" class="text-muted fw-semibold">0%</small>
          </div>
        <?php endif; ?>

        <?php if (!$isComplete && !empty($missing)): ?>
          <!-- Missing chips -->
          <div class="d-flex flex-wrap gap-2 mt-2">
            <?php foreach ($missing as $m): ?>
              <span class="badge rounded-pill text-bg-light border" title="Belum diisi">
                <i class="bi bi-exclamation-circle me-1"></i><?= h($m) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Modal Panduan Kelengkapan -->
<?php
// mapping section
$ALL_SECTIONS = [
  'Profil Dasar',
  'Pendidikan Formal',
  'Pelatihan/Nonformal',
  'Pengalaman Kerja',
  'Sertifikasi Profesi',
  'Bahasa',
  'Lampiran',
];
$ICON_MAP = [
  'Profil Dasar'        => 'bi-person',
  'Pendidikan Formal'   => 'bi-mortarboard',
  'Pelatihan/Nonformal' => 'bi-journal',
  'Pengalaman Kerja'    => 'bi-briefcase',
  'Sertifikasi Profesi' => 'bi-award',
  'Bahasa'              => 'bi-translate',
  'Lampiran'            => 'bi-paperclip',
];
$items = [];
foreach ($ALL_SECTIONS as $label) {
  $miss = !empty($missing) && in_array($label, $missing, true);
  $items[] = [
    'label'   => $label,
    'missing' => $miss,
    'icon'    => isset($ICON_MAP[$label]) ? $ICON_MAP[$label] : 'bi-check2-circle',
  ];
}
$isComplete = isset($cvProgress) && ((int)$cvProgress === 100);
?>

<style>
/* === Clean modal style === */
#cvGuideModal .modal-header{ border:0; padding-bottom:0 }
#cvGuideModal .modal-title{ font-weight:700 }
#cvGuideModal .subtle { color:#475467; } /* darker than muted */
#cvGuideModal .checklist .row{ --bs-gutter-y:.5rem }
#cvGuideModal .item{
  display:flex; align-items:center; justify-content:space-between;
  padding:.5rem .25rem; border-bottom:1px solid #e5e7eb;
}
#cvGuideModal .item:last-child{ border-bottom:0 }
#cvGuideModal .left{ display:flex; align-items:center; gap:.625rem; min-width:0 }
#cvGuideModal .icon{
  width:28px; height:28px; border-radius:8px; background:#f3f4f6;
  display:inline-flex; align-items:center; justify-content:center; font-size:14px; color:#374151;
}
#cvGuideModal .label{ font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
#cvGuideModal .badge-ok{
  background:#d1fae5; color:#065f46; border:1px solid #10b981;
  border-radius:999px; padding:.15rem .5rem; font-size:.75rem; display:inline-flex; gap:.35rem; align-items:center;
}
#cvGuideModal .badge-miss{
  background:#fef3c7; color:#7c2d12; border:1px solid #f59e0b;
  border-radius:999px; padding:.15rem .5rem; font-size:.75rem; display:inline-flex; gap:.35rem; align-items:center;
}

/* Right summary column */
#cvGuideModal .summary{
  border-left:1px solid #e5e7eb;
}
@media (max-width: 767.98px){
  #cvGuideModal .summary{ border-left:0; border-top:1px solid #e5e7eb; padding-top:1rem; margin-top:.5rem }
}
#cvGuideModal .big{
  font-size:2rem; font-weight:800; line-height:1; color:#111827;
}
#cvGuideModal .progress{ height:8px; background:#e5e7eb }
#cvGuideModal .progress-bar.bg-success{ background:#10b981 !important }
#cvGuideModal .tips .tip{ display:flex; gap:.5rem; align-items:flex-start }
#cvGuideModal .tips i{ margin-top:.15rem }
</style>

<div class="modal fade" id="cvGuideModal" tabindex="-1" aria-labelledby="cvGuideModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content rounded-4 overflow-hidden">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="cvGuideModalLabel"><i class="bi bi-list-check me-2"></i>Panduan Kelengkapan CV</h5>
          <div class="subtle">Cek apa aja yang udah lengkap & yang masih perlu dilengkapi.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <div class="modal-body">
        <div class="row g-4 checklist">
          <!-- LEFT: Checklist -->
          <div class="col-md-7">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Checklist Wajib</div>
              <a href="<?= base_url('user/form') ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil-square me-1"></i> Buka Form CV
              </a>
            </div>

            <div class="list">
              <?php foreach ($items as $it): ?>
                <div class="item">
                  <div class="left">
                    <span class="icon"><i class="bi <?= h($it['icon']) ?>"></i></span>
                    <div class="label"><?= h($it['label']) ?></div>
                  </div>
                  <?php if ($it['missing']): ?>
                    <span class="badge-miss"><i class="bi bi-exclamation-triangle"></i> Belum</span>
                  <?php else: ?>
                    <span class="badge-ok"><i class="bi bi-check2"></i> Lengkap</span>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- RIGHT: Summary -->
          <div class="col-md-5 summary">
            <div class="d-flex align-items-start justify-content-between mb-2">
              <div>
                <div class="fw-semibold">Ringkasan</div>
                <div class="subtle">
                  <?= $isComplete ? 'CV sudah lengkap. Rutin perbarui biar tetap relevan.' : 'Lengkapi checklist di kiri untuk naik ke 100%.' ?>
                </div>
              </div>
              <div class="big"><?= (int)$cvProgress ?>%</div>
            </div>

            <div class="progress mb-3">
              <div class="progress-bar <?= ($cvProgress>=80?'bg-success':'') ?>"
                   role="progressbar"
                   style="width: <?= (int)$cvProgress ?>%;"
                   aria-valuenow="<?= (int)$cvProgress ?>" aria-valuemin="0" aria-valuemax="100">
              </div>
            </div>

            <div class="tips vstack gap-2 mb-3">
              <div class="tip"><i class="bi bi-lightning-charge text-warning"></i><div class="small"><span class="fw-semibold">Tips cepat:</span> isi minimal satu entri untuk tiap bagian.</div></div>
              <div class="tip"><i class="bi bi-stars text-primary"></i><div class="small"><span class="fw-semibold">Stand out:</span> tambah <em>proyek relevan</em> & sertifikasi terbaru.</div></div>
              <div class="tip"><i class="bi bi-shield-check text-success"></i><div class="small"><span class="fw-semibold">Lampiran jelas:</span> unggah KTP/NPWP/Foto yang valid.</div></div>
            </div>

            <div class="d-grid gap-2">
              <a href="<?= base_url('user/form') ?>" class="btn btn-primary">
                <i class="bi bi-arrow-right-circle me-1"></i><?= $isComplete ? 'Perbarui Sekarang' : 'Lengkapi Sekarang' ?>
              </a>
              <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Nanti Dulu</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <div class="me-auto small subtle">
          Terakhir diperbarui: <?= h($user['updated_at'] ?? '') ?>
        </div>
        <a href="<?= base_url('user/form') ?>" class="btn btn-primary">
          <i class="bi bi-pencil-square me-1"></i><?= $isComplete ? 'Perbarui CV' : 'Lengkapi CV' ?>
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


<!-- Animasi progress (width + counter) -->
<script>
(function() {
  var bar   = document.getElementById('cvProgressBar');
  var label = document.getElementById('cvProgressLabel');
  if (!bar || !label) return;

  var target = parseInt(bar.getAttribute('data-progress'), 10) || 0;

  // warna dinamis kalau belum 100 (opsional: ≥80 jadi success)
  if (target >= 80 && target < 100) bar.classList.add('bg-success');

  // Smooth width
  requestAnimationFrame(function() {
    bar.style.width = target + '%';
    bar.setAttribute('aria-valuenow', target);
  });

  // Counter naik pelan
  var current = 0, duration = 700, start = null;
  function step(ts) {
    if (!start) start = ts;
    var p = Math.min((ts - start) / duration, 1);
    current = Math.round(p * target);
    label.textContent = current + '%';
    if (p < 1) requestAnimationFrame(step);
  }
  requestAnimationFrame(step);

  // Reduce motion
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    bar.classList.remove('progress-bar-striped', 'progress-bar-animated');
    bar.style.transition = 'none';
    bar.style.width = target + '%';
    label.textContent = target + '%';
  }
})();
</script>



      <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Orang yang Mungkin Anda Kenal</h4>

        <form class="d-flex ms-auto" action="<?= base_url('user/index') ?>" method="get" style="gap:8px; min-width:260px; max-width:100%;">
          <input type="text" name="q" value="<?= h($q ?? '') ?>" class="form-control"
                 placeholder="Cari nama/posisi/perusahaan">
          <button class="btn btn-primary">Cari</button>
        </form>
      </div>

      <?php if (!empty($people)): ?>
        <!-- Pakai row-cols biar responsif otomatis -->
        <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-3 people-grid">
          <?php foreach ($people as $p): ?>
            <?php
              $name     = $p['name'] ?? ($p['nama'] ?? 'User');
              $pos      = $p['posisi'] ?? '';
              $comp     = $p['perusahaan'] ?? '';
              $city     = $p['domisili_kota'] ?? ($p['cv_domisili_kota'] ?? '');
              $country  = $p['domisili_negara'] ?? ($p['cv_domisili_negara'] ?? '');
              $coverRaw = $p['cover'] ?? '';
              $photoRaw = $p['photo'] ?? '';
              $cover    = $coverRaw ? asset_url($coverRaw) : 'https://images.unsplash.com/photo-1503264116251-35a269479413?auto=format&fit=crop&w=1950&q=80';
              $photo    = $photoRaw ? asset_url($photoRaw) : '';
              $initials = initials($name);
              $profileUrl = base_url('user/profile_view/'.$p['id']);
              $email    = trim((string)($p['email'] ?? ''));
              $metaExp  = isset($p['meta_exp'])  ? (int)$p['meta_exp']  : null;
              $metaCert = isset($p['meta_cert']) ? (int)$p['meta_cert'] : null;
            ?>
            <div class="col">
              <div class="person-card h-100">
                <img src="<?= h($cover) ?>" class="person-cover" alt="cover" loading="lazy" referrerpolicy="no-referrer">
                <div class="person-body">
                  <div class="avatar-wrap" title="<?= h($name) ?>">
                    <?php if ($photo): ?>
                      <img src="<?= h($photo) ?>" alt="avatar" loading="lazy" referrerpolicy="no-referrer">
                    <?php else: ?>
                      <span><?= h($initials) ?></span>
                    <?php endif; ?>
                  </div>

                  <div class="person-name" title="<?= h($name) ?>"><?= h($name) ?></div>

                  <div class="person-title" title="<?= h(trim($pos.' '.($comp?'· '.$comp:''))) ?>">
                    <span class="text-truncate"><?= h($pos ?: '—') ?></span>
                    <?php if ($comp): ?>
                      <span class="dot">·</span>
                      <span class="text-truncate"><?= h($comp) ?></span>
                    <?php endif; ?>
                  </div>

                  <?php if ($city || $country): ?>
                    <div class="person-loc" title="<?= h(trim($city.($country ? ', '.$country : ''))) ?>">
                      <i class="bi bi-geo-alt me-1"></i><?= h(trim($city.($country ? ', '.$country : ''))) ?>
                    </div>
                  <?php endif; ?>

                  <?php if ($metaExp !== null || $metaCert !== null): ?>
                    <div class="meta">
                      <?php if ($metaExp !== null): ?><span class="chip"><?= $metaExp ?> pengalaman</span><?php endif; ?>
                      <?php if ($metaCert !== null): ?><span class="chip"><?= $metaCert ?> sertifikasi</span><?php endif; ?>
                    </div>
                  <?php endif; ?>

                  <div class="person-actions">
                    <a href="<?= $profileUrl ?>" class="btn btn-soft w-100">
                      <i class="bi bi-person-lines-fill me-1"></i>Lihat Profil
                    </a>

                    <?php if ($email !== ''): ?>
                      <a href="mailto:<?= h($email) ?>" class="btn btn-primary w-100">
                        <i class="bi bi-envelope me-1"></i>Hubungi
                      </a>
                    <?php else: ?>
                      <button class="btn btn-primary w-100" type="button" disabled title="Email tidak tersedia">
                        <i class="bi bi-envelope me-1"></i>Hubungi
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if (!empty($pagination_html)): ?>
          <div class="mt-3"><?= $pagination_html ?></div>
        <?php endif; ?>

      <?php else: ?>
        <div class="alert alert-light border">
          Gak ada hasil. Coba ganti kata kunci atau perluas pencarian.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
