// assets/js/modules/pos.js  ← REEMPLAZAR el archivo existente
// ── Estado ─────────────────────────────────────────────────────
let cart = [];
let selectedClient  = null;
let selectedMetodo  = 1;
let selectedModalClient = null;

// ── Init ────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  renderProductGrid();
  renderCart();
  renderClientRow();
  bindProductSearch();
  bindMetodos();
  document.getElementById('confirm-sale')?.addEventListener('click', confirmSale);
  document.getElementById('clear-cart')?.addEventListener('click', clearCart);
  document.getElementById('open-client-modal')?.addEventListener('click', () => openModal('client-modal'));
  document.getElementById('client-search')?.addEventListener('input', filterClients);
  document.getElementById('confirm-client-btn')?.addEventListener('click', confirmClient);
  // Cerrar ticket modal
  document.getElementById('ticket-close')?.addEventListener('click', () => closeModal('ticket-modal'));
  document.getElementById('ticket-print')?.addEventListener('click', printTicket);
  document.getElementById('ticket-new-sale')?.addEventListener('click', () => { closeModal('ticket-modal'); clearCart(); });
});

// ── Productos ───────────────────────────────────────────────────
function renderProductGrid() {
  const grid = document.getElementById('product-grid');
  if (!grid) return;
  const q = (document.getElementById('prod-search')?.value || '').toLowerCase();
  const filtered = POS_PRODUCTOS.filter(p =>
    !q || p.Nombre.toLowerCase().includes(q) || (p.marca||'').toLowerCase().includes(q) || (p.SKU||'').toLowerCase().includes(q)
  );
  grid.innerHTML = filtered.length ? filtered.map(p => `
    <div class="prod-card ${p.Stock_Actual<1?'out-stock':''}" onclick="addToCart(${p.ID_Producto})">
      <div class="pc-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" x2="12" y1="18" y2="18.01"/>
        </svg>
      </div>
      <div class="pc-name">${p.Nombre}</div>
      <div class="pc-brand">${p.marca||''} ${p.modelo||''}</div>
      <div class="pc-price">${fmt(p.Precio_Venta)}</div>
      <div class="pc-stock ${p.Stock_Actual<1?'empty':p.Stock_Actual<5?'low':''}">
        ${p.Stock_Actual<1 ? 'Sin stock' : `Stock: ${p.Stock_Actual}`}
      </div>
    </div>
  `).join('') : '<p class="empty-msg">Sin resultados</p>';
}

function bindProductSearch() {
  document.getElementById('prod-search')?.addEventListener('input', renderProductGrid);
}

// ── Carrito ─────────────────────────────────────────────────────
function addToCart(id) {
  const p = POS_PRODUCTOS.find(x => x.ID_Producto == id);
  if (!p || p.Stock_Actual < 1) { toast('Sin stock disponible', 'error'); return; }
  const ex = cart.find(x => x.id == id);
  if (ex) {
    if (ex.qty >= p.Stock_Actual) { toast('No hay más stock', 'error'); return; }
    ex.qty++;
  } else {
    cart.push({ id: p.ID_Producto, nombre: p.Nombre, precio: parseFloat(p.Precio_Venta), qty: 1, stock: p.Stock_Actual });
  }
  renderCart();
}

function changeQty(id, delta) {
  const item = cart.find(x => x.id == id);
  if (!item) return;
  item.qty += delta;
  if (item.qty <= 0) cart = cart.filter(x => x.id != id);
  else if (item.qty > item.stock) { item.qty -= delta; toast('Stock máximo alcanzado', 'error'); }
  renderCart();
}

function removeFromCart(id) {
  cart = cart.filter(x => x.id != id);
  renderCart();
}

function clearCart() {
  cart = [];
  selectedClient = null;
  renderCart();
  renderClientRow();
}

function renderCart() {
  const body  = document.getElementById('cart-items');
  const badge = document.getElementById('cart-badge');
  const totalItems = cart.reduce((s, c) => s + c.qty, 0);
  if (badge) badge.textContent = totalItems;

  if (!body) return;
  if (!cart.length) {
    body.innerHTML = '<div class="cart-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="32" height="32"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg><p>Carrito vacío</p></div>';
    updateTotals(0, 0, 0);
    return;
  }

  body.innerHTML = cart.map(item => `
    <div class="cart-item">
      <div class="ci-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" x2="12" y1="18" y2="18.01"/></svg></div>
      <div class="ci-info">
        <div class="ci-name">${item.nombre}</div>
        <div class="ci-price">Bs ${item.precio.toFixed(2)} c/u</div>
      </div>
      <div class="ci-qty">
        <button class="qty-btn" onclick="changeQty(${item.id},-1)">−</button>
        <span>${item.qty}</span>
        <button class="qty-btn" onclick="changeQty(${item.id},1)">+</button>
      </div>
      <div class="ci-sub">Bs ${(item.precio*item.qty).toFixed(2)}</div>
      <button class="ci-del" onclick="removeFromCart(${item.id})">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
      </button>
    </div>
  `).join('');

  const subtotal = cart.reduce((s, c) => s + c.precio * c.qty, 0);
  const iva      = subtotal * IVA_RATE;
  const total    = subtotal + iva;
  updateTotals(subtotal, iva, total);
}

function updateTotals(sub, iva, total) {
  const set = (id, v) => { const el = document.getElementById(id); if(el) el.textContent = v; };
  set('cart-subtotal', `Bs ${sub.toFixed(2)}`);
  set('cart-iva',      `Bs ${iva.toFixed(2)}`);
  set('cart-total',    `Bs ${total.toFixed(2)}`);
  set('cart-descuento', '− Bs 0.00');
}

// ── Métodos de pago ─────────────────────────────────────────────
function bindMetodos() {
  document.querySelectorAll('.metodo-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.metodo-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      selectedMetodo = parseInt(btn.dataset.id);
    });
  });
}

// ── Cliente ─────────────────────────────────────────────────────
function filterClients() {
  const q = (document.getElementById('client-search')?.value || '').toLowerCase();
  document.querySelectorAll('.client-option').forEach(el => {
    el.style.display = !q || el.dataset.search.includes(q) ? '' : 'none';
  });
}

function selectClientOption(id) {
  selectedModalClient = id;
  document.querySelectorAll('.client-option').forEach(el => {
    el.classList.toggle('selected', el.dataset.id == id);
  });
}

function confirmClient() {
  if (!selectedModalClient) { toast('Selecciona un cliente', 'error'); return; }
  selectedClient = POS_CLIENTES.find(c => c.ID_Cliente == selectedModalClient);
  renderClientRow();
  closeModal('client-modal');
  toast('Cliente: ' + selectedClient.Nombre + ' ' + selectedClient.Apellido);
}

function renderClientRow() {
  const row = document.getElementById('client-row');
  if (!row) return;
  if (!selectedClient) {
    row.innerHTML = `<div class="sel-btn" id="open-client-modal">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
      <span>Seleccionar Cliente / NIT</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13" style="color:var(--text-light);flex-shrink:0;"><path d="m9 18 6-6-6-6"/></svg>
    </div>`;
    document.getElementById('open-client-modal')?.addEventListener('click', () => openModal('client-modal'));
    return;
  }
  row.innerHTML = `<div class="sel-filled">
    <div class="sel-avatar blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg></div>
    <div style="flex:1;min-width:0;">
      <div class="sel-nm">${selectedClient.Nombre} ${selectedClient.Apellido}</div>
      <div class="sel-sub">NIT: ${selectedClient.NIT || '—'}</div>
    </div>
    <span class="sel-change" onclick="openModal('client-modal')">Cambiar</span>
  </div>`;
}

// ── Confirmar venta ─────────────────────────────────────────────
async function confirmSale() {
  if (!cart.length) { toast('El carrito está vacío', 'error'); return; }

  const btn = document.getElementById('confirm-sale');
  btn.disabled = true;
  btn.innerHTML = '<span style="opacity:.7">Procesando...</span>';

  const payload = {
    items:      cart.map(c => ({ id: c.id, cantidad: c.qty, precio: c.precio })),
    metodo_id:  selectedMetodo,
    cliente_id: selectedClient?.ID_Cliente || null,
    descuento:  0,
  };

  try {
    const res = await api(APP_URL + '/public/pos/vender', payload);
    if (res.success) {
      showTicket(res.data);
    } else {
      toast(res.message || 'Error al procesar la venta', 'error');
    }
  } catch (e) {
    toast('Error de conexión', 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Confirmar Venta y Facturar`;
  }
}

// ── Ticket térmico ──────────────────────────────────────────────
function showTicket(data) {
  const neg = (typeof NEGOCIO_CFG !== 'undefined' && Object.keys(NEGOCIO_CFG).length)
              ? NEGOCIO_CFG
              : (data.negocio || {});
  const nombre    = neg.negocio_nombre    || 'TechStore';
  const slogan    = neg.negocio_slogan    || '';
  const dir       = neg.negocio_direccion || '';
  const tel       = neg.negocio_telefono  || '';
  const email     = neg.negocio_email     || '';
  const nitEmp    = neg.negocio_nit       || '';
  const mensaje   = (neg.ticket_mensaje   || '¡Gracias por su compra!').replace(/\n/g,'<br>');
  const garantia  = neg.ticket_garantia   || '';

  const linea = '<div class="tk-line"></div>';
  const linea2 = '<div class="tk-line dashed"></div>';

  let itemsHTML = data.items.map(it => `
    <div class="tk-item">
      <div class="tk-item-name">${it.nombre}</div>
      <div class="tk-item-row">
        <span>${it.cantidad} x Bs ${parseFloat(it.precio).toFixed(2)}</span>
        <span>Bs ${parseFloat(it.subtotal).toFixed(2)}</span>
      </div>
    </div>
  `).join('');

  const html = `
    <div class="thermal-ticket" id="printable-ticket">
      <!-- Encabezado negocio -->
      <div class="tk-header">
        <div class="tk-logo-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        </div>
        <div class="tk-biz-name">${nombre}</div>
        ${slogan ? `<div class="tk-biz-slogan">${slogan}</div>` : ''}
        ${dir    ? `<div class="tk-biz-info">${dir}</div>` : ''}
        ${tel    ? `<div class="tk-biz-info">Tel: ${tel}</div>` : ''}
        ${email  ? `<div class="tk-biz-info">${email}</div>` : ''}
        ${nitEmp ? `<div class="tk-biz-info">NIT: ${nitEmp}</div>` : ''}
      </div>

      ${linea}

      <!-- Datos de la venta -->
      <div class="tk-section">
        <div class="tk-row"><span>N° Venta</span><span class="tk-bold">${data.numero}</span></div>
        <div class="tk-row"><span>N° Factura</span><span class="tk-bold">${data.numero_factura}</span></div>
        <div class="tk-row"><span>Fecha</span><span>${data.fecha}</span></div>
        <div class="tk-row"><span>Hora</span><span>${data.hora}</span></div>
        <div class="tk-row"><span>Cajero</span><span>${data.cajero || '—'}</span></div>
      </div>

      ${linea2}

      <!-- Datos del cliente -->
      <div class="tk-section">
        <div class="tk-section-title">CLIENTE</div>
        <div class="tk-row"><span>Nombre</span><span>${data.cliente_nombre || 'Consumidor Final'}</span></div>
        ${data.razon_social && data.razon_social !== 'CONSUMIDOR FINAL' ? `<div class="tk-row"><span>Razón Social</span><span>${data.razon_social}</span></div>` : ''}
        <div class="tk-row"><span>NIT</span><span>${data.cliente_nit || '0'}</span></div>
        ${data.cliente_ci ? `<div class="tk-row"><span>CI</span><span>${data.cliente_ci}</span></div>` : ''}
      </div>

      ${linea2}

      <!-- Items -->
      <div class="tk-section">
        <div class="tk-section-title">DETALLE</div>
        ${itemsHTML}
      </div>

      ${linea}

      <!-- Totales -->
      <div class="tk-section">
        <div class="tk-row"><span>Subtotal</span><span>Bs ${parseFloat(data.subtotal).toFixed(2)}</span></div>
        ${parseFloat(data.descuento) > 0 ? `<div class="tk-row tk-discount"><span>Descuento</span><span>- Bs ${parseFloat(data.descuento).toFixed(2)}</span></div>` : ''}
        <div class="tk-row"><span>IVA (${data.iva_pct})</span><span>Bs ${parseFloat(data.iva).toFixed(2)}</span></div>
      </div>

      <div class="tk-total-row">
        <span>TOTAL</span>
        <span>Bs ${parseFloat(data.total).toFixed(2)}</span>
      </div>

      ${linea}

      <!-- Método de pago -->
      <div class="tk-section">
        <div class="tk-row"><span>Método de pago</span><span class="tk-bold">${data.metodo || '—'}</span></div>
      </div>

      ${linea}

      <!-- Mensaje final -->
      <div class="tk-footer">
        <div class="tk-thank-you">${mensaje}</div>
        ${garantia ? `<div class="tk-garantia">${garantia}</div>` : ''}
      </div>
    </div>
  `;

  document.getElementById('ticket-content').innerHTML = html;
  openModal('ticket-modal');
}

function printTicket() {
  const content = document.getElementById('printable-ticket');
  if (!content) return;

  const win = window.open('', '_blank', 'width=380,height=700');
  win.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Ticket de Venta</title>
      <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;600&display=swap');
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:#fff; font-family:'DM Sans',sans-serif; }
        .thermal-ticket { width:80mm; min-height:100%; margin:0 auto; padding:10px 8px; background:#fff; font-size:11px; color:#1a1a1a; }
        .tk-header { text-align:center; padding-bottom:8px; }
        .tk-logo-icon { margin-bottom:4px; opacity:.5; }
        .tk-biz-name { font-size:16px; font-weight:700; letter-spacing:1px; text-transform:uppercase; }
        .tk-biz-slogan { font-size:10px; color:#555; margin-top:2px; }
        .tk-biz-info { font-size:10px; color:#444; margin-top:1px; }
        .tk-line { border-top:1.5px solid #1a1a1a; margin:6px 0; }
        .tk-line.dashed { border-top:1px dashed #999; }
        .tk-section { padding:4px 0; }
        .tk-section-title { font-size:9px; font-weight:700; letter-spacing:1.5px; color:#555; text-transform:uppercase; margin-bottom:4px; }
        .tk-row { display:flex; justify-content:space-between; padding:1px 0; font-size:11px; }
        .tk-bold { font-weight:700; }
        .tk-discount { color:#e00; }
        .tk-item { padding:3px 0; }
        .tk-item-name { font-weight:600; font-size:11px; }
        .tk-item-row { display:flex; justify-content:space-between; color:#444; font-size:10px; }
        .tk-total-row { display:flex; justify-content:space-between; font-size:16px; font-weight:800; padding:6px 0; border-top:2px solid #1a1a1a; border-bottom:2px solid #1a1a1a; margin:4px 0; }
        .tk-footer { text-align:center; padding-top:8px; }
        .tk-thank-you { font-size:13px; font-weight:700; line-height:1.5; }
        .tk-garantia { font-size:9px; color:#666; margin-top:6px; line-height:1.4; }
        @media print { body{margin:0;} }
      </style>
    </head>
    <body>
      ${content.outerHTML}
      <script>window.onload=()=>{window.print();}<\/script>
    </body>
    </html>
  `);
  win.document.close();
}
