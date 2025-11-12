<?php
// --- data minimal dari controller ---
$uemail  = html_escape($user['email'] ?? '');
$uname   = html_escape($user['name'] ?? '');
$updated = html_escape($user['updated_at'] ?? '-');
?>

<style>
  /* Sticky sidebar di desktop */
  @media (min-width: 992px){
    .settings-sticky{ position: sticky; top: 88px; }
  }

  /* Badge style kecil */
  .s-badge{
    background: #eef4ff; color:#0b5ed7; border:1px solid #dce8ff;
  }

  /* Sidebar groups + pills */
  #settingsMenu .s-group{
    font-size: .72rem; letter-spacing:.02em;
    text-transform: uppercase; color:#98a2b3;
    padding: 4px 10px 2px;
  }
  #settingsMenu .s-pill{
    border-radius: 14px; padding: .55rem .6rem;
    border: 1px solid transparent;
    display:flex; gap:.6rem; align-items:center;
    transition: background .12s ease, border-color .12s ease, color .12s ease, transform .08s ease;
  }
  #settingsMenu .s-pill:hover{ background:#f7faff; border-color:#e8eefc; }
  #settingsMenu .s-pill.active{
    background:#eef4ff; border-color:#dbe7ff; color:#0b5ed7; font-weight:600;
    box-shadow: 0 4px 14px rgba(11,94,215,.08);
  }
  #settingsMenu .s-ico{
    width: 36px; height: 36px; border-radius: 12px;
    display:flex; align-items:center; justify-content:center;
    background:#f0f4ff; color:#0b5ed7; flex: 0 0 auto;
  }
  #settingsMenu .s-text{ flex:1 1 auto; }
  #settingsMenu .s-chevron{ color:#c0c7d6; }
  #settingsMenu .s-pill.is-disabled{
    opacity:.55; cursor:not-allowed;
  }
  #settingsMenu .s-pill.is-disabled:hover{
    background:transparent; border-color:transparent;
  }
  #settingsMenu .s-pill .s-tag{
    font-size: .72rem; background:#f1f5f9; color:#64748b;
    border-radius:999px; padding:.08rem .45rem; margin-left:auto;
  }

  /* Head icon tiap section */
  .head-ico{
    width: 34px; height: 34px;
    border-radius: 10px;
    background: #f2f5ff;
    color:#0b5ed7;
    display:flex; align-items:center; justify-content:center;
  }

  /* Transisi pane */
  .set-pane{ transition: opacity .15s ease; }
  .set-pane.d-none{ opacity: 0; }
</style>

<div class="container py-2">
  <!-- Page header -->
  <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
    

    <!-- Mobile dropdown: hanya 3 opsi aktif -->
    <div class="d-lg-none mt-2">
      <label for="settingsSelect" class="visually-hidden">Pilih bagian pengaturan</label>
      <select id="settingsSelect" class="form-select form-select-sm">
        <option value="overview">Ringkasan</option>
        <option value="email">Ganti Email</option>
        <option value="password">Ganti Password</option>
      </select>
    </div>
  </div>

  <div class="row g-3">
    <!-- Sidebar Settings -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 settings-sticky" aria-label="Menu Pengaturan">
        <div class="card-body p-0">
          <!-- header mini -->
          <div class="px-3 pt-3 pb-2 d-flex align-items-center justify-content-between">
            <div>
              <div class="fw-bold">Pengaturan</div>
              <div class="text-muted small">Akun & Keamanan</div>
            </div>
            <span class="badge s-badge">Basic</span>
          </div>

          <nav id="settingsMenu" class="nav flex-column gap-1 p-2">
            <div class="s-group">Dasar</div>

            <!-- Aktif -->
            <a href="#overview" class="nav-link s-pill d-flex align-items-center" aria-current="page">
              <span class="s-ico"><i class="bi bi-speedometer2"></i></span>
              <span class="s-text">Ringkasan</span>
              <i class="bi bi-chevron-right s-chevron"></i>
            </a>

            <a href="#email" class="nav-link s-pill d-flex align-items-center">
              <span class="s-ico"><i class="bi bi-envelope"></i></span>
              <span class="s-text">Ganti Email</span>
              <i class="bi bi-chevron-right s-chevron"></i>
            </a>

            <a href="#password" class="nav-link s-pill d-flex align-items-center">
              <span class="s-ico"><i class="bi bi-shield-lock"></i></span>
              <span class="s-text">Ganti Password</span>
              <i class="bi bi-chevron-right s-chevron"></i>
            </a>

            <div class="s-group mt-2">Lainnya</div>

            <!-- Disabled (Soon) -->
            <a href="#account" class="nav-link s-pill is-disabled d-flex align-items-center"
               tabindex="-1" aria-disabled="true" data-bs-toggle="tooltip" data-bs-title="Segera hadir">
              <span class="s-ico"><i class="bi bi-person-badge"></i></span>
              <span class="s-text">Akun</span>
              <span class="s-tag">Soon</span>
            </a>

            <a href="#notifications" class="nav-link s-pill is-disabled d-flex align-items-center"
               tabindex="-1" aria-disabled="true" data-bs-toggle="tooltip" data-bs-title="Segera hadir">
              <span class="s-ico"><i class="bi bi-bell"></i></span>
              <span class="s-text">Notifikasi</span>
              <span class="s-tag">Soon</span>
            </a>

            <a href="#security" class="nav-link s-pill is-disabled d-flex align-items-center"
               tabindex="-1" aria-disabled="true" data-bs-toggle="tooltip" data-bs-title="Segera hadir">
              <span class="s-ico"><i class="bi bi-fingerprint"></i></span>
              <span class="s-text">Keamanan Lanjutan</span>
              <span class="s-tag">Soon</span>
            </a>

            <a href="#privacy" class="nav-link s-pill is-disabled d-flex align-items-center"
               tabindex="-1" aria-disabled="true" data-bs-toggle="tooltip" data-bs-title="Segera hadir">
              <span class="s-ico"><i class="bi bi-shield-check"></i></span>
              <span class="s-text">Privasi & Data</span>
              <span class="s-tag">Soon</span>
            </a>

            <a href="#appearance" class="nav-link s-pill is-disabled d-flex align-items-center"
               tabindex="-1" aria-disabled="true" data-bs-toggle="tooltip" data-bs-title="Segera hadir">
              <span class="s-ico"><i class="bi bi-palette"></i></span>
              <span class="s-text">Tampilan</span>
              <span class="s-tag">Soon</span>
            </a>
          </nav>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="col-lg-8">

      <!-- Overview -->
      <section id="overview" class="set-pane card border-0 shadow-sm rounded-4 mb-3" aria-labelledby="title-overview">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="head-ico"><i class="bi bi-speedometer2"></i></div>
            <h5 class="mb-0" id="title-overview">Ringkasan</h5>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="text-muted small">Nama</div>
              <div class="fw-semibold"><?= $uname ?: '—' ?></div>
            </div>
            <div class="col-md-6">
              <div class="text-muted small">Email</div>
              <div class="fw-semibold"><?= $uemail ?></div>
            </div>
            <div class="col-md-6">
              <div class="text-muted small">Terakhir diperbarui</div>
              <div class="fw-semibold"><?= $updated ?></div>
            </div>
          </div>
          <hr>
          <div class="small text-muted">
            Butuh update email atau password? Pilih menu di kiri (atau dropdown di HP).
          </div>
        </div>
      </section>

      <!-- Change Email -->
      <section id="email" class="set-pane card border-0 shadow-sm rounded-4 mb-3 d-none" aria-labelledby="title-email">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="head-ico"><i class="bi bi-envelope"></i></div>
            <h5 class="mb-0" id="title-email">Ganti Email</h5>
          </div>
          <hr class="mt-2">
          <form id="formEmail" method="post" action="<?= base_url('settings/email') ?>">
            <div class="mb-3">
              <label class="form-label">Email Saat Ini</label>
              <input type="email" class="form-control" value="<?= $uemail ?>" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Email Baru</label>
              <input type="email" name="new_email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Konfirmasi Email Baru</label>
              <input type="email" name="confirm_email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password Saat Ini</label>
              <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-primary">Simpan</button>
              <button class="btn btn-light" type="button" onclick="Settings.goto('overview')">Batal</button>
            </div>
          </form>
        </div>
      </section>

      <!-- Change Password -->
      <section id="password" class="set-pane card border-0 shadow-sm rounded-4 mb-3 d-none" aria-labelledby="title-password">
  <section id="password" class="set-pane card border-0 shadow-sm rounded-4 mb-3 d-none" aria-labelledby="title-password">
  <div class="card-body">
    <div class="d-flex align-items-center gap-2 mb-2">
      <div class="head-ico"><i class="bi bi-shield-lock"></i></div>
      <h5 class="mb-0" id="title-password">Ganti Password</h5>
    </div>
    <hr class="mt-2">

    <form id="formPassword" method="post" action="<?= base_url('settings/change_password') ?>" novalidate>
      <!-- Password Saat Ini -->
      <div class="mb-3">
        <label class="form-label">Password Saat Ini</label>
        <div class="input-group has-validation">
          <input type="password" name="current_password" id="curPwd" class="form-control" required minlength="6">
          <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('curPwd')"><i class="bi bi-eye"></i></button>
          <div class="invalid-feedback">Wajib diisi (min. 6 karakter).</div>
        </div>
      </div>

      <!-- Password Baru -->
      <div class="mb-3">
        <label class="form-label">Password Baru</label>
        <div class="input-group has-validation">
          <input type="password" name="new_password" id="newPwd" class="form-control" required minlength="8" autocomplete="new-password">
          <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('newPwd')"><i class="bi bi-eye"></i></button>
          <div class="invalid-feedback">Minimal 8 karakter.</div>
        </div>
        <small class="text-muted">
          Minimal 8 karakter. Pakai 3 dari 4 jenis (a-z, A-Z, 0-9, simbol) atau passphrase ≥14.
        </small>
        <div class="progress mt-2" style="height:6px;">
          <div id="strengthBar" class="progress-bar" role="progressbar" style="width:0%"></div>
        </div>
      </div>

      <!-- Konfirmasi Password Baru -->
      <div class="mb-3">
        <label class="form-label">Konfirmasi Password Baru</label>
        <div class="input-group has-validation">
          <input type="password" name="confirm_password" id="confPwd" class="form-control" required minlength="8" autocomplete="new-password">
          <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('confPwd')"><i class="bi bi-eye"></i></button>
          <div class="invalid-feedback" id="confFeedback">Konfirmasi tidak sama dengan password baru.</div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary" id="btnSavePw">Simpan</button>
        <button class="btn btn-light" type="button" onclick="Settings.goto('overview')">Batal</button>
      </div>
    </form>
  </div>
</section>

<script>
  function toggleVis(id){
    const el = document.getElementById(id);
    el.type = (el.type === 'password') ? 'text' : 'password';
  }

  // strength meter (min 8)
  (()=>{
    const input = document.getElementById('newPwd');
    const bar   = document.getElementById('strengthBar');
    if (!input || !bar) return;

    input.addEventListener('input', () => {
      const v = input.value || '';
      let s = 0;
      if (v.length >= 8)  s += 1;
      if (v.length >= 12) s += 1;
      if (v.length >= 14) s += 1; // passphrase bonus
      if (/[a-z]/.test(v)) s += 1;
      if (/[A-Z]/.test(v)) s += 1;
      if (/\d/.test(v))    s += 1;
      if (/[^A-Za-z0-9]/.test(v)) s += 1;

      const pct = Math.min(100, (s/7)*100);
      bar.style.width = pct + '%';
      bar.className = 'progress-bar';
      if (pct < 34) bar.classList.add('bg-danger');
      else if (pct < 67) bar.classList.add('bg-warning');
      else bar.classList.add('bg-success');
    });
  })();

  // live match check for confirm password
  (()=>{
    const form = document.getElementById('formPassword');
    const newPwd = document.getElementById('newPwd');
    const confPwd = document.getElementById('confPwd');
    const btn = document.getElementById('btnSavePw');
    const feedback = document.getElementById('confFeedback');

    function validateMatch(){
      const a = newPwd.value || '';
      const b = confPwd.value || '';
      if (!b) {
        confPwd.setCustomValidity('Harus dikonfirmasi.');
        confPwd.classList.add('is-invalid');
        feedback.textContent = 'Harus dikonfirmasi.';
        return false;
      }
      if (a !== b){
        confPwd.setCustomValidity('Konfirmasi tidak sama dengan password baru.');
        confPwd.classList.add('is-invalid');
        feedback.textContent = 'Konfirmasi tidak sama dengan password baru.';
        return false;
      }
      confPwd.setCustomValidity('');
      confPwd.classList.remove('is-invalid');
      return true;
    }

    // realtime validation
    newPwd.addEventListener('input', validateMatch);
    confPwd.addEventListener('input', validateMatch);

    form.addEventListener('submit', (e)=>{
      // trigger HTML5 validation
      if (!form.checkValidity() || !validateMatch()){
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  })();
</script>


      <!-- Pane lain (disabled) tetap ada tapi hidden; kalau nanti diaktifin tinggal hapus class d-none & is-disabled di menu -->
      <section id="account" class="set-pane card border-0 shadow-sm rounded-4 mb-3 d-none" aria-hidden="true"></section>
      <section id="notifications" class="set-pane card border-0 shadow-sm rounded-4 mb-3 d-none" aria-hidden="true"></section>
      <section id="security" class="set-pane card border-0 shadow-sm rounded-4 mb-3 d-none" aria-hidden="true"></section>
      <section id="privacy" class="set-pane card border-0 shadow-sm rounded-4 mb-3 d-none" aria-hidden="true"></section>
      <section id="appearance" class="set-pane card border-0 shadow-sm rounded-4 mb-3 d-none" aria-hidden="true"></section>

    </div>
  </div>
</div>

<script>
  // SPA-like switcher
  const Settings = (() => {
    const panes = () => Array.from(document.querySelectorAll('.set-pane'));
    const menu  = document.getElementById('settingsMenu');
    const select= document.getElementById('settingsSelect');

    function setActive(id){
      panes().forEach(p => p.classList.toggle('d-none', p.id !== id));
      if (menu){
        [...menu.querySelectorAll('a')].forEach(a=>{
          a.classList.toggle('active', a.getAttribute('href') === '#'+id);
          a.setAttribute('aria-current', a.classList.contains('active') ? 'page' : 'false');
        });
      }
      if (select){
        [...select.options].forEach(o => o.selected = (o.value === id));
      }
      try{ localStorage.setItem('settings:last', id); }catch(e){}
    }

    function goto(id){
      history.replaceState(null, '', '#'+id);
      setActive(id);
      const el = document.getElementById(id);
      if (el) window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 20, behavior:'smooth' });
    }

    function init(){
      // Sidebar clicks
      if (menu) {
        menu.addEventListener('click', (e)=>{
          const a = e.target.closest('a[href^="#"]');
          if(!a || a.classList.contains('is-disabled')) return;
          e.preventDefault();
          goto(a.getAttribute('href').substring(1));
        });
      }
      // Mobile select
      if (select){
        select.addEventListener('change', (e)=> goto(e.target.value));
      }
      // Initial section: from hash > localStorage > overview
      let target = (location.hash || '').replace('#','').trim();
      if(!target){
        try{ target = localStorage.getItem('settings:last') || 'overview'; }catch(e){ target = 'overview'; }
      }
      setActive(target);

      // Sync if hash changes (e.g., back/forward)
      window.addEventListener('hashchange', () => {
        const t = (location.hash || '').replace('#','').trim() || 'overview';
        setActive(t);
      });
    }

    function save(section){
      // placeholder submit normal (bisa ganti fetch/AJAX)
      const id = 'form' + section.charAt(0).toUpperCase() + section.slice(1);
      const form = document.getElementById(id);
      if (form) form.submit();
    }

    return { init, goto, save };
  })();

  document.addEventListener('DOMContentLoaded', Settings.init);
</script>

<script>
  // Enable-only: overview, email, password. Lainnya disabled + tooltip.
  document.addEventListener('DOMContentLoaded', () => {
    const enableIds = new Set(['#overview', '#email', '#password']);
    document.querySelectorAll('#settingsMenu .s-pill').forEach(a=>{
      const href = a.getAttribute('href');
      if (!enableIds.has(href)) a.classList.add('is-disabled');
    });
    // prevent click
    document.querySelectorAll('#settingsMenu .is-disabled').forEach(a=>{
      a.addEventListener('click', e => e.preventDefault());
    });
    // tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el=>{
      new bootstrap.Tooltip(el);
    });
  });
</script>
