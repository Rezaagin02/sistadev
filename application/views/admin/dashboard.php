<div class="row g-3">
  <div class="col-sm-6 col-lg-3">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <div class="text-muted small mb-1">Total Pengguna</div>
        <div class="h3 m-0"><?= (int)$total_users ?></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <div class="text-muted small mb-1">Email Terverifikasi</div>
        <div class="h3 m-0"><?= (int)$verified_email ?></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <div class="text-muted small mb-1">Menunggu Admin</div>
        <div class="h3 m-0"><?= (int)$pending_admin ?></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <div class="text-muted small mb-1">Login Hari Ini</div>
        <div class="h3 m-0"><?= (int)$active_today ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card mt-3 border-0 shadow-sm rounded-4">
  <div class="card-header bg-white border-0 fw-semibold">Pengguna Terbaru</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr class="text-muted small">
            <th>Nama</th><th>Email</th><th>Status</th><th>Terverifikasi</th><th>Daftar</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($latest_users as $u): ?>
          <tr>
            <td><?= html_escape($u['name']) ?> <div class="text-muted small">@<?= html_escape($u['username']) ?></div></td>
            <td><?= html_escape($u['email']) ?></td>
            <td><span class="badge text-bg-<?= $u['status']==='active'?'success':($u['status']==='pending_admin'?'warning':'secondary') ?>"><?= $u['status'] ?></span></td>
            <td><?= $u['email_verified_at'] ? date('d M Y H:i', strtotime($u['email_verified_at'])) : '<span class="text-muted">—</span>' ?></td>
            <td><?= $u['date_created'] ? date('d M Y', (int)$u['date_created']) : '<span class="text-muted">—</span>' ?></td>
          </tr>
          <?php endforeach ?>
          <?php if (empty($latest_users)): ?>
          <tr><td colspan="5" class="text-center text-muted small py-4">Belum ada data.</td></tr>
          <?php endif ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
