<?php

$pageTitle = 'Órdenes de Compra';

$activeNav = 'compras';

$extraJs   = ['compras.js'];

include ROOT . '/app/Views/partials/header.php';

?>

<div class="pos-layout">



  <div class="pos-left">

    <div style="display:flex;align-items:center;gap:10px;">

      <h2 style="font-family:'DM Sans',sans-serif;font-weight:700;font-size:18px;color:var(--text);flex:1;">Nueva Orden de Compra</h2>

      <button class="btn-outline" id="open-sup-modal">

        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/></svg>

        Cambiar Proveedor

      </button>

    </div>



    <div style="background:var(--white);border-radius:var(--r);border:1px solid var(--border);box-shadow:var(--sh-sm);overflow:hidden;">

      <div style="padding:10px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;background:var(--bg);">

        <span style="font-family:'DM Sans',sans-serif;font-weight:700;font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;flex:1;">Seleccionar Proveedor</span>

        <div style="display:flex;align-items:center;gap:8px;background:var(--white);border-radius:7px;padding:0 11px;height:32px;border:1px solid var(--border);flex:2;">

          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13" style="color:var(--text-light);flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>

          <input type="text" placeholder="Buscar proveedor..." style="border:none;outline:none;background:transparent;font-size:12px;width:100%;font-family:'DM Sans',sans-serif;" oninput="filterSupInline(this.value)">

        </div>

      </div>

     

      <div id="sup-inline-grid" style="display:flex; gap:10px; padding:12px 16px; overflow-x:auto; min-height:80px; align-items:center;">

          </div>



      <div style="display:none;background:var(--blue-soft);border-top:1px solid var(--border);padding:8px 16px;align-items:center;gap:10px;" id="sup-selected-bar"></div>

    </div>



    <div class="search-bar flex1" style="margin-top:10px;">

      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>

      <input type="text" id="pur-search" placeholder="Buscar producto para agregar a la orden...">

    </div>



    <div style="display:flex;align-items:center;justify-content:space-between; margin-top:5px;">

      <span class="sec-title">Seleccionar Productos</span>

      <span class="sec-count" id="pur-count"><?= count($productos) ?> artículos</span>

    </div>



    <div class="prod-grid" id="pur-grid"></div>



    <?php if (!empty($historial)): ?>

    <div style="flex-shrink:0; margin-top:15px;">

      <div class="sec-title" style="margin-bottom:8px;">Últimas Órdenes de Compra</div>

      <div style="background:var(--white);border-radius:var(--r);border:1px solid var(--border);overflow:hidden;">

        <table class="data-table">

          <thead><tr><th>N° Orden</th><th>Fecha</th><th>Proveedor</th><th>Total</th><th>Estado</th></tr></thead>

          <tbody>

          <?php foreach(array_slice($historial,0,5) as $h): ?>

          <tr>

            <td><span class="code-pill"><?= htmlspecialchars($h['Numero_Orden']) ?></span></td>

            <td style="color:var(--text-muted);"><?= date('d/m/Y', strtotime($h['Fecha'])) ?></td>

            <td><?= htmlspecialchars($h['proveedor']) ?></td>

            <td><strong style="font-variant-numeric: tabular-nums;">Bs <?= number_format($h['Total'],2) ?></strong></td>

            <td><span class="badge <?= $h['Estado']==='recibida'?'green':($h['Estado']==='anulada'?'red':'orange') ?>"><span class="bd"></span><?= ucfirst($h['Estado']) ?></span></td>

          </tr>

          <?php endforeach; ?>

          </tbody>

        </table>

      </div>

    </div>

    <?php endif; ?>

  </div>



  <div class="pos-right">

    <div class="rp-header">

      <div class="rp-title">

        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>

        Orden de Compra <span class="rp-count" id="pur-cart-cnt">0</span>

      </div>

      <button class="rp-clear" id="pur-clear-btn">

        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>

        Limpiar

      </button>

    </div>

    <div style="padding:9px 16px;border-bottom:1px solid var(--border);" id="pur-sup-display"></div>

    <div class="cart-list" id="pur-items">

      <div class="empty-state">

        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>

        <p>Sin productos en la orden.<br>Haz clic en un producto.</p>

      </div>

    </div>

    <div class="pay-section">

      <div class="totals">

        <div class="t-row"><span class="t-lbl">Subtotal</span><span class="t-val" id="pur-sub">Bs 0.00</span></div>

        <div class="t-row"><span class="t-lbl">Descuento proveedor</span><span class="t-val" style="color:var(--green)">— Bs 0.00</span></div>

        <div class="t-div"></div>

        <div class="t-row"><span class="t-grand-lbl">Total Orden</span><span class="t-grand-val" id="pur-total">Bs 0.00</span></div>

      </div>

    </div>

    <button class="confirm-btn blue-confirm" id="confirm-pur-btn">

      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>

      Registrar Orden de Compra

    </button>

  </div>



</div>



<div class="modal-overlay" id="sup-modal">

  <div class="modal">

    <div class="modal-header">

      <div class="modal-title">Seleccionar Proveedor</div>

      <button class="modal-close" data-close="sup-modal">×</button>

    </div>

    <div class="modal-body">

      <div class="modal-search-bar">

        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>

        <input type="text" id="modal-sup-q" placeholder="Buscar proveedor...">

      </div>

      <div id="modal-sup-list" class="modal-list"></div>

    </div>

    <div class="modal-footer">

      <button class="btn-outline" data-close="sup-modal">Cancelar</button>

      <button class="btn-yellow" id="confirm-sup-btn">Confirmar</button>

    </div>

  </div>

</div>



<script>

const PUR_PRODUCTOS   = <?= json_encode($productos,   JSON_UNESCAPED_UNICODE) ?>;

const PUR_PROVEEDORES = <?= json_encode($proveedores, JSON_UNESCAPED_UNICODE) ?>;

const APP_URL = '<?= APP_URL ?>';



// Ajustamos la función de renderizado de JS para que cree tarjetas pequeñas

function renderSupCards(sups) {

    const grid = document.getElementById('sup-inline-grid');

    grid.innerHTML = sups.map(s => `

        <div class="modal-item" onclick="selectSupplier(${s.ID_Proveedor})" style="min-width: 200px; padding: 8px 12px; border-radius: 8px; flex-shrink: 0;">

            <div class="mi-ico blue" style="width: 30px; height: 30px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px; height:14px;"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/></svg></div>

            <div class="mi-info">

                <div class="mi-name" style="font-size: 12px;">${s.Nombre}</div>

                <div class="mi-sub" style="font-size: 10px;">NIT: ${s.NIT || '—'}</div>

            </div>

        </div>

    `).join('');

}



// Inicializamos la lista al cargar

window.onload = () => { renderSupCards(PUR_PROVEEDORES); };



function filterSupInline(q){ renderSupCards(PUR_PROVEEDORES.filter(s=>s.Nombre.toLowerCase().includes(q.toLowerCase())||(s.NIT||'').includes(q))); }

</script>

<?php include ROOT . '/app/Views/partials/footer.php'; ?>