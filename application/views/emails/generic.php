<h1 class="h1">Pemberitahuan</h1>
<div class="small" style="margin:6px 0 2px">
  <?= isset($content) ? $content : 'Halo dari SISTA 👋'; ?>
</div>
<table role="presentation" cellspacing="0" cellpadding="0" style="margin:16px 0">
  <tr>
    <td class="btn-outline">
      <a href="<?= html_escape($cta_url ?? base_url()) ?>"><?= html_escape($cta_label ?? 'Buka SISTA') ?></a>
    </td>
  </tr>
</table>
<p class="small muted">Jika tombol tidak bisa diklik, buka: <br>
  <a href="<?= html_escape($cta_url ?? base_url()) ?>"><?= html_escape($cta_url ?? base_url()) ?></a>
</p>
