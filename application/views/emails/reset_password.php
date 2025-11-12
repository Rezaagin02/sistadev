<h1 class="h1">Verifikasi Akun Kamu</h1>
<p class="muted small" style="margin:6px 0 18px">
  Hai <?= html_escape($name ?? 'Pengguna'); ?>, klik tombol di bawah untuk mengaktifkan akunmu.
</p>

<!-- Button -->
<table role="presentation" cellspacing="0" cellpadding="0" style="margin:12px 0 18px">
  <tr>
    <td class="btn">
      <a href="<?= html_escape($verify_url ?? '#') ?>">Verifikasi Akun</a>
    </td>
  </tr>
</table>

<p class="small muted">Link berlaku sampai <?= html_escape($expires_text ?? '24 jam'); ?>.</p>
