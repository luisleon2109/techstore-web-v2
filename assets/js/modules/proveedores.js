// assets/js/modules/proveedores.js
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('new-prov-btn')?.addEventListener('click', resetForm);
  document.getElementById('prov-cancel')?.addEventListener('click', resetForm);
  document.getElementById('prov-save')?.addEventListener('click', saveProv);
  document.getElementById('prov-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#prov-tbody tr').forEach(tr => {
      tr.style.display = !q || tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
  document.querySelectorAll('.edit-prov').forEach(btn => btn.addEventListener('click', () => loadProv(btn.dataset)));
  document.querySelectorAll('.del-prov').forEach(btn => btn.addEventListener('click', () => delProv(btn.dataset.id)));
});

function resetForm() {
  ['prov-id','prov-nombre','prov-nit','prov-tel','prov-email','prov-dir','prov-cond'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = id === 'prov-id' ? '0' : '';
  });
  document.getElementById('prov-pais').value = '';
  document.getElementById('prov-fp-title').textContent = 'Nuevo Proveedor';
  document.getElementById('prov-fp-badge').textContent = 'NUEVO';
  document.getElementById('prov-fp-badge').className   = 'fp-badge new';
  document.getElementById('prov-hist').innerHTML = '<p style="font-size:11px;color:var(--text-muted);text-align:center;padding:8px 0;">Seleccioná un proveedor para ver su historial</p>';
}

function loadProv(d) {
  document.getElementById('prov-id').value     = d.id;
  document.getElementById('prov-nombre').value = d.nombre;
  document.getElementById('prov-nit').value    = d.nit;
  document.getElementById('prov-tel').value    = d.tel;
  document.getElementById('prov-email').value  = d.email;
  document.getElementById('prov-dir').value    = d.dir;
  document.getElementById('prov-pais').value   = d.pais;
  document.getElementById('prov-cond').value   = d.cond;
  document.getElementById('prov-fp-title').textContent = d.nombre;
  document.getElementById('prov-fp-badge').textContent = 'EDITANDO';
  document.getElementById('prov-fp-badge').className   = 'fp-badge edit';
  loadHistorial(d.id);
}

async function loadHistorial(id) {
  const hist = document.getElementById('prov-hist');
  hist.innerHTML = '<p style="font-size:11px;color:var(--text-muted);text-align:center;">Cargando...</p>';
  try {
    const res  = await fetch(APP_URL + '/public/proveedores/historial?id=' + id);
    const json = await res.json();
    const rows = json.data || [];
    if (!rows.length) { hist.innerHTML = '<p style="font-size:11px;color:var(--text-muted);text-align:center;padding:8px;">Sin órdenes registradas</p>'; return; }
    hist.innerHTML = rows.map(r => `
      <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px dashed var(--border);font-size:11px;">
        <span><strong>${r.Numero_Orden}</strong> &nbsp; ${r.fecha}</span>
        <span style="color:var(--blue);font-weight:700;">Bs ${parseFloat(r.Total).toFixed(2)}</span>
      </div>
    `).join('');
  } catch(e) {
    hist.innerHTML = '<p style="font-size:11px;color:red;text-align:center;">Error al cargar</p>';
  }
}

async function saveProv() {
  const nombre = document.getElementById('prov-nombre').value.trim();
  if (!nombre) { toast('El nombre es requerido', 'error'); return; }

  const fd = new FormData();
  fd.append('id',              document.getElementById('prov-id').value);
  fd.append('nombre',          nombre);
  fd.append('nit',             document.getElementById('prov-nit').value.trim());
  fd.append('telefono',        document.getElementById('prov-tel').value.trim());
  fd.append('email',           document.getElementById('prov-email').value.trim());
  fd.append('direccion',       document.getElementById('prov-dir').value.trim());
  fd.append('pais_id',         document.getElementById('prov-pais').value);
  fd.append('condicion_pago',  document.getElementById('prov-cond').value.trim());

  try {
    const res  = await fetch(APP_URL + '/public/proveedores/guardar', { method:'POST', body:fd });
    const json = await res.json();
    if (json.success) {
      toast(json.message, 'success');
      setTimeout(() => location.reload(), 700);
    } else {
      toast(json.message, 'error');
    }
  } catch(e) {
    toast('Error de conexión', 'error');
  }
}

async function delProv(id) {
  if (!confirm('¿Eliminar este proveedor?')) return;
  const fd = new FormData();
  fd.append('id', id);
  try {
    const res  = await fetch(APP_URL + '/public/proveedores/eliminar', { method:'POST', body:fd });
    const json = await res.json();
    if (json.success) {
      toast('Proveedor eliminado', 'success');
      setTimeout(() => location.reload(), 600);
    } else {
      toast(json.message, 'error');
    }
  } catch(e) {
    toast('Error de conexión', 'error');
  }
}
