<?php $flash = $this->session->flashdata('message') ?: ''; ?>
<style>
    /* ====== Background & page frame ====== */
    body{
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Apple Color Emoji","Segoe UI Emoji";
      background:
        radial-gradient(1200px 600px at 10% 10%, #eaf2ff 0%, rgba(234,242,255,0) 60%),
        radial-gradient(900px 500px at 90% 90%, #eafaf2 0%, rgba(234,250,242,0) 60%),
        #f7f9fc;
      color:#0f172a;
    }
    .auth-wrap{
      min-height:100svh;
      display:flex; align-items:center;
      padding-block:56px;
    }
    @media (max-width: 991.98px){
      .auth-wrap{ align-items:flex-start; padding-block:24px; }
    }

    /* ====== Left hero ====== */
    .hero-badge{
      display:inline-block; font-size:.9rem; font-weight:600;
      background:#eef4ff; color:#0b5ed7; border:1px solid #dbe7ff;
      padding:.35rem .7rem; border-radius:999px;
    }
    .hero-title{
      font-weight:800; line-height:1.1; letter-spacing:.2px;
      font-size: clamp(2rem, 4.6vw, 3.2rem);
    }
    .hero-title .brand{ color:#1e56ff; }
    .hero-title .hilite{ color:#19b45a; }
    .hero-list li::marker{ color:#19b45a; }

    /* ====== Card auth ====== */
    .card-auth{
      border:0; border-radius:20px; overflow:hidden;
      background:#fff;
      box-shadow:0 18px 50px rgba(16,24,40,.10);
      margin-block: 8px; /* natural */
    }
    .card-head{
      padding:18px 20px 14px;
      border-bottom:1px solid rgba(15,23,42,.06);
      background:linear-gradient(180deg,#fff 0%, #f8fbff 100%);
      text-align:center;
    }
    .brand-logo{
      display:inline-block;
      width:auto;
      max-width:220px;   /* kontrol lebar */
      max-height:64px;   /* kontrol tinggi */
      height:auto;
    }
    .card-body-auth{ padding:22px 24px 26px; }

    /* ====== Form grid 2 col ====== */
    .grid-2{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .grid-2 .full{ grid-column: 1 / -1; }
    @media (max-width: 991.98px){ .grid-2{ grid-template-columns:1fr; } }

    /* ====== Inputs ====== */
    .form-control{
      border:1px solid #e5e7eb; padding:.72rem .9rem; border-radius:12px;
      transition: box-shadow .12s ease, border-color .12s ease;
    }
    .form-control:focus{
      border-color:#cfe0ff;
      box-shadow:0 0 0 4px rgba(11,94,215,.12);
    }
    .input-with-icon{ position:relative; }
    .input-with-icon .reveal-btn{
      position:absolute; right:8px; top:50%; transform:translateY(-50%);
      width:40px; height:38px; border-radius:10px;
      display:flex; align-items:center; justify-content:center;
      background:#f2f5ff; border:1px solid #dbe7ff; color:#475467; cursor:pointer;
    }
    .input-with-icon .reveal-btn:hover{ background:#e9efff; }
    .input-with-icon svg{ width:18px; height:18px; }

    /* ====== Submit ====== */
    .btn-primary{
      background:#0b5ed7; border-color:#0b5ed7; border-radius:12px; padding:.9rem 1rem; font-weight:700;
    }
    .btn-primary:disabled{ opacity:.5; cursor:not-allowed; }

    /* ====== Password strength (tambahan kecil) ====== */
    .pw-meter{ height:6px; background:#e5e7eb; border-radius:999px; overflow:hidden; }
    .pw-meter > span{ display:block; height:100%; width:0%; transition:width .2s ease; }
    .pw-weak  { background:#ef4444; }   /* merah */
    .pw-fair  { background:#f59e0b; }   /* oranye */
    .pw-good  { background:#10b981; }   /* hijau muda */
    .pw-strong{ background:#059669; }   /* hijau kuat */
</style>
</head>
<body>
  <div class="container auth-wrap">
    <div class="row w-100 g-4 align-items-center">
      <!-- Left hero -->
      <div class="col-lg-6">
        <div class="hero-badge mb-3">SISTA • PT LAPI ITB</div>
        <h1 class="hero-title mb-3">
          Buat akun <span class="brand">SISTA</span>, data <span class="hilite">tenaga ahli</span> makin rapi.
        </h1>
        <p class="text-muted mb-4" style="max-width:560px">
          Satu akun untuk kelola profil, CV, pendidikan, sertifikasi, dan riwayat proyek—terpusat &amp; siap buat penugasan.
        </p>
        <ul class="hero-list text-muted">
          <li>Validasi data standar &amp; siap pakai</li>
          <li>Desain ringan, responsif, dan aman</li>
          <li>Notifikasi verifikasi &amp; approval admin</li>
        </ul>
      </div>

      <!-- Right card -->
      <div class="col-lg-6">
        <div class="card-auth">
          <div class="card-head">
            <img src="<?= base_url('assets/img/main.png') ?>" class="brand-logo" alt="SISTA">
            <div class="small text-muted mt-1">Daftar Akun Baru</div>
          </div>
          <div class="card-body-auth">
            <form id="registerForm" method="post" action="<?= base_url('auth/register') ?>" novalidate>
              <?php if (function_exists('csrf_field')) { echo csrf_field(); } ?>

              <!-- Grid 2 kolom -->
              <div class="grid-2">
                <!-- Row 1: Nama + Username -->
                <div>
                  <label class="form-label">Nama Lengkap</label>
                  <input type="text" name="name" class="form-control" placeholder="Nama kamu"
                         value="<?= isset($old['name'])?htmlspecialchars($old['name']):'' ?>" required>
                </div>

                <div>
                  <label class="form-label">Username</label>
                  <input type="text" name="username" class="form-control" placeholder="huruf/angka tanpa spasi" pattern="^[A-Za-z0-9_.-]+$"
                         title="Gunakan huruf/angka/titik/garis bawah/tanda minus, tanpa spasi."
                         value="<?= isset($old['username'])?htmlspecialchars($old['username']):'' ?>" required>
                  <!-- teks kecil di bawah username DIHILANGKAN -->
                </div>

                <!-- Row 2: Email (full width) -->
                <div class="full">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" placeholder="nama@contoh.com"
                         value="<?= isset($old['email'])?htmlspecialchars($old['email']):'' ?>" required>
                </div>

                <!-- Row 3: Password + Konfirmasi -->
                <div>
                  <label class="form-label">Password</label>
                  <div class="input-with-icon">
                    <input type="password" id="pwd" name="password" minlength="8" class="form-control" placeholder="min. 8 karakter" required>
                    <button class="reveal-btn" type="button" aria-label="Tampilkan password" data-target="#pwd">
                      <!-- eye icon -->
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                    </button>
                  </div>
                  <!-- meter kekuatan password -->
                  <div class="mt-2">
                    <div class="pw-meter"><span id="pwBar" class="pw-weak" style="width:0%"></span></div>
                    <div id="pwText" class="small text-muted mt-1"></div>
                  </div>
                </div>

                <div>
                  <label class="form-label">Konfirmasi Password</label>
                  <div class="input-with-icon">
                    <input type="password" id="pwd2" name="confirm_password" minlength="8" class="form-control" placeholder="ulang password" required>
                    <button class="reveal-btn" type="button" aria-label="Tampilkan password" data-target="#pwd2">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Terms -->
              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" value="1" id="agreeCheck" required>
                <label class="form-check-label text-muted" for="agreeCheck">
                  Saya setuju dengan ketentuan penggunaan.
                </label>
              </div>

              <!-- Submit -->
              <button id="btnSubmit" class="btn btn-primary w-100 mt-3" type="submit" disabled>Buat Akun</button>

              <div class="text-center mt-3 small text-muted">
                Sudah punya akun? <a href="<?= base_url('auth') ?>">Masuk</a>
              </div>
            </form>
          </div>
        </div>
      </div> <!-- /Right -->
    </div>
  </div>

  <script>
    // Enable/disable submit by agreement checkbox
    const agree = document.getElementById('agreeCheck');
    const submitBtn = document.getElementById('btnSubmit');
    function toggleSubmit(){ submitBtn.disabled = !agree.checked || !matchOK(); }
    agree.addEventListener('change', toggleSubmit);

    // Show/hide password buttons
    document.querySelectorAll('.reveal-btn').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const target = document.querySelector(btn.dataset.target);
        if(!target) return;
        target.type = target.type === 'password' ? 'text' : 'password';
        btn.classList.toggle('active');
      });
    });

    // ====== Password strength + match validation ======
    const pwd  = document.getElementById('pwd');
    const pwd2 = document.getElementById('pwd2');
    const bar  = document.getElementById('pwBar');
    const txt  = document.getElementById('pwText');

    function scorePassword(p){
      let score = 0;
      if (!p) return 0;
      const hasLower = /[a-z]/.test(p);
      const hasUpper = /[A-Z]/.test(p);
      const hasDigit = /\d/.test(p);
      const hasSpec  = /[^A-Za-z0-9]/.test(p);
      const lenOK    = p.length >= 8;

      // base on variety
      score += (hasLower + hasUpper + hasDigit + hasSpec);
      // bonus for length
      if (p.length >= 12) score++;
      if (!lenOK) score = Math.min(score, 1); // force weak if < 8
      return Math.min(score, 5); // cap
    }

    function renderStrength(){
      const s = scorePassword(pwd.value); // 0..5
      let pct=0,label='',cls='pw-weak';
      switch(true){
        case (s <= 1): pct=20; label='Sangat lemah'; cls='pw-weak'; break;
        case (s === 2): pct=40; label='Lemah';        cls='pw-fair'; break;
        case (s === 3): pct=70; label='Cukup';        cls='pw-good'; break;
        default:        pct=100;label='Kuat';         cls='pw-strong'; break;
      }
      bar.className = cls;
      bar.style.width = pct + '%';
      txt.textContent = label;
    }

    function validateMatch(){
      // sinkron: pakai name="password_confirm" (sesuai controller) dan cek di sini
      if (pwd2.value && pwd.value !== pwd2.value){
        pwd2.setCustomValidity('Konfirmasi password tidak sama.');
      } else {
        pwd2.setCustomValidity('');
      }
    }

    function matchOK(){ return pwd2.value && pwd.value === pwd2.value; }

    ['input','blur'].forEach(ev=>{
      pwd.addEventListener(ev, ()=>{ renderStrength(); validateMatch(); toggleSubmit(); });
      pwd2.addEventListener(ev, ()=>{ validateMatch(); toggleSubmit(); });
    });

    // initial state
    renderStrength(); validateMatch(); toggleSubmit();

    // Final guard on submit
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', (e)=>{
      renderStrength(); validateMatch(); toggleSubmit();
      if (!form.checkValidity()){
        e.preventDefault();
        form.reportValidity();
      }
    });
  </script>
