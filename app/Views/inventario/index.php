<?php
$pageTitle = 'Inventario';
$activeNav = 'inventario';
include ROOT . '/app/Views/partials/header.php';
?>
<div class="module-wrap">
  <div class="module-header">
    <div>
      <h1 class="module-title">Inventario</h1>
      <p class="module-sub">Consulta de stock y ajustes manuales</p>
    </div>
    <div class="hdr-actions">
      <button class="btn-yellow" onclick="openModal('ajuste-modal')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
        Ajuste Manual
      </button>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
        <div class="sc-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg></div>
        <div class="sc-info"><div class="sc-val"><?= $stats['total'] ?></div><div class="sc-lbl">Total Productos</div></div>
    </div>
    <div class="stat-card">
        <div class="sc-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg></div>
        <div class="sc-info"><div class="sc-val"><?= $stats['en_stock'] ?></div><div class="sc-lbl">En Stock</div></div>
    </div>
    <div class="stat-card">
        <div class="sc-icon orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg></div>
        <div class="sc-info"><div class="sc-val"><?= $stats['stock_bajo'] ?></div><div class="sc-lbl">Stock Bajo</div></div>
    </div>
    <div class="stat-card">
        <div class="sc-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg></div>
        <div class="sc-info"><div class="sc-val"><?= $stats['agotados'] ?></div><div class="sc-lbl">Agotados</div></div>
    </div>
  </div>

  <div class="table-card" style="flex:1;min-height:0;">
    <div class="table-toolbar">
      <div class="tbl-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" id="inv-search" placeholder="Buscar producto...">
      </div>
    </div>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
            <tr><th>SKU</th><th>Producto</th><th>Tipo</th><th>Marca</th><th>Stock</th><th>Stock Mín.</th><th>Precio Venta</th><th>Estado</th></tr>
        </thead>
        <tbody id="inv-tbody">
        <?php foreach($productos as $p):
          $sc = $p['Stock_Actual']==0?'red':($p['Stock_Actual']<=$p['Stock_Minimo']?'orange':'green');
          $sl = $p['Stock_Actual']==0?'Agotado':($p['Stock_Actual']<=$p['Stock_Minimo']?'Stock Bajo':'En Stock');
        ?>
        <tr>
          <td><span class="code-pill"><?= htmlspecialchars($p['SKU']) ?></span></td>
          <td><strong><?= htmlspecialchars($p['Nombre']) ?></strong></td>
          <td style="color:var(--text-muted);"><?= htmlspecialchars($p['tipo']) ?></td>
          <td style="color:var(--text-muted);"><?= htmlspecialchars($p['marca']) ?></td>
          <td><strong style="color:<?= $p['Stock_Actual']==0?'var(--red)':($p['Stock_Actual']<=$p['Stock_Minimo']?'var(--orange)':'var(--green)') ?>; font-variant-numeric: tabular-nums;"><?= $p['Stock_Actual'] ?> uds.</strong></td>
          <td style="color:var(--text-muted);"><?= $p['Stock_Minimo'] ?></td>
          <td><strong style="color:var(--blue); font-variant-numeric: tabular-nums;">Bs <?= number_format($p['Precio_Venta'],2) ?></strong></td>
          <td><span class="badge <?= $sc ?>"><span class="bd"></span><?= $sl ?></span></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal-overlay" id="ajuste-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Ajuste Manual de Inventario</div>
      <button class="modal-close" data-close="ajuste-modal">×</button>
    </div>
    <div class="modal-body">
      <div class="f-group">
        <label class="f-label">Producto</label>
        <select class="f-select" id="aj-producto">
          <option value="">Seleccionar producto...</option>
          <?php foreach($productos as $p): ?>
          <option value="<?= $p['ID_Producto'] ?>"><?= htmlspecialchars($p['Nombre']) ?> (actual: <?= $p['Stock_Actual'] ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="f-group">
        <label class="f-label">Cantidad (+ entrada / - salida)</label>
        <input class="f-input" type="number" id="aj-cantidad" placeholder="Ej: 10 o -5">
      </div>
      <div class="f-group">
        <label class="f-label">Notas / Motivo</label>
        <textarea class="f-textarea" id="aj-notas" placeholder="¿Por qué estás cambiando el stock?"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="ajuste-modal">Cancelar</button>
      <button class="btn-yellow" onclick="registrarAjuste()">Guardar Cambios</button>
    </div>
  </div>
</div>

<script>
// Buscador de tabla
document.getElementById('inv-search')?.addEventListener('input', e => {
    const term = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#inv-tbody tr');
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
    });
});

// Función para enviar el ajuste al controlador
async function registrarAjuste(){
  const p = document.getElementById('aj-producto').value;
  const c = document.getElementById('aj-cantidad').value;
  const n = document.getElementById('aj-notas').value;
  
  if(!p || !c){
    alert('Por favor selecciona un producto y la cantidad.');
    return;
  }

  try {
    const res = await fetch('<?= APP_URL ?>/public/inventario/ajuste', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `producto_id=${p}&cantidad=${c}&notas=${encodeURIComponent(n)}`
    });
    
    const json = await res.json();
    if(json.success){
      alert('¡Listo! Nuevo stock: ' + json.data.nuevo_stock);
      location.reload(); // Recargamos para ver los cambios en la tabla
    } else {
      alert('Error: ' + json.message);
    }
  } catch (error) {
    alert('Hubo un error al conectar con el servidor.');
  }
}
</script>
<?php include ROOT . '/app/Views/partials/footer.php'; ?>