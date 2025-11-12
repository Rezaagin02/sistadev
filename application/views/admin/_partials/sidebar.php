<!-- application/views/admin/_partials/sidebar.php -->
<aside class="sidebar">
  <div class="brand">
    <img src="<?= base_url('assets/img/main.png') ?>" alt="logo">
    <span>SISTA Admin</span>
  </div>

  <nav class="p-2">
    <a class="nav-link d-flex align-items-center gap-2 <?= ($menu??'')==='dashboard'?'active':'' ?>" href="<?= base_url('admin') ?>">
      <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
    </a>
    <a class="nav-link d-flex align-items-center gap-2 <?= ($menu??'')==='users'?'active':'' ?>" href="<?= base_url('admin/users') ?>">
      <i class="bi bi-people"></i> <span>Pengguna</span>
    </a>
    <a class="nav-link d-flex align-items-center gap-2 <?= ($menu??'')==='verifications'?'active':'' ?>" href="<?= base_url('admin/verifications') ?>">
      <i class="bi bi-patch-check"></i> <span>Verifikasi Akun</span>
    </a>
    <a class="nav-link d-flex align-items-center gap-2 <?= ($menu??'')==='settings'?'active':'' ?>" href="<?= base_url('admin/settings') ?>">
      <i class="bi bi-gear"></i> <span>Pengaturan</span>
    </a>
  </nav>
</aside>
