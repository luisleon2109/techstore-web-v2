// assets/js/modules/ventas.js
let ventaDetalle = null;

document.addEventListener('DOMContentLoaded', () => {
  bindSearch();
  bindFiltrar();
  bindVerVenta();
  bindAnular();
  document.getElementById('ticket-close')?.addEventListener('click',  () => closeModal('ticket-modal'));
  document.getElementById('ticket-close2')?.addEventListener('click', () => closeModal('ticket-modal'));
  document.getElementById('ticket-print')?.addEventListener('click', printTicket);
});

function bindSearch() {
  document.getElementById('vta-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#vta-tbody tr').forEach(tr => {
      tr.style.display = !q || tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
}

function bindFiltrar() {
  document.getElementById('filtrar-btn')?.addEventListener('click', () => {
    const desde = document.getElementById('v-desde').value;
    const hasta = document.getElementById('v-hasta').value;
    window.location.href = APP_URL + '/public/ventas?desde=' + desde + '&hasta=' + hasta;
  });
}

function bindVerVenta() {
  document.querySelectorAll('.ver-vta').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      try {
        const res = await fetch(APP_URL + '/public/ventas/detalle?id=' + id);
        const json = await res.json();
        if (json.success) {
          ventaDetalle = json.data;
          renderDetalle(json.data);
        } else {
          toast(json.message, 'error');
        }
      } catch(e) {
        toast('Error al cargar el detalle', 'error');
      }
    });
  });
}

function renderDetalle(v) {
  const sc = v.Estado === 'completada' ? 'green' : (v.Estado === 'anulada' ? 'red' : 'orange');
  document.getElementById('vta-panel-title').textContent = v.Numero;
  document.getElementById('vta-panel-badge').textContent = v.Estado;
  document.getElementById('vta-panel-badge').className = `fp-badge ${sc}`;

  let itemsHTML = (v.items||[]).map(it => `
    <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px dashed var(--border);">
      <div>
        <div style="font-weight:600;font-size:12px;">${it.Nombre||it.nombre}</div>
        <div style="font-size:11px;color:var(--text-muted);">${it.Cantidad||it.cantidad} x Bs ${parseFloat(it.precio||it.Precio_Unitario).toFixed(2)}</div>
      </div>
      <div style="font-weight:700;font-size:13px;">Bs ${parseFloat(it.Subtotal||it.subtotal).toFixed(2)}</div>
    </div>
  `).join('');

  document.getElementById('vta-panel-body').innerHTML = `
    <div style="display:flex;flex-direction:column;gap:10px;height:100%;overflow-y:auto;padding:4px 2px;">
      <div style="background:var(--bg);border-radius:9px;padding:12px;">
        <div style="font-size:9px;font-weight:700;letter-spacing:1.5px;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Cliente</div>
        <div style="font-weight:700;">${v.cliente_nombre || 'Consumidor Final'}</div>
        <div style="font-size:11px;color:var(--text-muted);">NIT: ${v.cliente_nit||'0'} &nbsp;|&nbsp; ${v.fecha} ${v.hora}</div>
        <div style="font-size:11px;color:var(--text-muted);">Cajero: ${v.cajero||'—'}</div>
      </div>
      <div style="background:var(--bg);border-radius:9px;padding:12px;">
        <div style="font-size:9px;font-weight:700;letter-spacing:1.5px;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Productos</div>
        ${itemsHTML}
      </div>
      <div style="background:var(--bg);border-radius:9px;padding:12px;">
        <div style="display:flex;justify-content:space-between;font-size:12px;padding:2px 0;color:var(--text-muted);"><span>Subtotal</span><span>Bs ${parseFloat(v.Subtotal||v.subtotal||0).toFixed(2)}</span></div>
        <div style="display:flex;justify-content:space-between;font-size:12px;padding:2px 0;color:var(--text-muted);"><span>IVA</span><span>Bs ${parseFloat(v.IVA||v.iva||0).toFixed(2)}</span></div>
        <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:800;padding:6px 0 0;border-top:1.5px solid var(--border);margin-top:4px;font-family:'Syne',sans-serif;"><span>TOTAL</span><span style="color:var(--blue);">Bs ${parseFloat(v.Total||v.total||0).toFixed(2)}</span></div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Pago: ${v.metodo||'—'} &nbsp;|&nbsp; Factura: ${v.Numero_Factura||'—'}</div>
      </div>
    </div>
  `;

  document.getElementById('vta-panel-footer').style.display = 'flex';
  document.getElementById('reimprimir-btn')?.addEventListener('click', () => showTicketFromData(ventaDetalle), { once: true });
}

function bindAnular() {
  document.querySelectorAll('.anular-vta').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('anular-id').value  = btn.dataset.id;
      document.getElementById('anular-num').textContent = btn.dataset.num;
      document.getElementById('anular-motivo').value = '';
      openModal('anular-modal');
    });
  });

  document.getElementById('confirmar-anular')?.addEventListener('click', async () => {
    const id     = document.getElementById('anular-id').value;
    const motivo = document.getElementById('anular-motivo').value.trim() || 'Sin motivo';
    const fd     = new FormData();
    fd.append('id', id);
    fd.append('motivo', motivo);

    try {
      const res  = await fetch(APP_URL + '/public/ventas/anular', { method:'POST', body:fd });
      const json = await res.json();
      if (json.success) {
        toast(json.message || 'Venta anulada', 'success');
        closeModal('anular-modal');
        setTimeout(() => location.reload(), 800);
      } else {
        toast(json.message, 'error');
      }
    } catch(e) {
      toast('Error de conexión', 'error');
    }
  });
}

// ── Ticket ─────────────────────────────────────────────────────
function showTicketFromData(v) {
  // Normalizar campos (detalle viene con mayúsculas desde SQL)
  const data = {
    numero:         v.Numero      || v.numero,
    numero_factura: v.Numero_Factura || v.numero_factura,
    fecha:          v.fecha,
    hora:           v.hora,
    subtotal:       v.Subtotal    || v.subtotal    || 0,
    descuento:      v.Descuento   || v.descuento   || 0,
    iva:            v.IVA         || v.iva         || 0,
    iva_pct:        v.iva_pct     || '13%',
    total:          v.Total       || v.total       || 0,
    metodo:         v.metodo,
    cajero:         v.cajero,
    cliente_nombre: v.cliente_nombre,
    cliente_nit:    v.cliente_nit || v.NIT,
    cliente_ci:     v.cliente_ci  || v.CI,
    razon_social:   v.razon_social || v.Razon_Social,
    items:          (v.items||[]).map(it => ({
      nombre:   it.Nombre   || it.nombre,
      cantidad: it.Cantidad || it.cantidad,
      precio:   it.Precio_Unitario || it.precio,
      subtotal: it.Subtotal || it.subtotal,
    })),
    negocio: v.negocio || {},
  };
  renderTicketHTML(data);
  openModal('ticket-modal');
}

function renderTicketHTML(data) {
  const neg     = data.negocio || {};
  const nombre  = neg.negocio_nombre    || 'TechStore';
  const slogan  = neg.negocio_slogan    || '';
  const dir     = neg.negocio_direccion || '';
  const tel     = neg.negocio_telefono  || '';
  const email   = neg.negocio_email     || '';
  const nitEmp  = neg.negocio_nit       || '';
  const mensaje = (neg.ticket_mensaje   || '¡Gracias por su compra!').replace(/\n/g,'<br>');
  const garantia= neg.ticket_garantia   || '';

  const linea  = '<div class="tk-line"></div>';
  const linea2 = '<div class="tk-line dashed"></div>';

  const itemsHTML = data.items.map(it => `
    <div class="tk-item">
      <div class="tk-item-name">${it.nombre}</div>
      <div class="tk-item-row">
        <span>${it.cantidad} x Bs ${parseFloat(it.precio).toFixed(2)}</span>
        <span>Bs ${parseFloat(it.subtotal).toFixed(2)}</span>
      </div>
    </div>
  `).join('');

  document.getElementById('ticket-content').innerHTML = `
    <div class="thermal-ticket" id="printable-ticket">
      <div class="tk-header">
        <div class="tk-logo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="26" height="26"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div>
        <div class="tk-biz-name">${nombre}</div>
        ${slogan  ? `<div class="tk-biz-slogan">${slogan}</div>`     : ''}
        ${dir     ? `<div class="tk-biz-info">${dir}</div>`          : ''}
        ${tel     ? `<div class="tk-biz-info">Tel: ${tel}</div>`     : ''}
        ${email   ? `<div class="tk-biz-info">${email}</div>`        : ''}
        ${nitEmp  ? `<div class="tk-biz-info">NIT: ${nitEmp}</div>`  : ''}
      </div>
      ${linea}
      <div class="tk-section">
        <div class="tk-row"><span>N° Venta</span>   <span class="tk-bold">${data.numero}</span></div>
        <div class="tk-row"><span>N° Factura</span> <span class="tk-bold">${data.numero_factura}</span></div>
        <div class="tk-row"><span>Fecha</span>      <span>${data.fecha}</span></div>
        <div class="tk-row"><span>Hora</span>       <span>${data.hora}</span></div>
        <div class="tk-row"><span>Cajero</span>     <span>${data.cajero||'—'}</span></div>
      </div>
      ${linea2}
      <div class="tk-section">
        <div class="tk-section-title">Cliente</div>
        <div class="tk-row"><span>Nombre</span><span>${data.cliente_nombre||'Consumidor Final'}</span></div>
        ${data.razon_social && data.razon_social!=='CONSUMIDOR FINAL' ? `<div class="tk-row"><span>Razón Social</span><span>${data.razon_social}</span></div>` : ''}
        <div class="tk-row"><span>NIT</span><span>${data.cliente_nit||'0'}</span></div>
        ${data.cliente_ci ? `<div class="tk-row"><span>CI</span><span>${data.cliente_ci}</span></div>` : ''}
      </div>
      ${linea2}
      <div class="tk-section">
        <div class="tk-section-title">Detalle</div>
        ${itemsHTML}
      </div>
      ${linea}
      <div class="tk-section">
        <div class="tk-row"><span>Subtotal</span>   <span>Bs ${parseFloat(data.subtotal).toFixed(2)}</span></div>
        ${parseFloat(data.descuento)>0 ? `<div class="tk-row tk-discount"><span>Descuento</span><span>- Bs ${parseFloat(data.descuento).toFixed(2)}</span></div>` : ''}
        <div class="tk-row"><span>IVA (${data.iva_pct})</span><span>Bs ${parseFloat(data.iva).toFixed(2)}</span></div>
      </div>
      <div class="tk-total-row"><span>TOTAL</span><span>Bs ${parseFloat(data.total).toFixed(2)}</span></div>
      ${linea}
      <div class="tk-section">
        <div class="tk-row"><span>Método de pago</span><span class="tk-bold">${data.metodo||'—'}</span></div>
      </div>
      ${linea}
      <div class="tk-footer">
        <div class="tk-thank-you">${mensaje}</div>
        ${garantia ? `<div class="tk-garantia">${garantia}</div>` : ''}
      </div>
    </div>
  `;
}

function printTicket() {
  const content = document.getElementById('printable-ticket');
  if (!content) return;
  const win = window.open('','_blank','width=400,height=700');
  win.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Ticket</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{background:#fff;font-family:'DM Sans',sans-serif;}
    .thermal-ticket{width:80mm;margin:0 auto;padding:10px 8px;background:#fff;font-size:11px;color:#1a1a1a;line-height:1.5;}
    .tk-header{text-align:center;padding-bottom:8px;}
    .tk-logo-icon{margin-bottom:4px;opacity:.5;}
    .tk-biz-name{font-size:16px;font-weight:800;letter-spacing:1px;text-transform:uppercase;}
    .tk-biz-slogan{font-size:10px;color:#555;margin-top:2px;}
    .tk-biz-info{font-size:10px;color:#444;margin-top:1px;}
    .tk-line{border-top:1.5px solid #1a1a1a;margin:6px 0;}
    .tk-line.dashed{border-top:1px dashed #999;}
    .tk-section{padding:4px 0;}
    .tk-section-title{font-size:9px;font-weight:700;letter-spacing:1.5px;color:#555;text-transform:uppercase;margin-bottom:4px;}
    .tk-row{display:flex;justify-content:space-between;padding:1px 0;font-size:11px;}
    .tk-bold{font-weight:700;}
    .tk-discount{color:#e00;}
    .tk-item{padding:3px 0;}
    .tk-item-name{font-weight:600;font-size:11px;}
    .tk-item-row{display:flex;justify-content:space-between;color:#444;font-size:10px;}
    .tk-total-row{display:flex;justify-content:space-between;font-size:16px;font-weight:800;padding:6px 0;border-top:2px solid #1a1a1a;border-bottom:2px solid #1a1a1a;margin:4px 0;}
    .tk-footer{text-align:center;padding-top:8px;}
    .tk-thank-you{font-size:13px;font-weight:700;line-height:1.5;}
    .tk-garantia{font-size:9px;color:#666;margin-top:6px;line-height:1.4;}
    @media print{body{margin:0;}}
  </style></head><body>${content.outerHTML}<script>window.onload=()=>window.print();<\/script></body></html>`);
  win.document.close();
}
