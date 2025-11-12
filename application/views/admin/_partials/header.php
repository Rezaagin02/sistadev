<!-- application/views/admin/_partials/header.php -->
<?php $appName = 'SISTA Admin'; ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? $title.' · '.$appName : $appName ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{
      --primary:#0b5ed7;
      --sidebar-w: 250px;
    }
    body{ background:#f4f6fb; }
    .layout{
      display:grid; grid-template-columns: var(--sidebar-w) 1fr; min-height:100vh;
    }
    .sidebar{
      background:#0c1b3a; color:#dbe1f0; position:sticky; top:0; height:100vh;
    }
    .sidebar .brand{
      padding:16px 18px; font-weight:800; letter-spacing:.2px;
      display:flex; align-items:center; gap:10px; border-bottom:1px solid rgba(255,255,255,.06);
    }
    .sidebar .brand img{ height:28px; width:auto; }
    .sidebar .nav-link{ color:#c8d2ea; border-radius:10px; margin:.15rem .5rem; }
    .sidebar .nav-link:hover{ background:rgba(255,255,255,.08); }
    .sidebar .nav-link.active{ background:#1d2b55; color:#fff; }
    .content-wrap{ display:flex; flex-direction:column; min-width:0; }
    .topbar{
      background:#fff; border-bottom:1px solid #eef0f4;
      display:flex; align-items:center; justify-content:space-between; padding:10px 16px;
      position:sticky; top:0; z-index:5;
    }
    .main{ padding:18px; }
    .card{ border:1px solid #eef1f5; border-radius:14px; }
    @media (max-width: 991.98px){
      .layout{ grid-template-columns: 1fr; }
      .sidebar{ position:static; height:auto; }
    }
  </style>
</head>
<body>
<div class="layout">
