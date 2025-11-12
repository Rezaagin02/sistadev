<?php // flash
echo $this->session->flashdata('message') ?: ''; 

$pending = $this->session->userdata('pending_otp') ?: [];
$email   = isset($pending['email']) ? html_escape($pending['email']) : '';
?>

<style>
  :root{
    --primary:#0b5ed7; --accent:#16a34a;
    --ink-900:#111827; --ink-600:#475467; --ink-500:#667085; --ink-400:#98a2b3;
    --radius:18px; --card-w:460px;
  }
  body{
    margin:0; min-height:100vh; color:var(--ink-900);
    background:
      radial-gradient(900px 600px at 14% 22%, rgba(37,99,235,.16), transparent 60%),
      radial-gradient(900px 600px at 86% 86%, rgba(34,197,94,.14), transparent 60%),
      #f7fafc;
    background-attachment:fixed;
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Apple Color Emoji","Segoe UI Emoji";
  }
  .wrap{min-height:100vh; display:flex; align-items:center; justify-content:center; padding:28px 20px;}
  .card-auth{width:100%; max-width:var(--card-w); background:#fff; border:1px solid #e5e7eb; border-radius:var(--radius); box-shadow:0 20px 70px rgba(15,23,42,.12); overflow:hidden;}
  .c-head{ padding:22px 24px 10px; text-align:center;}
  .brand-logo{ height:50px; width:auto; object-fit:contain;}
  .subtitle{ text-align:center; color:var(--ink-400); margin:6px 0 18px;}
  .c-body{ padding:22px 24px 16px;}
  .c-foot{ padding:14px 24px; border-top:1px solid #eef0f2; display:flex; justify-content:center; color:var(--ink-400);}
  .otp-input{ letter-spacing:.4em; font-weight:700; text-align:center; font-size:26px; padding:.8rem 1rem; height:auto; }
  .btn-primary{ background:#0b5ed7; border-color:#0b5ed7; }
  .btn-primary:disabled{ opacity:.6; }
  .muted{ color:var(--ink-500); }
</style>

<div class="wrap">
  <div class="card-auth">
    <div class="c-head">
      <a href="<?= base_url() ?>" aria-label="Homepage">
        <img src="<?= base_url('assets/img/main.png') ?>" alt="Logo" class="brand-logo">
      </a>
      <div class="subtitle">Verifikasi OTP</div>
    </div>

    <div class="c-body">
      <p class="muted">
        Kami telah mengirim <strong>kode OTP</strong> ke <strong><?= $email ?: 'email kamu' ?></strong>.
        Kode berlaku <strong>10 menit</strong>.
      </p>

      <form action="<?= base_url('auth/otp_verify') ?>" method="post" id="otpForm" novalidate>
        <?php if (function_exists('csrf_field')) echo csrf_field(); ?>

        <div class="mb-3">
          <label class="form-label">Kode OTP</label>
          <input type="text" inputmode="numeric" pattern="\d{6}" maxlength="6" class="form-control otp-input" name="code" id="otpCode" placeholder="••••••" required autofocus>
          <div class="form-text">Masukkan 6 digit angka.</div>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" value="1" id="rememberDeviceOtp" name="remember">
          <label class="form-check-label" for="rememberDeviceOtp">Ingat perangkat ini</label>
        </div>

        <div class="d-flex gap-2 align-items-center mb-2">
          <button type="submit" class="btn btn-primary flex-grow-1">Verifikasi</button>
          <button type="button" class="btn btn-outline-secondary" id="btnResend" disabled>Kirim ulang (<span id="cd">60</span>s)</button>
        </div>

        <div class="small muted">Tidak menerima email? Coba cek folder Spam/Junk. Kamu bisa kirim ulang setelah hitungan mundur selesai.</div>
      </form>
    </div>
    <div class="c-foot">© <?= date('Y') ?> PT LAPI ITB</div>
  </div>
</div>

<script>
(function(){
  // Auto paste/format
  const code = document.getElementById('otpCode');
  if(code){
    code.addEventListener('input', (e)=>{
      let v = e.target.value.replace(/\D/g, '').slice(0,6);
      e.target.value = v;
    });
  }

  // Resend cooldown 60s
  let left = 60, btn = document.getElementById('btnResend'), cd = document.getElementById('cd');
  const t = setInterval(()=>{
    left--; if(cd) cd.textContent = left;
    if(left<=0){ clearInterval(t); if(btn){ btn.disabled = false; btn.textContent = 'Kirim ulang'; } }
  }, 1000);

  // Resend handler (AJAX very simple)
  btn && btn.addEventListener('click', async ()=>{
    btn.disabled = true; btn.textContent = 'Mengirim...';
    try{
      const res = await fetch('<?= base_url('auth/otp_resend') ?>', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:new FormData(document.getElementById('otpForm')) });
      const js  = await res.json();
      if(js && js.ok){
        left = 60; cd.textContent = left; btn.textContent = 'Kirim ulang (60s)';
        const nt = setInterval(()=>{ left--; cd.textContent = left; if(left<=0){ clearInterval(nt); btn.disabled=false; btn.textContent='Kirim ulang'; } }, 1000);
      }else{
        alert(js.error || 'Gagal mengirim ulang OTP.');
        btn.disabled = false; btn.textContent = 'Kirim ulang';
      }
    }catch(err){
      alert('Gagal mengirim ulang OTP.');
      btn.disabled = false; btn.textContent = 'Kirim ulang';
    }
  });
})();
</script>
