<?php
$pageTitle = 'Reportes';
$activeNav = 'reportes';
include ROOT . '/app/Views/partials/header.php';
?>
<div class="module-wrap">
  <div class="module-header">
    <div>
      <h1 class="module-title">Reportes</h1>
      <p class="module-sub">Consulta de movimientos y exportación oficial en PDF</p>
    </div>
  </div>

  <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:16px; flex-shrink:0;">
    
    <div style="background:var(--white); border-radius:var(--r); padding:24px; border:1px solid var(--border); box-shadow:var(--sh-sm); display:flex; flex-direction:column; gap:16px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div class="sc-icon blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
        </div>
        <div>
          <div style="font-family:'DM Sans',sans-serif; font-weight:700; font-size:15px; color:var(--text);">Reporte de Ventas</div>
          <div style="font-size:12px; color:var(--text-muted);">Filtro por rango de fechas</div>
        </div>
      </div>
      
      <div class="f-row">
        <div class="f-group">
          <label class="f-label">Desde</label>
          <input class="f-input" type="date" id="v-desde" value="<?= date('Y-m-01') ?>">
        </div>
        <div class="f-group">
          <label class="f-label">Hasta</label>
          <input class="f-input" type="date" id="v-hasta" value="<?= date('Y-m-d') ?>">
        </div>
      </div>

      <div style="display:flex; gap:10px; margin-top:8px;">
        <button class="btn-blue" style="flex:1; justify-content:center; height:44px;" onclick="exportVentas('pdf')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px; width:18px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Descargar PDF
        </button>
        <button class="btn-outline" style="flex:1; justify-content:center; height:44px;" onclick="loadVentasReport()">
          Ver en Pantalla
        </button>
      </div>
    </div>

    <div style="background:var(--white); border-radius:var(--r); padding:24px; border:1px solid var(--border); box-shadow:var(--sh-sm); display:flex; flex-direction:column; gap:16px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div class="sc-icon green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
        </div>
        <div>
          <div style="font-family:'DM Sans',sans-serif; font-weight:700; font-size:15px; color:var(--text);">Reporte de Inventario</div>
          <div style="font-size:12px; color:var(--text-muted);">Stock actual y valorización</div>
        </div>
      </div>
      <p style="font-size:13px; color:var(--text-muted); line-height:1.5;">Consulta el estado de tus artículos, identificando productos agotados y el valor total de tu mercadería en almacén.</p>
      
      <div style="margin-top:auto;">
        <button class="btn-outline" style="width:100%; justify-content:center; height:44px;" onclick="loadInvReport()">
          Visualizar Inventario
        </button>
      </div>
    </div>

  </div>

  <div class="table-card" style="flex:1; min-height:0; display:none; margin-top:20px;" id="report-result">
    <div class="table-toolbar">
      <span style="font-family:'DM Sans',sans-serif; font-weight:700; font-size:14px; color:var(--blue);" id="report-title">—</span>
      <button class="btn-outline" style="height:32px; margin-left:auto; font-size:11px;" onclick="document.getElementById('report-result').style.display='none'">Cerrar Vista</button>
    </div>
    <div class="table-scroll">
      <div id="report-body" style="padding:10px;"></div>
    </div>
  </div>
</div>

<script>
// Funciones de Carga de Reportes
async function loadVentasReport(){
  const desde = document.getElementById('v-desde').value;
  const hasta = document.getElementById('v-hasta').value;
  const res = await fetch('<?= APP_URL ?>/public/reportes/ventas?desde='+desde+'&hasta='+hasta);
  const json = await res.json();
  
  if(!json.success){ alert('Error al cargar datos'); return; }
  
  const {ventas, total} = json.data;
  document.getElementById('report-title').textContent = `Ventas del ${desde} al ${hasta} — Total: Bs ${Number(total).toLocaleString('es-BO',{minimumFractionDigits:2})}`;
  
  document.getElementById('report-body').innerHTML = `
    <table class="data-table">
      <thead><tr><th>N° Venta</th><th>Fecha</th><th>Cliente</th><th>Método</th><th>Total</th></tr></thead>
      <tbody>
        ${ventas.map(v=>`
          <tr>
            <td><span class="code-pill">${v.Numero}</span></td>
            <td>${new Date(v.Fecha).toLocaleDateString('es-BO')}</td>
            <td>${v.cliente || 'Consumidor Final'}</td>
            <td>${v.metodo}</td>
            <td><strong style="color:var(--blue);">Bs ${Number(v.Total).toLocaleString('es-BO',{minimumFractionDigits:2})}</strong></td>
          </tr>`).join('')}
      </tbody>
    </table>`;
  document.getElementById('report-result').style.display = 'flex';
}

async function loadInvReport(){
  const res = await fetch('<?= APP_URL ?>/public/reportes/inventario');
  const json = await res.json();
  
  if(!json.success){ alert('Error'); return; }
  
  document.getElementById('report-title').textContent = 'Estado Actual del Inventario';
  document.getElementById('report-body').innerHTML = `
    <table class="data-table">
      <thead><tr><th>SKU</th><th>Producto</th><th>Tipo</th><th>Stock</th><th>P. Venta</th><th>Valor Total</th></tr></thead>
      <tbody>
        ${json.data.map(p=>`
          <tr>
            <td><span class="code-pill">${p.SKU}</span></td>
            <td>${p.Nombre}</td>
            <td>${p.tipo}</td>
            <td><strong>${p.Stock_Actual} uds.</strong></td>
            <td>Bs ${Number(p.Precio_Venta).toLocaleString('es-BO',{minimumFractionDigits:2})}</td>
            <td><strong style="color:var(--green);">Bs ${Number(p.valor_inventario).toLocaleString('es-BO',{minimumFractionDigits:2})}</strong></td>
          </tr>`).join('')}
      </tbody>
    </table>`;
  document.getElementById('report-result').style.display = 'flex';
}

function exportVentas(fmt){
  const desde = document.getElementById('v-desde').value;
  const hasta = document.getElementById('v-hasta').value;
  // Solo lanzamos el formato solicitado (PDF)
  window.open('<?= APP_URL ?>/public/reportes/ventas?desde='+desde+'&hasta='+hasta+'&formato='+fmt, '_blank');
}
</script>

<?php include ROOT . '/app/Views/partials/footer.php'; ?>