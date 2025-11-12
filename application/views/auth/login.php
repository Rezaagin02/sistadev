<?php // flash message tetap jalan ?>
<?= $this->session->flashdata('message'); ?>

<style>
  :root{
    --primary:#0b5ed7;  /* biru corporate */
    --accent:#16a34a;   /* hijau aksen */
    --ink-900:#111827;
    --ink-600:#475467;
    --ink-500:#667085;
    --ink-400:#98a2b3;
    --radius:18px;
    --card-w: 460px;
  }

  /* ===== Canvas & background ===== */
  body{
    margin:0; min-height:100vh; color:var(--ink-900);
    background:
      radial-gradient(900px 600px at 14% 22%, rgba(37,99,235,.16), transparent 60%),
      radial-gradient(900px 600px at 86% 86%, rgba(34,197,94,.14), transparent 60%),
      #f7fafc;
    background-attachment:fixed;
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Apple Color Emoji","Segoe UI Emoji";
  }

  /* ===== Split grid ===== */
  .auth-grid{
    min-height:100vh;
    display:grid;
    grid-template-columns: 1fr;      /* mobile: 1 kolom */
  }
  @media (min-width: 992px){
    .auth-grid{
      grid-template-columns: 1.1fr .9fr; /* kiri : kanan */
      align-items:center;
    }
  }

  /* ===== Left hero ===== */
  .hero{
    display:none;
  }
  @media (min-width: 992px){
    .hero{ display:flex; align-items:center; justify-content:center; }
    .hero-wrap{ max-width:720px; padding:64px 56px; }
    .hero-logo{ height:52px; width:auto; display:block; margin-bottom:18px; }
    .hero h1{ font-size:48px; line-height:1.1; letter-spacing:.2px; margin:0 0 14px; font-weight:900; }
    .hero p.lead{ color:var(--ink-500); margin:0 0 22px; max-width:620px; }
    .hero .bullets{ color:var(--ink-600); margin:18px 0 0; }
    .hero .bullets li{ margin:6px 0; }
    .hero strong.blue{ color:#2563eb; }
    .hero strong.green{ color:#10b981; }
  }

  /* ===== Right panel (form) ===== */
  .panel{
    display:flex; align-items:center; justify-content:center;
    padding:28px 20px 40px;
  }
  /* Desktop: geser form sedikit ke kiri */
  @media (min-width: 992px){
    .panel{
      justify-content: flex-end;        /* tetap di sisi kanan area kanan */
      padding: 48px 56px;               /* padding default */
    }
    .card-auth{
      /* jarak dari sisi kanan layar -> makin besar nilainya makin ke kiri */
      margin-right: clamp(24px, 6vw, 120px);
    }
  }


  .card-auth{
    width:100%; max-width:var(--card-w);
    background:#fff; border:1px solid #e5e7eb; border-radius:var(--radius);
    box-shadow:0 20px 70px rgba(15,23,42,.12);
    overflow:hidden;
  }

  .c-head{ padding:22px 24px 0; display:flex; justify-content:center; }
  .brand-logo{ height:50px; width:auto; object-fit:contain; display:block; }
  @media (max-width:575.98px){ .brand-logo{ height:44px; } }

  .subtitle{ text-align:center; color:var(--ink-400); margin:6px 0 18px; }

  .c-body{ padding:22px 24px 16px; }
  .c-body .form-label{ font-weight:600; margin-bottom:6px; }

  .input-group .btn-toggle{ border-color:#ced4da; }
  .input-group .btn-toggle:hover{ background:#f3f4f6; border-color:#bfc6cf; }

  .meta-row{
    display:flex; justify-content:space-between; align-items:center; gap:12px;
    margin:10px 0 14px;
  }
  .meta-row a{ text-decoration:none; }
  .meta-row a:hover{ text-decoration:underline; }

  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ filter:saturate(1.05) brightness(.98); }

  .c-foot{
    padding:14px 24px; border-top:1px solid #eef0f2;
    display:flex; justify-content:center; color:var(--ink-400);
  }

  /* Form control height konsisten */
  .form-control{ height:44px; }
  .input-group .btn{ height:44px; }

  /* Reduce motion */
  @media (prefers-reduced-motion:reduce){
    *{ scroll-behavior:auto; transition:none !important; }
  }
</style>

<div class="auth-grid">

  <!-- LEFT: hero -->
  <aside class="hero">
    <div class="hero-wrap">
      <div class="hero-inner position-relative">
  <img src="<?= base_url('assets/img/main.png') ?>" alt="Logo" style="height:52px;width:auto;object-fit:contain;display:block;margin-bottom:14px;">

  <h1 class="display-6 mb-2">
    Kelola <span style="color:#2563eb">data tenaga ahli</span>
    secara <span style="color:#10b981">akurat</span> & terpusat.
  </h1>

  <p class="lead" style="color:#667085;max-width:640px">
    SISTA bantu tim mendata profil tenaga ahli—dari CV, pendidikan, sertifikasi, sampai riwayat proyek—lengkap, rapi, dan siap dipakai untuk penugasan.
  </p>

 

  
</div>

    </div>
  </aside>

  <!-- RIGHT: form -->
  <main class="panel">
    <div class="card-auth">
      <div class="c-head">
        <a href="<?= base_url() ?>" aria-label="Homepage">
          <img src="<?= base_url('assets/img/main.png') ?>" alt="Logo" class="brand-logo">
        </a>
      </div>
      <div class="subtitle">Silakan masuk untuk melanjutkan</div>

      <div class="c-body">
        <form action="<?= base_url('auth') ?>" method="post" novalidate>
          <div class="mb-3">
            <label for="loginEmail" class="form-label">Email</label>
            <input type="email" name="email" id="loginEmail" class="form-control" autocomplete="username" required>
            <?= form_error('email', '<small class="text-danger">', '</small>'); ?>
          </div>

          <div class="mb-2">
            <label for="loginPassword" class="form-label">Password <i class="bi bi-eye"></i></label>
            <div class="input-group">
              <input type="password" name="password" id="loginPassword" class="form-control" autocomplete="current-password" required>
              <button class="btn btn-outline-secondary btn-toggle" type="button" id="togglePw" aria-label="Tampilkan password">
                show
              </button>
            </div>
            <?= form_error('password', '<small class="text-danger">', '</small>'); ?>
          </div>

          <div class="meta-row">
            <div class="form-check m-0">
              <!-- <input class="form-check-input" type="checkbox" value="1" id="rememberDevice" name="remember">
              <label class="form-check-label" for="rememberDevice">Ingat perangkat ini</label> -->
            </div>
            <a href="<?= base_url('auth/forgot') ?>">Lupa password?</a>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 mb-2">Sign In</button>

          <div class="text-center small text-muted">
            Belum punya akun? <a href="<?= base_url('auth/register') ?>">Registrasi</a>
          </div>
        </form>
      </div>

      <div class="c-foot">© <?= date('Y') ?> PT LAPI ITB</div>
    </div>
  </main>
</div>

<script>
  // Toggle show/hide password
  (function(){
    const btn = document.getElementById('togglePw');
    const pw  = document.getElementById('loginPassword');
    if(btn && pw){
      btn.addEventListener('click', function(){
        const show = pw.type === 'text';
        pw.type = show ? 'password' : 'text';
        this.innerHTML = show ? 'show' : 'hide';
        this.setAttribute('aria-label', show ? 'Tampilkan password' : 'Sembunyikan password');
      });
    }
  })();
</script>
