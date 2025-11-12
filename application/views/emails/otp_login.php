<?php
$name    = html_escape($name ?? 'Pengguna');
$brand   = html_escape($brand ?? 'SISTA');
$expires = html_escape($expires ?? '10 menit');
$code    = preg_replace('/\D/', '', (string)($code ?? '')); // pastikan hanya angka
?>
<p style="margin:0 0 10px;color:#475467">
  Halo <strong><?= $name; ?></strong>,
</p>

<p style="margin:0 0 18px;color:#475467">
  Masukkan kode verifikasi berikut untuk melanjutkan proses masuk ke <strong><?= $brand; ?></strong>:
</p>

<!-- OTP 1 baris: copy-friendly (tanpa spasi), visual lega pakai letter-spacing -->
<div
  style="
    display:inline-block;
    padding:12px 16px;
    border:1px solid #e5e7eb;
    border-radius:12px;
    background:#f8fafc;
    font:700 28px/1.1 'SFMono-Regular', Consolas, Menlo, Monaco, 'Fira Code', monospace;
    color:#0f172a;
    letter-spacing:.35em;      /* visual renggang, tapi tetap satu string */
    -webkit-text-size-adjust:100%;
  "
>
  <?= $code ?>
</div>

<p style="margin:16px 0 10px;color:#475467">
  Kode berlaku selama <strong><?= $expires; ?></strong>.
</p>

<ul style="margin:8px 0 16px;padding-left:18px;color:#64748b">
  <li>Jangan bagikan kode ini kepada siapa pun.</li>
  <li><?= $brand; ?> tidak akan pernah meminta kode melalui telepon atau chat.</li>
</ul>

<p style="margin:0;color:#94a3b8;font-size:13px">
  Jika kamu tidak meminta kode ini, abaikan email ini.
</p>
