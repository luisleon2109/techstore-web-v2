// assets/js/modules/usuarios.js
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('new-usr-btn')?.addEventListener('click', resetForm);
  document.getElementById('usr-cancel')?.addEventListener('click', resetForm);
  document.getElementById('usr-save')?.addEventListener('click', saveUser);
  document.getElementById('usr-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#usr-tbody tr').forEach(tr => {
      tr.style.display = !q || tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
  document.querySelectorAll('.edit-usr').forEach(btn => {
    btn.addEventListener('click', () => loadUser(btn.dataset));
  });
  document.querySelectorAll('.toggle-usr').forEach(btn => {
    btn.addEventListener('click', () => toggleUser(btn.dataset.id));
  });
});

function resetForm() {
  document.getElementById('usr-id').value      = '0';
  document.getElementById('usr-nombre').value  = '';
  document.getElementById('usr-apellido').value = '';
  document.getElementById('usr-email').value   = '';
  document.getElementById('usr-rol').value     = '';
  document.getElementById('usr-cargo').value   = '';
  document.getElementById('usr-pwd').value     = '';
  document.getElementById('usr-pwd2').value    = '';
  document.getElementById('usr-fp-title').textContent  = 'Nuevo Usuario';
  document.getElementById('usr-fp-badge').textContent  = 'NUEVO';
  document.getElementById('usr-fp-badge').className    = 'fp-badge new';
  document.getElementById('pwd-hint').style.display    = '';
}

function loadUser(d) {
  document.getElementById('usr-id').value      = d.id;
  document.getElementById('usr-nombre').value  = d.nombre;
  document.getElementById('usr-apellido').value = d.apellido;
  document.getElementById('usr-email').value   = d.email;
  document.getElementById('usr-rol').value     = d.rol;
  document.getElementById('usr-cargo').value   = d.cargo;
  document.getElementById('usr-pwd').value     = '';
  document.getElementById('usr-pwd2').value    = '';
  document.getElementById('usr-fp-title').textContent  = d.nombre + ' ' + d.apellido;
  document.getElementById('usr-fp-badge').textContent  = 'EDITANDO';
  document.getElementById('usr-fp-badge').className    = 'fp-badge edit';
  document.getElementById('pwd-hint').style.display    = 'none';
}

async function saveUser() {
  const id       = document.getElementById('usr-id').value;
  const nombre   = document.getElementById('usr-nombre').value.trim();
  const apellido = document.getElementById('usr-apellido').value.trim();
  const email    = document.getElementById('usr-email').value.trim();
  const rol_id   = document.getElementById('usr-rol').value;
  const cargo    = document.getElementById('usr-cargo').value.trim();
  const pwd      = document.getElementById('usr-pwd').value;
  const pwd2     = document.getElementById('usr-pwd2').value;

  if (!nombre || !apellido || !email || !rol_id) { toast('Completá todos los campos requeridos', 'error'); return; }
  if (pwd && pwd !== pwd2) { toast('Las contraseñas no coinciden', 'error'); return; }
  if (!id || id === '0') {
    if (!pwd || pwd.length < 6) { toast('La contraseña debe tener al menos 6 caracteres', 'error'); return; }
  }

  const fd = new FormData();
  fd.append('id',       id);
  fd.append('nombre',   nombre);
  fd.append('apellido', apellido);
  fd.append('email',    email);
  fd.append('rol_id',   rol_id);
  fd.append('cargo',    cargo);
  if (pwd) fd.append('password', pwd);

  try {
    const res  = await fetch(APP_URL + '/public/usuarios/guardar', { method:'POST', body:fd });
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

async function toggleUser(id) {
  const fd = new FormData();
  fd.append('id', id);
  try {
    const res  = await fetch(APP_URL + '/public/usuarios/toggleActivo', { method:'POST', body:fd });
    const json = await res.json();
    if (json.success) {
      toast(json.message, 'success');
      setTimeout(() => location.reload(), 600);
    } else {
      toast(json.message, 'error');
    }
  } catch(e) {
    toast('Error de conexión', 'error');
  }
}
