<!-- application/views/admin/_partials/topbar.php -->
<div class="content-wrap">
  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <h6 class="m-0 fw-bold"><?= isset($title)?$title:'—' ?></h6>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="text-muted small d-none d-md-inline">Hi, <?= html_escape($this->session->userdata('username')) ?></span>
      <a href="<?= base_url('user') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-person"></i> Profil</a>
      <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
    </div>
  </header>
  <main class="main">
