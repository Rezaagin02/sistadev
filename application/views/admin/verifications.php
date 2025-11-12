<!-- application/views/admin/verifications.php -->
<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="m-0">Verifikasi Akun</h6>
    <small class="text-muted">Email verif & persetujuan admin</small>
  </div>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>#</th><th>Nama</th><th>Email</th><th>Email Verified</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>—</td>
          <td>—</td>
          <td>—</td>
          <td><span class="badge text-bg-secondary">belum</span></td>
          <td><span class="badge text-bg-warning">pending_admin</span></td>
          <td>
            <a href="#" class="btn btn-sm btn-success"><i class="bi bi-check2"></i> Setujui</a>
            <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i> Tolak</a>
          </td>
        </tr>
        <tr><td colspan="6" class="text-muted">Tarik data user: status = pending_email / pending_admin.</td></tr>
      </tbody>
    </table>
  </div>
</div>
