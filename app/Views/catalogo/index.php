<?php
$pageTitle = 'Catálogo';
$activeNav = 'catalogo';
$extraJs   = ['catalogo.js'];
include ROOT . '/app/Views/partials/header.php';
?>
<div style="flex:1; padding:20px; display:flex; flex-direction:column; gap:20px; overflow-y:auto; height:100%;">

  <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
    <div>
      <h1 class="module-title">Catálogo Base</h1>
      <p class="module-sub">Gestión de jerarquías para el registro de productos</p>
    </div>
  </div>

  <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:20px; align-items:start;">

    <div style="background:var(--white); border-radius:12px; border:1px solid var(--border); padding:20px; box-shadow:var(--sh-sm); display:flex; flex-direction:column; gap:15px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:42px; height:42px; border-radius:10px; background:var(--blue-soft); color:var(--blue); display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/></svg>
        </div>
        <h3 style="font-family:'DM Sans',sans-serif; font-weight:700; font-size:16px; color:var(--text); margin:0;">Países</h3>
      </div>
      
      <div class="f-group" style="margin:0;">
        <label class="f-label">Nombre del País</label>
        <input class="f-input" id="pais-nombre" placeholder="Ej: Bolivia, USA...">
      </div>
      
      <button class="btn-blue" style="width:100%; justify-content:center; height:42px; margin-top:5px;" onclick="guardarPais()">
        Guardar País
      </button>
    </div>

    <div style="background:var(--white); border-radius:12px; border:1px solid var(--border); padding:20px; box-shadow:var(--sh-sm); display:flex; flex-direction:column; gap:15px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:42px; height:42px; border-radius:10px; background:var(--green-light); color:var(--green); display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        </div>
        <h3 style="font-family:'DM Sans',sans-serif; font-weight:700; font-size:16px; color:var(--text); margin:0;">Tipos</h3>
      </div>
      
      <div class="f-group" style="margin:0;">
        <label class="f-label">Categoría de Producto</label>
        <input class="f-input" id="tipo-nombre" placeholder="Ej: Smartphone, Laptop...">
      </div>
      
      <button class="btn-blue" style="width:100%; justify-content:center; height:42px; margin-top:5px;" onclick="guardarTipo()">
        Guardar Tipo
      </button>
    </div>

    <div style="background:var(--white); border-radius:12px; border:1px solid var(--border); padding:20px; box-shadow:var(--sh-sm); display:flex; flex-direction:column; gap:15px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:42px; height:42px; border-radius:10px; background:var(--orange-light); color:var(--orange); display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        </div>
        <h3 style="font-family:'DM Sans',sans-serif; font-weight:700; font-size:16px; color:var(--text); margin:0;">Marcas</h3>
      </div>
      
      <div class="f-group" style="margin:0;">
        <label class="f-label">Nombre de la Marca</label>
        <input class="f-input" id="marca-nombre" placeholder="Ej: Apple, Samsung...">
      </div>
      <div class="f-group" style="margin:0;">
        <label class="f-label">País de Origen</label>
        <select class="f-select" id="marca-pais">
          <option value="">Seleccionar país...</option>
          <?php foreach($paises as $p): ?>
          <option value="<?= $p['ID_Pais'] ?>"><?= htmlspecialchars($p['Nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <button class="btn-blue" style="width:100%; justify-content:center; height:42px; margin-top:5px;" onclick="guardarMarca()">
        Guardar Marca
      </button>
    </div>

    <div style="background:var(--white); border-radius:12px; border:1px solid var(--border); padding:20px; box-shadow:var(--sh-sm); display:flex; flex-direction:column; gap:15px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:42px; height:42px; border-radius:10px; background:var(--red-light); color:var(--red); display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
        </div>
        <h3 style="font-family:'DM Sans',sans-serif; font-weight:700; font-size:16px; color:var(--text); margin:0;">Modelos</h3>
      </div>
      
      <div class="f-group" style="margin:0;">
        <label class="f-label">Nombre del Modelo</label>
        <input class="f-input" id="modelo-nombre" placeholder="Ej: iPhone 15 Pro Max...">
      </div>
      <div class="f-group" style="margin:0;">
        <label class="f-label">Marca Asociada</label>
        <select class="f-select" id="modelo-marca">
          <option value="">Seleccionar marca...</option>
          <?php foreach($marcas as $m): ?>
          <option value="<?= $m['ID_Marca'] ?>"><?= htmlspecialchars($m['Nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <button class="btn-blue" style="width:100%; justify-content:center; height:42px; margin-top:5px;" onclick="guardarModelo()">
        Guardar Modelo
      </button>
    </div>

  </div>
</div>

<script>
const APP_URL = '<?= APP_URL ?>';

// Cargamos los datos actuales de la BD en JS para validar duplicados
const dbPaises  = <?= json_encode(array_column($paises, 'Nombre')) ?>.map(s => s.toLowerCase());
const dbTipos   = <?= json_encode(array_column($tipos, 'Nombre')) ?>.map(s => s.toLowerCase());
const dbMarcas  = <?= json_encode(array_column($marcas, 'Nombre')) ?>.map(s => s.toLowerCase());
const dbModelos = <?= json_encode(array_column($modelos, 'Nombre')) ?>.map(s => s.toLowerCase());

async function postCat(url, body) {
  const res  = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  const json = await res.json();
  if (json.success) toast(json.message, 'success');
  else toast(json.message, 'error');
  return json.success;
}

// Función auxiliar para refrescar la página silenciosamente y actualizar los selectores
function recargarSelects() {
    setTimeout(() => { location.reload(); }, 1200);
}

async function guardarPais() {
  const nombre = document.getElementById('pais-nombre').value.trim();
  if (!nombre) { toast('Escribe el nombre del país', 'error'); return; }
  
  // VALIDACIÓN: Evitar duplicados
  if (dbPaises.includes(nombre.toLowerCase())) {
      toast('⚠️ Este país ya está registrado.', 'warning');
      return;
  }

  if (await postCat(APP_URL+'/public/catalogo/guardarPais', 'nombre='+encodeURIComponent(nombre))) {
    dbPaises.push(nombre.toLowerCase());
    document.getElementById('pais-nombre').value = '';
    recargarSelects(); // Recarga para que aparezca en el select de Marcas
  }
}

async function guardarTipo() {
  const nombre = document.getElementById('tipo-nombre').value.trim();
  if (!nombre) { toast('Escribe el nombre del tipo', 'error'); return; }

  // VALIDACIÓN: Evitar duplicados
  if (dbTipos.includes(nombre.toLowerCase())) {
      toast('⚠️ Este tipo de producto ya existe.', 'warning');
      return;
  }

  if (await postCat(APP_URL+'/public/catalogo/guardarTipo', 'nombre='+encodeURIComponent(nombre))) {
    dbTipos.push(nombre.toLowerCase());
    document.getElementById('tipo-nombre').value = '';
  }
}

async function guardarMarca() {
  const nombre  = document.getElementById('marca-nombre').value.trim();
  const pais_id = document.getElementById('marca-pais').value;
  if (!nombre || !pais_id) { toast('Completa nombre y país', 'error'); return; }

  // VALIDACIÓN: Evitar duplicados
  if (dbMarcas.includes(nombre.toLowerCase())) {
      toast('⚠️ Esta marca ya está registrada.', 'warning');
      return;
  }

  if (await postCat(APP_URL+'/public/catalogo/guardarMarca', `nombre=${encodeURIComponent(nombre)}&pais_id=${pais_id}`)) {
    dbMarcas.push(nombre.toLowerCase());
    document.getElementById('marca-nombre').value = '';
    document.getElementById('marca-pais').value   = '';
    recargarSelects(); // Recarga para que aparezca en el select de Modelos
  }
}

async function guardarModelo() {
  const nombre   = document.getElementById('modelo-nombre').value.trim();
  const marca_id = document.getElementById('modelo-marca').value;
  if (!nombre || !marca_id) { toast('Completa nombre y marca', 'error'); return; }

  // VALIDACIÓN: Evitar duplicados
  if (dbModelos.includes(nombre.toLowerCase())) {
      toast('⚠️ Este modelo ya está registrado.', 'warning');
      return;
  }

  if (await postCat(APP_URL+'/public/catalogo/guardarModelo', `nombre=${encodeURIComponent(nombre)}&marca_id=${marca_id}`)) {
    dbModelos.push(nombre.toLowerCase());
    document.getElementById('modelo-nombre').value = '';
    document.getElementById('modelo-marca').value  = '';
  }
}
</script>

<?php include ROOT . '/app/Views/partials/footer.php'; ?>