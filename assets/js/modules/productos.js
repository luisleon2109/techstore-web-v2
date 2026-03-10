// assets/js/modules/productos.js

document.addEventListener('DOMContentLoaded', () => {

  // Búsqueda
  document.getElementById('prod-search')?.addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('#prod-tbody .prod-row').forEach(tr => {
      tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // Nueva fila → limpiar
  document.getElementById('new-prod-btn')?.addEventListener('click', resetForm);
  document.getElementById('prod-cancel')?.addEventListener('click', resetForm);

  // Guardar
  document.getElementById('prod-save')?.addEventListener('click', saveProducto);

  // Editar
  document.querySelectorAll('.edit-prod').forEach(btn => {
    btn.addEventListener('click', e => { e.stopPropagation(); loadProducto(btn.dataset.id); });
  });

  // Eliminar
  document.querySelectorAll('.del-prod').forEach(btn => {
    btn.addEventListener('click', e => { e.stopPropagation(); deleteProducto(btn.dataset.id); });
  });

  // Fila clic
  document.querySelectorAll('.prod-row').forEach(tr => {
    tr.addEventListener('click', () => loadProducto(tr.dataset.id));
  });

});

function loadProducto(id) {
  const p = PROD_DATA.find(x => x.ID_Producto == id);
  if (!p) return;

  document.getElementById('prod-id').value       = p.ID_Producto;
  document.getElementById('prod-sku').value       = p.SKU || '';
  document.getElementById('prod-nombre').value    = p.Nombre || '';
  document.getElementById('prod-desc').value      = p.Descripcion || '';
  document.getElementById('prod-costo').value     = p.Precio_Costo || '';
  document.getElementById('prod-precio').value    = p.Precio_Venta || '';
  document.getElementById('prod-voltaje').value   = p.Voltaje || '';
  document.getElementById('prod-potencia').value  = p.Potencia_Watts || '';
  document.getElementById('prod-garantia').value  = p.Garantia_Meses || 12;
  document.getElementById('prod-stockmin').value  = p.Stock_Minimo || 5;
  document.getElementById('prod-tipo').value      = p.ID_Tipo || '';
  document.getElementById('prod-modelo').value    = p.ID_Modelo || '';

  // Ocultar stock inicial en modo edición
  document.getElementById('stock-inicial-row').style.display = 'none';

  const badge = document.getElementById('prod-fp-badge');
  badge.textContent = 'EDITAR'; badge.className = 'fp-badge edit';
  document.getElementById('prod-fp-title').textContent = p.Nombre;

  document.querySelectorAll('.prod-row').forEach(r => r.classList.remove('selected'));
  document.querySelector(`.prod-row[data-id="${id}"]`)?.classList.add('selected');
}

async function saveProducto() {
  const nombre = document.getElementById('prod-nombre').value.trim();
  const precio = document.getElementById('prod-precio').value;
  const tipo   = document.getElementById('prod-tipo').value;
  const modelo = document.getElementById('prod-modelo').value;

  if (!nombre) { toast('El nombre es requerido', 'error'); return; }
  if (!precio) { toast('El precio de venta es requerido', 'error'); return; }
  if (!tipo)   { toast('Selecciona el tipo de producto', 'error'); return; }
  if (!modelo) { toast('Selecciona el modelo', 'error'); return; }

  const form = document.getElementById('prod-form');
  const data = new URLSearchParams(new FormData(form)).toString();

  try {
    const res  = await fetch(APP_URL + '/public/productos/guardar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: data,
    });
    const json = await res.json();
    if (json.success) {
      toast(json.message, 'success');
      setTimeout(() => location.reload(), 800);
    } else {
      toast(json.message, 'error');
    }
  } catch { toast('Error de conexión', 'error'); }
}

async function deleteProducto(id) {
  if (!confirm('¿Eliminar este producto?')) return;
  try {
    const res  = await fetch(APP_URL + '/public/productos/eliminar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'id=' + id,
    });
    const json = await res.json();
    if (json.success) {
      toast('Producto eliminado', 'success');
      document.querySelector(`.prod-row[data-id="${id}"]`)?.remove();
      resetForm();
    } else {
      toast(json.message, 'error');
    }
  } catch { toast('Error de conexión', 'error'); }
}

function resetForm() {
  document.getElementById('prod-id').value       = '0';
  document.getElementById('prod-sku').value       = '';
  document.getElementById('prod-nombre').value    = '';
  document.getElementById('prod-desc').value      = '';
  document.getElementById('prod-costo').value     = '';
  document.getElementById('prod-precio').value    = '';
  document.getElementById('prod-voltaje').value   = '';
  document.getElementById('prod-potencia').value  = '';
  document.getElementById('prod-garantia').value  = '12';
  document.getElementById('prod-stockmin').value  = '5';
  document.getElementById('prod-stockini').value  = '0';
  document.getElementById('prod-tipo').value      = '';
  document.getElementById('prod-modelo').value    = '';
  document.getElementById('stock-inicial-row').style.display = '';

  const badge = document.getElementById('prod-fp-badge');
  badge.textContent = 'NUEVO'; badge.className = 'fp-badge new';
  document.getElementById('prod-fp-title').textContent = 'Nuevo Producto';
  document.querySelectorAll('.prod-row').forEach(r => r.classList.remove('selected'));
}
