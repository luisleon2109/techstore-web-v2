<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/components.css">
<?php if (isset($extraCss)): foreach($extraCss as $css): ?>
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/<?= $css ?>">
<?php endforeach; endif; ?>
</head>
<body>
<?php $user = Auth::user(); ?>
<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sb-logo">
    <div class="logo-mark">
      <svg viewBox="0 0 24 24" fill="none" stroke="#1A2B5F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
    </div>
  </div>
  <nav class="sb-nav">

    <?php if(Auth::can('pos')): ?>
    <a href="<?= APP_URL ?>/public/pos" class="nav-i <?= ($activeNav??'')==='pos'?'active':'' ?>" title="Punto de Venta">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg>
      <span>POS</span>
    </a>
    <?php endif; ?>

    <?php if(Auth::can('ventas')): ?>
    <a href="<?= APP_URL ?>/public/ventas" class="nav-i <?= ($activeNav??'')==='ventas'?'active':'' ?>" title="Historial de Ventas">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
      <span>Ventas</span>
    </a>
    <?php endif; ?>

    <?php if(Auth::can('clientes')): ?>
    <a href="<?= APP_URL ?>/public/clientes" class="nav-i <?= ($activeNav??'')==='clientes'?'active':'' ?>" title="Clientes">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <span>Clientes</span>
    </a>
    <?php endif; ?>

    <?php if(Auth::can('compras')): ?>
    <a href="<?= APP_URL ?>/public/compras" class="nav-i <?= ($activeNav??'')==='compras'?'active':'' ?>" title="Compras">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      <span>Compras</span>
    </a>
    <?php endif; ?>

    <?php if(Auth::can('proveedores')): ?>
    <a href="<?= APP_URL ?>/public/proveedores" class="nav-i <?= ($activeNav??'')==='proveedores'?'active':'' ?>" title="Proveedores">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/><path d="M12 3v6"/></svg>
      <span>Proveedores</span>
    </a>
    <?php endif; ?>

    <?php if(Auth::can('inventario')): ?>
    <a href="<?= APP_URL ?>/public/inventario" class="nav-i <?= ($activeNav??'')==='inventario'?'active':'' ?>" title="Inventario">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
      <span>Inventario</span>
    </a>
    <?php endif; ?>

    <?php if(Auth::can('productos')): ?>
    <a href="<?= APP_URL ?>/public/productos" class="nav-i <?= ($activeNav??'')==='productos'?'active':'' ?>" title="Productos">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg>
      <span>Productos</span>
    </a>
    <?php endif; ?>

    <?php if(Auth::can('catalogo')): ?>
    <a href="<?= APP_URL ?>/public/catalogo" class="nav-i <?= ($activeNav??'')==='catalogo'?'active':'' ?>" title="Catálogo">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
      <span>Catálogo</span>
    </a>
    <?php endif; ?>

    <?php if(Auth::can('reportes')): ?>
    <a href="<?= APP_URL ?>/public/reportes" class="nav-i <?= ($activeNav??'')==='reportes'?'active':'' ?>" title="Reportes">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      <span>Reportes</span>
    </a>
    <?php endif; ?>

    <?php if(Auth::can('usuarios')): ?>
    <a href="<?= APP_URL ?>/public/usuarios" class="nav-i <?= ($activeNav??'')==='usuarios'?'active':'' ?>" title="Usuarios">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
      <span>Usuarios</span>
    </a>
    <?php endif; ?>

  </nav>
  <div class="sb-bottom">
    <a href="<?= APP_URL ?>/public/logout" class="nav-i" title="Cerrar Sesión" style="color:rgba(255,255,255,.35);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
      <span>Salir</span>
    </a>
  </div>
</aside>

<!-- MAIN -->
<div class="main">
  <!-- TOPBAR -->
  <header class="topbar">
    <div class="tb-brand">Tech<span>Store</span> POS</div>
    <div class="tb-center"><?= $pageTitle ?? '' ?></div>
    <div class="tb-right">
      <div class="notif-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        <span class="nd"></span>
      </div>
      <div class="user-chip">
        <div class="u-av">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
        </div>
        <div class="u-info">
          <span class="u-name"><?= htmlspecialchars($user['name']) ?></span>
          <span class="u-role"><?= htmlspecialchars($user['role']) ?></span>
        </div>
      </div>
    </div>
  </header>
  <!-- PAGE CONTENT -->
  <main class="page-content">
