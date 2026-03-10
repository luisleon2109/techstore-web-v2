<?php
$pageTitle = 'Punto de Venta';
$activeNav = 'pos';
$extraJs   = ['pos.js'];
include ROOT . '/app/Views/partials/header.php';
?>
<div class="pos-layout">

  <div class="pos-left">
    <div class="pos-toolbar">
      <div class="search-bar flex1">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="text" id="pos-search" placeholder="Buscar producto por nombre o SKU..." autocomplete="off">
      </div>
    </div>

    <div class="cat-pills" id="pos-cats">
      <div class="pill active" data-cat="all">Todos</div>
      <?php
      $tipos = array_unique(array_column($productos, 'tipo'));
      foreach($tipos as $tipo): ?>
      <div class="pill" data-cat="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></div>
      <?php endforeach; ?>
    </div>

    <div class="pos-grid-header">
      <span class="sec-title">Productos Disponibles</span>
      <span class="sec-count" id="pos-count"><?= count($productos) ?> artículos</span>
    </div>

    <div class="prod-grid" id="pos-grid"></div>
  </div>

  <div class="pos-right">
    <div class="rp-header">
      <div class="rp-title">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
        Ticket <span class="rp-count" id="cart-count">0</span>
      </div>
      <button class="rp-clear" id="clear-cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
        Limpiar
      </button>
    </div>

    <div class="sel-row" id="client-row">
      <div class="sel-btn" id="open-client-modal">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
        <span>Seleccionar Cliente / NIT</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13" style="color:var(--text-light);flex-shrink:0;"><path d="m9 18 6-6-6-6"/></svg>
      </div>
    </div>

    <div class="cart-list" id="cart-items">
      <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
        <p>El carrito está vacío.<br>Haz clic en un producto para añadir.</p>
      </div>
    </div>

    <div class="pay-section">
      <div class="pay-label">Método de Pago</div>
      <div class="pay-methods">
        <?php foreach($metodos as $m): ?>
        <div class="pay-m <?= $m['ID_Metodo']==1?'active':'' ?>" data-id="<?= $m['ID_Metodo'] ?>">
          <?php
          $icons = [
            1 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>',
            2 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3M21 21v.01M12 7v3a2 2 0 0 1-2 2H7"/></svg>',
            3 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>',
            4 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>',
          ];
          echo $icons[$m['ID_Metodo']] ?? $icons[3];
          ?>
          <span><?= htmlspecialchars($m['Nombre']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="totals">
        <div class="t-row"><span class="t-lbl">Subtotal</span><span class="t-val" id="pos-sub">Bs 0.00</span></div>
        <div class="t-row"><span class="t-lbl">Descuento</span><span class="t-val" style="color:var(--green)">- Bs 0.00</span></div>
        <div class="t-row"><span class="t-lbl">IVA (13%)</span><span class="t-val" id="pos-iva">Bs 0.00</span></div>
        <div class="t-div"></div>
        <div class="t-row"><span class="t-grand-lbl">Total</span><span class="t-grand-val" id="pos-total">Bs 0.00</span></div>
      </div>
    </div>

    <button class="confirm-btn" id="confirm-sale">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
      Confirmar Venta y Facturar
    </button>
  </div>

</div>

<!-- ── Modal: Seleccionar Cliente ── -->
<div class="modal-overlay" id="client-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Seleccionar Cliente</div>
      <button class="modal-close" data-close="client-modal">×</button>
    </div>
    <div class="modal-body">
      <div class="modal-search-bar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="text" id="modal-cli-q" placeholder="Buscar por nombre o CI...">
      </div>
      <div id="modal-cli-list" class="modal-list"></div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="client-modal">Cancelar</button>
      <button class="btn-yellow" id="confirm-client">Confirmar</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     Modal: Ticket Térmico  ← NUEVO
     ══════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="ticket-modal" style="z-index:1200;">
  <div class="modal" style="width:380px;max-height:90vh;display:flex;flex-direction:column;padding:0;overflow:hidden;">
    <div class="modal-header">
      <div class="modal-title" style="display:flex;align-items:center;gap:8px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Ticket de Venta
      </div>
      <button class="modal-close" id="ticket-close">×</button>
    </div>
    <div class="modal-body" style="flex:1;overflow-y:auto;padding:0;background:#ebebeb;">
      <div id="ticket-content"></div>
    </div>
    <div class="modal-footer" style="gap:8px;">
      <button class="btn-outline" id="ticket-nueva-venta" style="flex:1;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        Nueva Venta
      </button>
      <button class="btn-yellow" id="ticket-print" style="flex:1;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Imprimir
      </button>
    </div>
  </div>
</div>

<!-- Estilos del ticket térmico -->
<style>
.thermal-ticket {
  width: 100%;
  background: #fff;
  padding: 18px 16px 14px;
  font-family: 'DM Sans', 'Courier New', monospace;
  font-size: 12px;
  color: #1a1a1a;
  line-height: 1.55;
}
.tk-header        { text-align: center; padding-bottom: 12px; }
.tk-logo-icon     { margin: 0 auto 6px; color: #333; display: flex; justify-content: center; }
.tk-biz-name      { font-size: 18px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; font-family: 'Syne', sans-serif; }
.tk-biz-slogan    { font-size: 10px; color: #666; margin-top: 3px; }
.tk-biz-info      { font-size: 11px; color: #555; margin-top: 2px; }
.tk-line          { border-top: 1.5px solid #222; margin: 8px 0; }
.tk-line.dashed   { border-top: 1px dashed #bbb; margin: 6px 0; }
.tk-section       { padding: 4px 0; }
.tk-section-title { font-size: 9px; font-weight: 700; letter-spacing: 2px; color: #888; text-transform: uppercase; margin-bottom: 5px; }
.tk-row           { display: flex; justify-content: space-between; padding: 1.5px 0; font-size: 11.5px; gap: 8px; }
.tk-row span:first-child { color: #666; }
.tk-row span:last-child  { text-align: right; font-weight: 500; color: #1a1a1a; }
.tk-bold          { font-weight: 700 !important; }
.tk-discount      { color: #cc0000 !important; }
.tk-item          { padding: 5px 0; border-bottom: 1px dashed #e5e5e5; }
.tk-item:last-child { border-bottom: none; }
.tk-item-name     { font-weight: 600; font-size: 12px; margin-bottom: 2px; }
.tk-item-row      { display: flex; justify-content: space-between; color: #666; font-size: 11px; }
.tk-total-row     {
  display: flex; justify-content: space-between;
  font-size: 19px; font-weight: 800;
  padding: 8px 2px;
  border-top: 2.5px solid #1a1a1a;
  border-bottom: 2.5px solid #1a1a1a;
  margin: 6px 0;
  font-family: 'Syne', sans-serif;
}
.tk-footer        { text-align: center; padding-top: 12px; padding-bottom: 8px; }
.tk-thank-you     { font-size: 14px; font-weight: 700; line-height: 1.6; color: #1a1a1a; }
.tk-garantia      { font-size: 10px; color: #888; margin-top: 8px; line-height: 1.5; }
</style>

<script>
const POS_PRODUCTOS = <?= json_encode($productos, JSON_UNESCAPED_UNICODE) ?>;
const POS_CLIENTES  = <?= json_encode($clientes,  JSON_UNESCAPED_UNICODE) ?>;
const APP_URL       = '<?= APP_URL ?>';
const IVA_RATE      = <?= IVA_RATE ?>;
const NEGOCIO_CFG   = <?= json_encode($cfg ?? [], JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php include ROOT . '/app/Views/partials/footer.php'; ?>
