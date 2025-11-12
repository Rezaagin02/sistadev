<div class="container py-4">
  <div class="row g-3">
    <div class="col-md-3 d-none d-md-block">
      <?php $this->load->view('templates/sidebar'); ?>
    </div>
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <h5 class="mb-2">Pengaturan</h5>
          <p class="text-muted small mb-3">Kelola akun & preferensi.</p>
          <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action d-flex align-items-center" href="<?= base_url('settings/email') ?>">
              <i class="bi bi-envelope me-2"></i> Ganti Email
            </a>
            <a class="list-group-item list-group-item-action d-flex align-items-center" href="<?= base_url('settings/password') ?>">
              <i class="bi bi-shield-lock me-2"></i> Ganti Password
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <h5 class="mb-3">Ringkasan Akun</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="small text-muted">Email saat ini</div>
              <div class="fw-semibold"><?= html_escape($user['email']) ?></div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Terakhir diperbarui</div>
              <div class="fw-semibold"><?= html_escape($user['updated_at'] ?? '-') ?></div>
            </div>
          </div>
          <hr>
          <div class="small text-muted">
            Gunakan menu di sebelah kiri untuk memperbarui email atau password.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
