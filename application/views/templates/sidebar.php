<?php
// 1. Dapatkan instance CodeIgniter untuk akses database & session
$CI =& get_instance();

// 2. Muat session jika belum ada
if (!isset($CI->session)) {
    $CI->load->library('session');
}

// 3. Ambil ID PENGGUNA YANG LOGIN dari session
// Berdasarkan User.php (line 896, 1079, dll), Anda menggunakan key 'id'
$loggedInUserId = (int) $CI->session->userdata('id');

// 4. Siapkan variabel default
$sidebarUser = null;
$sidebarCv = null;
$sidebarCounts = ['pengalaman'=>0, 'pendidikan'=>0, 'pelatihan'=>0, 'sertifikasi'=>0];

// 5. JALANKAN QUERY BARU KHUSUS UNTUK SIDEBAR
// Hanya jalankan jika user sudah login (ID > 0)
if ($loggedInUserId > 0) {
    
    // Ambil data dari tabel 'user' (untuk foto, dll)
    $sidebarUser = $CI->db->get_where('user', ['id' => $loggedInUserId])->row_array();
    
    // Ambil data dari tabel 'cv' (untuk nama, posisi, dll)
    $sidebarCv = $CI->db->get_where('cv', ['user_id' => $loggedInUserId])->row_array();
    
    $sidebarCvId = $sidebarCv['id'] ?? null;

    if ($sidebarCvId) {
        // Ambil hitungan (counts) seperti di controller profile() Anda
        // Saya sesuaikan dengan logika di User.php Anda
        $sidebarCounts['pengalaman']  = $CI->db->where('cv_id', $sidebarCvId)->count_all_results('pengalaman_kerja');
        $sidebarCounts['pendidikan']  = $CI->db->where('cv_id', $sidebarCvId)->count_all_results('pendidikan_formal');
        $sidebarCounts['pelatihan']   = $CI->db->where('cv_id', $sidebarCvId)->count_all_results('pendidikan_nonformal'); // Ditemukan di User.php [baris 1133]
        $sidebarCounts['sertifikasi'] = $CI->db->where('cv_id', $sidebarCvId)->count_all_results('sertifikasi_profesi');
    }
}

// 6. Siapkan variabel untuk view, SEKARANG aman dari konflik
// Variabel ini HANYA menggunakan data dari $sidebarUser dan $sidebarCv
// Ini sesuai dengan logika file sidebar.php Anda sebelumnya:
// - 'nama' & 'posisi' dari CV
// - 'photo' dari USER

$nama     = $sidebarCv['nama'] ?? ($sidebarUser['name'] ?? '—'); // 'name' dari tabel user sebagai cadangan
$posisi   = $sidebarCv['posisi'] ?? '—';
$kota     = $sidebarCv['domisili_kota'] ?? $sidebarUser['domisili_kota'] ?? '';
$prov     = $sidebarCv['domisili_negara'] ?? $sidebarUser['domisili_negara'] ?? '';
$lokasi   = trim($kota . ($prov ? ", $prov" : ''));
$fotoUrl  = !empty($sidebarUser['photo']) ? media_url($sidebarUser['photo']) : ''; // 'photo' dari tabel 'user'
$avatarUi = 'https://ui-avatars.com/api/?name='.urlencode($nama).'&size=160';

$expCount = (int)($sidebarCounts['pengalaman'] ?? 0);
$eduFormal= (int)($sidebarCounts['pendidikan'] ?? 0);
$pelatihan= (int)($sidebarCounts['pelatihan'] ?? 0); // Pastikan ini 'pelatihan' bukan 'pendidikan_nonformal'
$certCount= (int)($sidebarCounts['sertifikasi'] ?? 0);

// Hapus variabel sementara agar tidak bocor ke global scope
unset($CI, $loggedInUserId, $sidebarUser, $sidebarCv, $sidebarCvId, $sidebarCounts);

?>

<style>
/* === TAMBAHKAN INI === */
.sidebar-left {
  position: -webkit-sticky; /* Untuk Safari */
  position: sticky;
  top: 80px; /* <--- SESUAIKAN NILAI INI */
}

.sbx-card{ border:1px solid #e5e7eb; border-radius:18px; overflow:hidden; background:#fff; box-shadow:0 4px 16px rgba(16,24,40,.06); }
.sbx-cover{ background: linear-gradient(135deg, #e8f0ff, #f0fff4); height: 96px; }
.sbx-avatar{ width: 88px; height: 88px; border-radius:50%; border:4px solid #fff; box-shadow:0 8px 20px rgba(0,0,0,.08); margin-top: -44px; object-fit:cover; }
.sbx-name{ font-weight:800; font-size:1.05rem; }
.sbx-role{ color:#667085; }
.sbx-loc { color:#98a2b3; }
.sbx-divider{ height:1px; background:#eef0f2; margin:.75rem 0; }
.sbx-stat{ display:flex; flex-direction:column; align-items:center; justify-content:center; padding:.5rem .25rem; border:1px solid #eef0f2; border-radius:12px; background:#fafafa; }
.sbx-stat .k{ font-weight:800; color:#0f172a; line-height:1; }
.sbx-stat .v{ font-size:.78rem; color:#667085; }
/* suggested list */
.sbx-list .item{ display:flex; gap:.6rem; align-items:flex-start; padding:.5rem .25rem; }
.sbx-list .item + .item{ border-top:1px solid #eef0f2; padding-top:.75rem; margin-top:.75rem; }
.sbx-list .ava{ width:36px; height:36px; border-radius:50%; object-fit:cover; background:#e5e7eb; }
.sbx-list .title{ font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sbx-list .sub{ color:#667085; font-size:.84rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sbx-list .loc{ color:#98a2b3; font-size:.78rem; }
@media (max-width: 575.98px){ .sbx-avatar{ width:76px; height:76px; margin-top:-38px; } }
</style>

<div class="sidebar-left">

  <!-- PROFILE -->
  <div class="sbx-card mb-3">
    <div class="sbx-cover"></div>
    <div class="text-center px-3">
      <img src="<?= h($fotoUrl ?: $avatarUi) ?>" alt="Avatar" class="sbx-avatar" referrerpolicy="no-referrer">
      <div class="pt-2">
        <div class="sbx-name"><?= h($nama) ?></div>
        <div class="sbx-role"><?= h($posisi ?: '—') ?></div>
        <div class="sbx-loc mt-1"><i class="bi bi-geo-alt me-1"></i><?= $lokasi ? h($lokasi) : '—' ?></div>
      </div>

      <div class="sbx-divider"></div>

      <!-- Stats grid 2x2 -->
      <div class="row g-2 px-1 pb-3">
        <div class="col-6">
          <div class="sbx-stat"><div class="k"><?= $expCount ?></div><div class="v">Pengalaman</div></div>
        </div>
        <div class="col-6">
          <div class="sbx-stat"><div class="k"><?= $eduFormal ?></div><div class="v">Pendidikan</div></div>
        </div>
        <div class="col-6">
          <div class="sbx-stat"><div class="k"><?= $pelatihan ?></div><div class="v">Pelatihan</div></div>
        </div>
        <div class="col-6">
          <div class="sbx-stat"><div class="k"><?= $certCount ?></div><div class="v">Sertifikasi</div></div>
        </div>
      </div>
    </div>
  </div>

  <!-- SUGGESTED PEOPLE (replace Aktivitas) -->
  <?php
// EXPECTED VARS:
// $suggested_people -> array top 5 (id,name,photo,posisi,perusahaan,domisili_kota,domisili_negara)
// helper h() untuk htmlspecialchars & media_url() untuk path gambar lokal

if (!function_exists('h')) {
  function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
?>

<style>
/* === Profil Terlengkap & Terbaru (Sidebar Card) === */
.sx-card{border:1px solid #e5e7eb;border-radius:16px;background:#fff;box-shadow:0 4px 16px rgba(16,24,40,.06);overflow:hidden}
.sx-head{position:relative;padding:14px 14px 10px;display:flex;align-items:center;gap:.6rem}
.sx-head::before{content:"";position:absolute;inset:0 0 auto 0;height:4px;border-radius:16px 16px 0 0;background:linear-gradient(90deg,#2563eb 0%,#22c55e 100%);opacity:.18}
.sx-ico{width:28px;height:28px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;background:#eef2ff;color:#2563eb;font-size:14px;flex:0 0 28px;box-shadow:0 1px 0 rgba(0,0,0,.03) inset}
.sx-titles{min-width:0}
.sx-title{margin:0;font-weight:800;font-size:1rem;line-height:1.05;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sx-sub{margin:0;color:#667085;font-size:.84rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

.sx-list .sx-item{display:flex;gap:.75rem;align-items:center;padding:12px 14px;border-top:1px solid #eef0f2;text-decoration:none;color:inherit;transition:background .15s ease}
.sx-list .sx-item:hover{background:#f8fafc}
.sx-ava{flex:0 0 36px;width:36px;height:36px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;font-weight:700;color:#475467;font-size:.8rem}
.sx-ava img{width:100%;height:100%;object-fit:cover}
.sx-main{min-width:0}
.sx-name{font-weight:500;line-height:1.15;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sx-role{color:#4b5563;font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sx-loc {color:#9aa3af;font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

.sx-foot{padding:10px 14px;border-top:1px solid #eef0f2;display:flex;justify-content:center}
.sx-seeall{display:inline-flex;align-items:center;gap:.4rem;font-weight:600;font-size:.9rem;text-decoration:none;color:#2563eb}
.sx-seeall:hover{text-decoration:underline}

@media (max-width: 575.98px){
  .sx-ava{width:34px;height:34px}
  .sx-head{padding:12px}
  .sx-list .sx-item{padding:10px 12px}
}
</style>

<div class="sx-card mb-3" aria-label="Profil Terlengkap & Terbaru">
  <!-- Header -->
  <div class="sx-head">
    <span class="sx-ico"><i class="bi bi-stars"></i></span>
    <div class="sx-titles">
      <h6 class="sx-title">Profil Terlengkap &amp; Terbaru</h6>
      <p class="sx-sub">Top 5 akun paling lengkap &amp; update</p>
    </div>
  </div>

  <!-- List -->
  <?php if (!empty($suggested_people)): ?>
    <div class="sx-list">
      <?php foreach ($suggested_people as $p):
        $pName = $p['name'] ?? 'User';
        $pPos  = $p['posisi'] ?? '';
        $pComp = $p['perusahaan'] ?? '';
        $pLoc  = trim(($p['domisili_kota'] ?? '').(($p['domisili_negara'] ?? '') ? ', '.$p['domisili_negara'] : ''));
        $pPhoto= !empty($p['photo']) ? media_url($p['photo']) : '';
        // inisial fallback: huruf awal nama & nama belakang
        $parts = preg_split('/\s+/', trim($pName));
        $init  = strtoupper(mb_substr($parts[0] ?? 'U',0,1).(count($parts)>1 ? mb_substr(end($parts),0,1) : ''));
        $url   = base_url('user/profile_view/'.$p['id']);
      ?>
      <a href="<?= $url ?>" class="sx-item" title="<?= h($pName) ?>">
        <div class="sx-ava" aria-hidden="true">
          <?php if ($pPhoto): ?>
            <img src="<?= h($pPhoto) ?>" alt="">
          <?php else: ?>
            <span><?= h($init) ?></span>
          <?php endif; ?>
        </div>
        <div class="sx-main">
          <div class="sx-name"><?= h($pName) ?></div>
          <div class="sx-role">
            <?= h($pPos ?: '—') ?><?= $pComp ? ' · '.h($pComp) : '' ?>
          </div>
          <?php if ($pLoc): ?>
            <div class="sx-loc"><i class="bi bi-geo-alt me-1"></i><?= h($pLoc) ?></div>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Footer CTA -->
    <div class="sx-foot">
      <a href="<?= base_url('user') ?>" class="sx-seeall">
        <i class="bi bi-people"></i> Lihat semua
      </a>
    </div>
  <?php else: ?>
    <div class="px-3 pb-3 text-muted small">Belum ada rekomendasi.</div>
  <?php endif; ?>
</div>




</div>
