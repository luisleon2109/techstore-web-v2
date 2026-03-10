// assets/js/modules/clientes.js — CRUD de clientes

document.addEventListener('DOMContentLoaded', () => {
  // Search filter
  document.getElementById('cli-search')?.addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('#cli-tbody .cli-row').forEach(tr => {
      tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // New client button
  document.getElementById('new-client-btn')?.addEventListener('click', resetForm);

  // Cancel / clear form
  document.getElementById('cli-cancel')?.addEventListener('click', resetForm);

  // Save
  document.getElementById('cli-save')?.addEventListener('click', saveClient);

  // Edit buttons
  document.querySelectorAll('.edit-cli').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      loadClient(btn.dataset.id);
    });
  });

  // Delete buttons
  document.querySelectorAll('.del-cli').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      deleteClient(btn.dataset.id);
    });
  });

  // Row click → load
  document.querySelectorAll('.cli-row').forEach(tr => {
    tr.addEventListener('click', () => loadClient(tr.dataset.id));
  });
});

function loadClient(id) {
  const c = CLIENTES_DATA.find(x => x.ID_Cliente == id);
  if (!c) return;

  document.getElementById('cli-id').value       = c.ID_Cliente;
  document.getElementById('cli-nombre').value   = c.Nombre || '';
  document.getElementById('cli-apellido').value = c.Apellido || '';
  document.getElementById('cli-ci').value       = c.CI || '';
  document.getElementById('cli-tel').value      = c.Telefono || '';
  document.getElementById('cli-email').value    = c.Email || '';
  document.getElementById('cli-nit').value      = c.NIT || '';
  document.getElementById('cli-razon').value    = c.Razon_Social || '';

  // Badge edit mode
  const badge = document.getElementById('fp-badge');
  badge.textContent = 'EDITAR';
  badge.className = 'fp-badge edit';
  document.getElementById('fp-title').textContent = c.Nombre + ' ' + c.Apellido;

  // Highlight row
  document.querySelectorAll('.cli-row').forEach(r => r.classList.remove('selected'));
  document.querySelector(`.cli-row[data-id="${id}"]`)?.classList.add('selected');

  // Load purchase history
  loadHistory(id);
}

async function loadHistory(id) {
  const container = document.getElementById('cli-history');
  container.innerHTML = '<p style="font-size:12px;color:var(--text-muted);text-align:center;padding:8px;">Cargando...</p>';
  try {
    const res = await api(APP_URL + '/public/clientes/historial?id=' + id);
    if (res.success && res.data.length) {
      container.innerHTML = res.data.map(v => `
        <div class="hist-item">
          <span class="hist-date">${new Date(v.Fecha).toLocaleDateString('es-BO')}</span>
          <span class="hist-num">${v.Numero}</span>
          <span class="badge green"><span class="bd"></span>${v.Estado}</span>
          <span class="hist-tot">Bs ${Number(v.Total).toLocaleString('es-BO', {minimumFractionDigits:2})}</span>
        </div>`).join('');
    } else {
      container.innerHTML = '<p style="font-size:12px;color:var(--text-muted);text-align:center;padding:8px;">Sin compras registradas</p>';
    }
  } catch {
    container.innerHTML = '<p style="font-size:12px;color:var(--red);text-align:center;padding:8px;">Error al cargar historial</p>';
  }
}

async function saveClient() {
  const form = document.getElementById('cli-form');
  const nombre = document.getElementById('cli-nombre').value.trim();
  if (!nombre) { toast('El nombre es requerido', 'error'); return; }

  const data = new URLSearchParams(new FormData(form)).toString();
  try {
    const res = await fetch(APP_URL + '/public/clientes/guardar', {
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
  } catch {
    toast('Error de conexión', 'error');
  }
}

async function deleteClient(id) {
  if (!confirm('¿Eliminar este cliente? Esta acción no se puede deshacer.')) return;
  try {
    const res = await fetch(APP_URL + '/public/clientes/eliminar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'id=' + id,
    });
    const json = await res.json();
    if (json.success) {
      toast('Cliente eliminado', 'success');
      document.querySelector(`.cli-row[data-id="${id}"]`)?.remove();
      resetForm();
    } else {
      toast(json.message, 'error');
    }
  } catch {
    toast('Error de conexión', 'error');
  }
}

function resetForm() {
  document.getElementById('cli-id').value       = '0';
  document.getElementById('cli-nombre').value   = '';
  document.getElementById('cli-apellido').value = '';
  document.getElementById('cli-ci').value       = '';
  document.getElementById('cli-tel').value      = '';
  document.getElementById('cli-email').value    = '';
  document.getElementById('cli-nit').value      = '';
  document.getElementById('cli-razon').value    = '';

  const badge = document.getElementById('fp-badge');
  badge.textContent = 'NUEVO';
  badge.className = 'fp-badge new';
  document.getElementById('fp-title').textContent = 'Nuevo Cliente';
  document.getElementById('cli-history').innerHTML = '<p style="font-size:12px;color:var(--text-muted);text-align:center;padding:10px;">Selecciona un cliente para ver su historial</p>';
  document.querySelectorAll('.cli-row').forEach(r => r.classList.remove('selected'));
}
