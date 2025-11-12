

<!-- Topbar ala LinkedIn (Responsive) -->
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm fixed-top">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="<?= base_url('user') ?>">
  <img src="<?= base_url('assets/img/main.png') ?>" alt="SISTA" class="brand-logo d-inline-block align-text-top">
  <!-- <span class="fw-bold text-primary ms-2">SISTA</span> -->
</a>


    <!-- Toggler -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topbarNav" aria-controls="topbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Desktop Search -->
    <form class="d-none d-md-flex ms-3 flex-grow-1" style="max-width: 320px;">
      <input class="form-control form-control-sm" type="search" placeholder="Cari..." aria-label="Search">
    </form>

    <!-- Right area / Collapsible -->
    <div class="collapse navbar-collapse" id="topbarNav">
      <!-- Mobile Search (full width) -->
      <div class="d-lg-none w-100 mt-3">
        <form class="d-flex">
          <input class="form-control" type="search" placeholder="Cari..." aria-label="Search">
        </form>
        <hr class="my-2">
      </div>

      <!-- Nav links -->
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item me-lg-3">
          <a class="nav-link d-flex align-items-center" href="<?= base_url('user') ?>">
            <i class="bi bi-house-door-fill me-1"></i> <small class="d-lg-inline">Beranda</small>
          </a>
        </li>

        <!-- Profile dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-1"></i>
            <small><?= $user['nama_lengkap'] ?? 'User'; ?></small>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= base_url('user/profile') ?>">Profil Saya</a></li>
            <li><a class="dropdown-item" href="<?= base_url('settings') ?>">Pengaturan</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">Keluar</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>


