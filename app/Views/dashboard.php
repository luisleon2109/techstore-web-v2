<?php
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
include ROOT . '/app/Views/partials/header.php';
?>
<div class="dashboard-wrap">

  <!-- Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card accent">
      <div class="sc-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></div>
      <div class="sc-info">
        <div class="sc-val">Bs <?= number_format($stats['ventas_hoy'],0,'.', ',') ?></div>
        <div class="sc-lbl">Ventas Hoy</div>
        <div class="sc-sub"><?= $stats['num_ventas_hoy'] ?> transacciones</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="sc-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
      <div class="sc-info">
        <div class="sc-val">Bs <?= number_format($stats['ventas_mes'],0,'.', ',') ?></div>
        <div class="sc-lbl">Ventas del Mes</div>
        <div class="sc-sub"><?= date('F Y') ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="sc-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
      <div class="sc-info">
        <div class="sc-val"><?= $stats['clientes'] ?></div>
        <div class="sc-lbl">Clientes Activos</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="sc-icon <?= $stats['agotados']>0?'red':'green' ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg></div>
      <div class="sc-info">
        <div class="sc-val"><?= $stats['agotados'] ?></div>
        <div class="sc-lbl">Productos Agotados</div>
        <div class="sc-sub"><?= $stats['stock_bajo'] ?> con stock bajo</div>
      </div>
    </div>
  </div>

  <!-- Tables Row -->
  <div class="dash-tables">
    <!-- Últimas ventas -->
    <div class="dash-table-card">
      <div class="dtc-header">
        <h3>Últimas Ventas</h3>
        <a href="<?= APP_URL ?>/public/reportes" class="link-sm">Ver reporte →</a>
      </div>
      <table class="data-table">
        <thead><tr><th>N° Venta</th><th>Cliente</th><th>Método</th><th>Total</th><th>Estado</th></tr></thead>
        <tbody>
        <?php foreach($ultimas_ventas as $v): ?>
        <tr>
          <td><span class="code-pill"><?= htmlspecialchars($v['Numero']) ?></span></td>
          <td><?= htmlspecialchars($v['cliente'] ?? 'Consumidor Final') ?></td>
          <td><?= htmlspecialchars($v['metodo']) ?></td>
          <td><strong>Bs <?= number_format($v['Total'],2) ?></strong></td>
          <td><span class="badge green"><span class="bd"></span><?= ucfirst($v['Estado']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($ultimas_ventas)): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Sin ventas aún</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Alertas stock -->
    <div class="dash-table-card">
      <div class="dtc-header">
        <h3>⚠️ Alertas de Stock</h3>
        <a href="<?= APP_URL ?>/public/inventario" class="link-sm">Ver inventario →</a>
      </div>
      <?php if(empty($alertas)): ?>
        <div class="empty-dash"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="32" height="32"><path d="M20 6 9 17l-5-5"/></svg><p>Todo el inventario está bien</p></div>
      <?php else: ?>
      <div class="alert-list">
        <?php foreach($alertas as $a): ?>
        <div class="alert-item <?= $a['Stock_Actual']==0?'critical':'warning' ?>">
          <div class="ai-info">
            <strong><?= htmlspecialchars($a['Nombre']) ?></strong>
            <span><?= $a['SKU'] ?></span>
          </div>
          <div class="ai-stock">
            <span class="badge <?= $a['Stock_Actual']==0?'red':'orange' ?>">
              <span class="bd"></span>
              <?= $a['Stock_Actual']==0?'Agotado':$a['Stock_Actual'].' uds.' ?>
            </span>
            <small>Mín: <?= $a['Stock_Minimo'] ?></small>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>
<?php include ROOT . '/app/Views/partials/footer.php'; ?>
