<div class="d-flex align-items-center justify-content-between mb-3">
  <h4 class="mb-0">Pengguna</h4>

  <form method="get" class="d-flex gap-2">
    <select name="status" class="form-select form-select-sm" id="filterStatus" style="min-width:200px">
      <option value="" <?= ($status===''?'selected':''); ?>>Semua status</option>
      <option value="pending_email"  <?= ($status==='pending_email'?'selected':''); ?>>Pending Email</option>
      <option value="pending_admin"  <?= ($status==='pending_admin'?'selected':''); ?>>Pending Admin</option>
      <option value="active"         <?= ($status==='active'?'selected':''); ?>>Active</option>
    </select>
    <button class="btn btn-sm btn-outline-primary">Terapkan</button>
  </form>
</div>

<?php if ($this->session->flashdata('success')): ?>
  <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
  <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
<?php endif; ?>

<div class="table-responsive">
  <table id="usersTable" class="table table-striped table-hover align-middle">
    <thead>
      <tr>
        <th style="width:56px">#</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Verified By</th>
        <th style="width:180px">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $i=1; foreach ($users as $u): ?>
        <tr>
          <td class="text-muted"><?= $i++; ?></td>
          <td><?= html_escape($u['name']); ?></td>
          <td><?= html_escape($u['email']); ?></td>
          <td>
            <?php if ((int)$u['role_id']===1): ?>
              <span class="badge bg-secondary">Admin</span>
            <?php else: ?>
              <span class="badge bg-light text-dark">User</span>
            <?php endif; ?>
          </td>
          <td>
            <?php
              $badge = 'bg-secondary'; $label = ucfirst($u['status'] ?: 'unknown');
              if ($u['status']==='pending_email')  { $badge='bg-warning text-dark'; $label='Menunggu verifikasi email'; }
              if ($u['status']==='pending_admin')  { $badge='bg-info text-dark';    $label='Menunggu verifikasi admin'; }
              if ($u['status']==='active')         { $badge='bg-success';           $label='Aktif'; }
              if ((int)$u['is_active']===0 && $u['status']==='active') { $badge='bg-secondary'; $label='Nonaktif'; }
            ?>
            <span class="badge <?= $badge ?>"><?= $label; ?></span>
          </td>
          <td><?= !empty($u['verified_by_name']) ? html_escape($u['verified_by_name']) : '—'; ?></td>
          <td class="text-nowrap">
            <?php if ((int)$u['role_id'] !== 1): ?>
              <?php if ($u['status']!=='active'): ?>
                <button type="button" class="btn btn-sm btn-primary js-verify" data-id="<?= (int)$u['id']; ?>">
                  <i class="bi bi-check2-circle me-1"></i> Konfirmasi
                </button>
              <?php endif; ?>
              <button type="button" class="btn btn-sm btn-outline-danger js-disable" data-id="<?= (int)$u['id']; ?>">
                <i class="bi bi-x-circle me-1"></i> Nonaktifkan
              </button>
            <?php else: ?>
              <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Admin</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Inline JS: DataTables + aksi tombol -->
<script>
(function(){
  // Pastikan jQuery + DataTables sudah diload di footer.
  document.addEventListener('DOMContentLoaded', function(){
    if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) return;
    var $ = jQuery;

    var dt = $('#usersTable').DataTable({
      responsive: true,
      autoWidth: false,
      pageLength: 25,
      language: {
        search: 'Cari:',
        lengthMenu: 'Tampil _MENU_',
        info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
        infoEmpty: 'Tidak ada data',
        zeroRecords: 'Data tidak ditemukan',
        paginate: { previous: '‹', next: '›' }
      },
      columnDefs: [
        { orderable:false, targets: [-1] } // kolom Aksi
      ]
    });

    // Nomor urut kolom # mengikuti urutan/search/paging
    function renumber(){
      var i = 1;
      dt.column(0, { search:'applied', order:'applied' })
        .nodes().each(function(cell){ cell.innerHTML = i++; });
    }
    dt.on('order.dt search.dt draw.dt', renumber);
    renumber();

    // ===== Aksi tombol (AJAX) =====
    var CSRF_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
    var CSRF_HASH = '<?= $this->security->get_csrf_hash(); ?>';

    function postJson(url, data, done){
      data[CSRF_NAME] = CSRF_HASH;
      fetch(url, {
        method: 'POST',
        headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
        body: new URLSearchParams(data)
      })
      .then(r => r.json())
      .then(res => {
        if (res && res.csrf_hash) CSRF_HASH = res.csrf_hash; // refresh csrf
        done(res);
      })
      .catch(() => alert('Terjadi kesalahan jaringan'));
    }

    // Konfirmasi
    $(document).on('click', '.js-verify', function(){
      var id = this.getAttribute('data-id');
      if (!confirm('Konfirmasi akun ini?')) return;
      postJson('<?= base_url('admin/verify'); ?>', { id: id }, function(res){
        if (res && res.ok) { location.reload(); }
        else { alert(res && res.error ? res.error : 'Gagal verifikasi'); }
      });
    });

    // Nonaktifkan
    $(document).on('click', '.js-disable', function(){
      var id = this.getAttribute('data-id');
      if (!confirm('Nonaktifkan akun ini?')) return;
      postJson('<?= base_url('admin/disable'); ?>', { id: id }, function(res){
        if (res && res.ok) { location.reload(); }
        else { alert(res && res.error ? res.error : 'Gagal menonaktifkan'); }
      });
    });
  });
})();
</script>
