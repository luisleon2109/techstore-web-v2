// assets/js/modules/compras.js — Lógica de Órdenes de Compra

const PUR_ICONS = {
  Smartphone:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2"/><path d="M12 18h.01"/></svg>`,
  Laptop:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="12" x="3" y="4" rx="2"/><line x1="2" x2="22" y1="20" y2="20"/></svg>`,
  default:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>`,
};

let purCart        = [];
let purProveedor   = null;
let selModalSupId  = null;

document.addEventListener('DOMContentLoaded', () => {
  renderPurGrid(PUR_PRODUCTOS);
  renderSupCards(PUR_PROVEEDORES);

  document.getElementById('pur-search')?.addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    renderPurGrid(PUR_PRODUCTOS.filter(p => p.Nombre.toLowerCase().includes(q) || (p.SKU||'').toLowerCase().includes(q)));
  });

  document.getElementById('pur-clear-btn')?.addEventListener('click', () => {
    if (purCart.length && !confirm('¿Limpiar la orden?')) return;
    clearPur();
  });

  document.getElementById('confirm-pur-btn')?.addEventListener('click', confirmPurchase);

  // Sup modal
  document.getElementById('open-sup-modal')?.addEventListener('click', () => {
    renderModalSup(PUR_PROVEEDORES);
    openModal('sup-modal');
  });
  document.getElementById('confirm-sup-btn')?.addEventListener('click', confirmSupSelect);
  document.getElementById('modal-sup-q')?.addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    renderModalSup(PUR_PROVEEDORES.filter(s => s.Nombre.toLowerCase().includes(q) || (s.NIT||'').includes(q)));
  });
});

// ── Product grid ──
function renderPurGrid(prods) {
  document.getElementById('pur-count').textContent = prods.length + ' artículos';
  const grid = document.getElementById('pur-grid');
  if (!prods.length) { grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:40px;">Sin resultados</div>`; return; }
  grid.innerHTML = prods.map(p => {
    const icon = PUR_ICONS[p.tipo] || PUR_ICONS.default;
    return `<div class="p-card" onclick="addToPur(${p.ID_Producto})">
      <div class="p-icon">${icon}</div>
      <div class="p-name">${p.Nombre}</div>
      <div class="p-foot">
        <span class="p-price" style="font-size:11px;">Bs ${Number(p.Precio_Costo).toLocaleString('es-BO')}<span style="font-size:9px;color:var(--text-muted);font-weight:400"> costo</span></span>
        <span class="stk-b in">${p.Stock_Actual} uds.</span>
      </div>
      <div class="add-ov"><div class="add-ov-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg></div></div>
    </div>`;
  }).join('');
}

// ── Cart logic ──
function addToPur(id) {
  const p = PUR_PRODUCTOS.find(x => x.ID_Producto == id);
  if (!p) return;
  const e = purCart.find(c => c.id == id);
  if (e) e.qty++;
  else purCart.push({ id: p.ID_Producto, nombre: p.Nombre, costo: parseFloat(p.Precio_Costo), qty: 1, tipo: p.tipo });
  renderPurCart();
}

function changePurQty(id, d) {
  const item = purCart.find(c => c.id == id);
  if (!item) return;
  item.qty += d;
  if (item.qty <= 0) purCart = purCart.filter(c => c.id != id);
  renderPurCart();
}

function updateCosto(id, val) {
  const item = purCart.find(c => c.id == id);
  if (item) item.costo = parseFloat(val) || 0;
  renderPurTotals();
}

function removePur(id) { purCart = purCart.filter(c => c.id != id); renderPurCart(); }
function clearPur() { purCart = []; purProveedor = null; renderPurCart(); renderSupDisplay(); renderSupCards(PUR_PROVEEDORES); }

function renderPurCart() {
  const cnt = purCart.reduce((a, c) => a + c.qty, 0);
  document.getElementById('pur-cart-cnt').textContent = cnt;
  const list = document.getElementById('pur-items');
  if (!purCart.length) {
    list.innerHTML = `<div class="empty-state"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg><p>Sin productos en la orden.</p></div>`;
    renderPurTotals(); return;
  }
  list.innerHTML = purCart.map(item => {
    const icon = PUR_ICONS[item.tipo] || PUR_ICONS.default;
    return `<div class="c-item" style="flex-wrap:wrap;gap:6px;">
      <div class="ci-ico">${icon}</div>
      <div class="ci-inf" style="flex:1;min-width:0;">
        <div class="ci-nm">${item.nombre}</div>
        <div style="display:flex;align-items:center;gap:5px;margin-top:3px;">
          <span style="font-size:10px;color:var(--text-muted);">Costo Bs</span>
          <input type="number" style="width:72px;height:26px;border:1.5px solid var(--border);border-radius:6px;text-align:center;font-size:11px;font-weight:700;outline:none;background:white;"
            value="${item.costo}" onchange="updateCosto(${item.id},this.value)" min="0" step="0.01">
        </div>
      </div>
      <div class="ci-qty">
        <button class="qb" onclick="changePurQty(${item.id},-1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg></button>
        <span class="qn">${item.qty}</span>
        <button class="qb" onclick="changePurQty(${item.id},1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg></button>
      </div>
      <span class="ci-tot">${fmt(item.costo * item.qty)}</span>
      <button class="ci-del" onclick="removePur(${item.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
    </div>`;
  }).join('');
  renderPurTotals();
}

function renderPurTotals() {
  const sub = purCart.reduce((a, c) => a + (c.costo || 0) * c.qty, 0);
  document.getElementById('pur-sub').textContent   = fmt(sub);
  document.getElementById('pur-total').textContent = fmt(sub);
}

// ── Supplier cards ──
function renderSupCards(list) {
  const grid = document.getElementById('sup-inline-grid');
  if (!grid) return;
  grid.innerHTML = list.map(s => `
    <div class="sup-card${purProveedor && purProveedor.ID_Proveedor == s.ID_Proveedor ? ' selected' : ''}" onclick="selectSupInline(${s.ID_Proveedor})">
      <div class="sup-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01"/></svg></div>
      <div>
        <div class="sup-nm">${s.Nombre}</div>
        <div style="font-size:10px;color:var(--text-muted);">NIT: ${s.NIT}</div>
        <div style="font-size:9px;color:var(--green);font-weight:600;">${s.Condicion_Pago}</div>
      </div>
    </div>`).join('');
  renderSupBar();
}

function selectSupInline(id) {
  purProveedor = PUR_PROVEEDORES.find(s => s.ID_Proveedor == id);
  renderSupCards(PUR_PROVEEDORES);
  renderSupDisplay();
}

function renderSupBar() {
  const bar = document.getElementById('sup-selected-bar');
  if (!bar) return;
  bar.style.display = purProveedor ? 'flex' : 'none';
  if (purProveedor) {
    bar.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
    <span style="flex:1;font-size:12px;font-weight:600;color:var(--blue);">${purProveedor.Nombre}</span>
    <span style="font-size:11px;color:var(--blue-light);">NIT: ${purProveedor.NIT}</span>`;
  }
}

function renderSupDisplay() {
  const d = document.getElementById('pur-sup-display');
  if (!d) return;
  if (!purProveedor) {
    d.innerHTML = `<div style="display:flex;align-items:center;gap:9px;padding:8px 11px;background:var(--bg);border:1.5px dashed var(--border);border-radius:9px;color:var(--text-muted);font-size:12px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/></svg> Sin proveedor seleccionado</div>`;
    return;
  }
  d.innerHTML = `<div class="sel-filled">
    <div class="sel-avatar" style="background:var(--green);"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/></svg></div>
    <div style="flex:1;min-width:0;"><div class="sel-nm">${purProveedor.Nombre}</div><div class="sel-sub">NIT: ${purProveedor.NIT}</div></div>
    <span class="badge blue">${purProveedor.Condicion_Pago}</span>
  </div>`;
}

// ── Supplier modal ──
function renderModalSup(list) {
  document.getElementById('modal-sup-list').innerHTML = list.map(s => `
    <div class="modal-item${selModalSupId == s.ID_Proveedor ? ' sel' : ''}" onclick="toggleModalSup(${s.ID_Proveedor})">
      <div class="mi-ico green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/></svg></div>
      <div class="mi-info"><div class="mi-name">${s.Nombre}</div><div class="mi-sub">NIT: ${s.NIT} · ${s.ciudad || ''} · ${s.Condicion_Pago}</div></div>
      <div class="mi-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></div>
    </div>`).join('');
}

function toggleModalSup(id) { selModalSupId = id; renderModalSup(PUR_PROVEEDORES); }
function confirmSupSelect() {
  if (!selModalSupId) { toast('Selecciona un proveedor', 'error'); return; }
  purProveedor = PUR_PROVEEDORES.find(s => s.ID_Proveedor == selModalSupId);
  renderSupCards(PUR_PROVEEDORES); renderSupDisplay();
  closeModal('sup-modal');
  toast('Proveedor: ' + purProveedor.Nombre);
}

// ── Confirm purchase ──
async function confirmPurchase() {
  if (!purProveedor) { toast('Selecciona un proveedor primero', 'error'); return; }
  if (!purCart.length) { toast('La orden está vacía', 'error'); return; }

  const btn = document.getElementById('confirm-pur-btn');
  btn.disabled = true; btn.textContent = 'Registrando...';

  const payload = {
    proveedor_id: purProveedor.ID_Proveedor,
    items: purCart.map(c => ({ id: c.id, cantidad: c.qty, costo: c.costo })),
    descuento: 0,
  };

  try {
    const res = await api(APP_URL + '/public/compras/registrar', payload);
    if (res.success) {
      toast(`📦 ${res.data.numero} registrada — ${fmt(res.data.total)}`, 'success');
      clearPur();
    } else {
      toast(res.message, 'error');
    }
  } catch { toast('Error de conexión', 'error'); }
  finally {
    btn.disabled = false;
    btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg> Registrar Orden de Compra`;
  }
}
