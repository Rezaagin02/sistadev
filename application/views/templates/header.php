<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title . ' ·  SISTA' ?? 'SISTA'; ?></title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon.png') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  
  <style>
    body {
      background-color: #f3f2ef;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }
    /* logo di topbar */
.navbar .brand-logo{
  height: 28px;        /* pas buat mobile */
  width: auto;         /* biar proporsional */
  object-fit: contain; /* jaga proporsi kalau png punya padding */
  flex-shrink: 0;      /* jangan ikut mengecil kalau space sempit */
  display: block;
}

/* scale dikit di layar lebih lebar */
@media (min-width: 768px){
  .navbar .brand-logo{ height: 32px; }
}
@media (min-width: 1200px){
  .navbar .brand-logo{ height: 36px; }
}

  </style>
</head>
<body>
<div class="container" style="margin-top: 80px;">
  <div class="row">
