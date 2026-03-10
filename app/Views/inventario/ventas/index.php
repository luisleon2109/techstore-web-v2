<?php
// app/Views/ventas/index.php
$pageTitle = 'Historial de Ventas';
$activeNav = 'ventas';
$extraJs   = ['ventas.js'];
include ROOT . '/app/Views/partials/header.php';
?>
<div class="module-wrap">

  <div class="module-header">
    <div>
      <h1 class="module-title">Historial de Ventas</h1>
      <p class="module-sub">Consulta, detalle y anulación de ventas</p>
    </div>
    <div class="hdr-actions">
      <input type="date" class="f-input" id="v-desde" value="<?= htmlspecialchars($desde) ?>" style="height:40px;width:145px;">
      <input type="date" class="f-input" id="v-hasta" value="<?= htmlspecialchars($hasta) ?>" style="height:40px;width:145px;">
      <button class="btn-blue" id="filtrar-btn">Filtrar</button>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);">
    <div class="stat-card accent">
      <div class="sc-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg></div>
      <div class="sc-info"><div class="sc-val"><?= $totales['count'] ?></div><div class="sc-lbl">Total Ventas</div></div>
    </div>
    <div class="stat-card">
      <div class="sc-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
      <div class="sc-info"><div class="sc-val">Bs <?= number_format($totales['sum'],2) ?></div><div class="sc-lbl">Total Facturado</div></div>
    </div>
    <div class="stat-card">
      <div class="sc-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></div>
      <div class="sc-info"><div class="sc-val"><?= $totales['completadas'] ?></div><div class="sc-lbl">Completadas</div></div>
    </div>
    <div class="stat-card">
      <div class="sc-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg></div>
      <div class="sc-info"><div class="sc-val"><?= $totales['anuladas'] ?></div><div class="sc-lbl">Anuladas</div></div>
    </div>
  </div>

  <div class="module-body two-col">

    <!-- Tabla -->
    <div class="table-card">
      <div class="table-toolbar">
        <div class="tbl-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" id="vta-search" placeholder="Buscar por número, cliente, factura...">
        </div>
      </div>
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>N° Venta</th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Método</th>
              <th>Factura</th>
              <th>Total</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="vta-tbody">
          <?php foreach($ventas as $v):
            $sc = $v['Estado']==='completada' ? 'green' : ($v['Estado']==='anulada' ? 'red' : 'orange');
          ?>
          <tr>
            <td><span class="code-pill"><?= htmlspecialchars($v['Numero']) ?></span></td>
            <td style="color:var(--text-muted);font-size:12px;white-space:nowrap;">
              <?= date('d/m/Y H:i', strtotime($v['Fecha'])) ?>
            </td>
            <td><?= htmlspecialchars($v['cliente']) ?></td>
            <td style="color:var(--text-muted);"><?= htmlspecialchars($v['metodo'] ?? '—') ?></td>
            <td><span class="code-pill" style="font-size:10px;"><?= htmlspecialchars($v['Numero_Factura'] ?? '—') ?></span></td>
            <td><strong style="color:var(--blue);font-family:'Syne',sans-serif;">Bs <?= number_format($v['Total'],2) ?></strong></td>
            <td><span class="badge <?= $sc ?>"><span class="bd"></span><?= ucfirst($v['Estado']) ?></span></td>
            <td>
              <div class="act-btns">
                <button class="act-b ver-vta" data-id="<?= $v['ID_Venta'] ?>" title="Ver detalle y ticket">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
                <?php if($v['Estado']==='completada' && Auth::role()==='Administrador'): ?>
                <button class="act-b act-del anular-vta" data-id="<?= $v['ID_Venta'] ?>" data-num="<?= htmlspecialchars($v['Numero']) ?>" title="Anular venta">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($ventas)): ?>
          <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:30px;">Sin ventas en el período seleccionado</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Panel detalle -->
    <div class="form-panel" id="vta-panel">
      <div class="fp-header">
        <h3 id="vta-panel-title">Detalle</h3>
        <span class="fp-badge new" id="vta-panel-badge">—</span>
      </div>
      <div class="fp-body" id="vta-panel-body" style="justify-content:center;align-items:center;">
        <div style="text-align:center;color:var(--text-muted);">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="36" height="36"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <p style="margin-top:10px;font-size:13px;">Seleccioná una venta para ver el detalle</p>
        </div>
      </div>
      <div class="fp-footer" id="vta-panel-footer" style="display:none;">
        <button class="btn-yellow" id="reimprimir-btn" style="width:100%;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
          Reimprimir Ticket
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Modal ticket para reimprimir -->
<div class="modal-overlay" id="ticket-modal" style="z-index:1200;">
  <div class="modal" style="width:380px;max-height:90vh;display:flex;flex-direction:column;">
    <div class="modal-header">
      <div class="modal-title">Ticket de Venta</div>
      <button class="modal-close" id="ticket-close">×</button>
    </div>
    <div class="modal-body" style="flex:1;overflow-y:auto;padding:0;background:#f0f0f0;">
      <div id="ticket-content"></div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" id="ticket-close2" style="flex:1;">Cerrar</button>
      <button class="btn-yellow" id="ticket-print" style="flex:1;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Imprimir
      </button>
    </div>
  </div>
</div>

<!-- Modal anular -->
<div class="modal-overlay" id="anular-modal">
  <div class="modal" style="width:400px;">
    <div class="modal-header">
      <div class="modal-title">Anular Venta</div>
      <button class="modal-close" data-close="anular-modal">×</button>
    </div>
    <div class="modal-body" style="gap:12px;">
      <p style="font-size:13px;color:var(--text-muted);">¿Estás seguro que querés anular la venta <strong id="anular-num"></strong>? El stock será restaurado automáticamente.</p>
      <div class="f-group">
        <label class="f-label">Motivo de anulación</label>
        <textarea class="f-textarea" id="anular-motivo" placeholder="Descripción del motivo..." style="min-height:70px;"></textarea>
      </div>
      <input type="hidden" id="anular-id">
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="anular-modal">Cancelar</button>
      <button class="btn-red" id="confirmar-anular">Sí, anular</button>
    </div>
  </div>
</div>

<style>
/* Estilos ticket térmico */
.thermal-ticket { width:100%; background:#fff; padding:16px 14px; font-family:'DM Sans','Courier New',monospace; font-size:12px; color:#1a1a1a; line-height:1.5; }
.tk-header       { text-align:center; padding-bottom:10px; }
.tk-logo-icon    { margin:0 auto 6px; color:#333; }
.tk-biz-name     { font-size:17px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; font-family:'Syne',sans-serif; }
.tk-biz-slogan   { font-size:10px; color:#666; margin-top:2px; }
.tk-biz-info     { font-size:11px; color:#555; margin-top:2px; }
.tk-line         { border-top:1.5px solid #222; margin:8px 0; }
.tk-line.dashed  { border-top:1px dashed #bbb; }
.tk-section      { padding:4px 0; }
.tk-section-title{ font-size:9px; font-weight:700; letter-spacing:2px; color:#888; text-transform:uppercase; margin-bottom:5px; }
.tk-row          { display:flex; justify-content:space-between; padding:1.5px 0; font-size:11.5px; gap:8px; }
.tk-row span:first-child { color:#555; }
.tk-row span:last-child  { text-align:right; font-weight:500; }
.tk-bold         { font-weight:700 !important; color:#1a1a1a !important; }
.tk-discount     { color:#cc0000 !important; }
.tk-item         { padding:4px 0; border-bottom:1px dashed #eee; }
.tk-item:last-child { border-bottom:none; }
.tk-item-name    { font-weight:600; font-size:12px; margin-bottom:2px; }
.tk-item-row     { display:flex; justify-content:space-between; color:#555; font-size:11px; }
.tk-total-row    { display:flex; justify-content:space-between; font-size:18px; font-weight:800; padding:8px 2px; border-top:2.5px solid #1a1a1a; border-bottom:2.5px solid #1a1a1a; margin:6px 0; font-family:'Syne',sans-serif; }
.tk-footer       { text-align:center; padding-top:10px; padding-bottom:6px; }
.tk-thank-you    { font-size:14px; font-weight:700; line-height:1.6; color:#1a1a1a; }
.tk-garantia     { font-size:10px; color:#777; margin-top:8px; line-height:1.5; }
.btn-red         { background:#e5484d; color:#fff; border:none; border-radius:10px; padding:0 20px; height:40px; font-family:'Syne',sans-serif; font-weight:700; font-size:13px; cursor:pointer; }
</style>

<script>
const APP_URL    = '<?= APP_URL ?>';
const ROL_ACTUAL = '<?= Auth::role() ?>';
</script>
<?php include ROOT . '/app/Views/partials/footer.php'; ?>
