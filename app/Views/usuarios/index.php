<?php
// app/Views/usuarios/index.php
$pageTitle = 'Usuarios';
$activeNav = 'usuarios';
$extraJs   = ['usuarios.js'];
include ROOT . '/app/Views/partials/header.php';
?>
<div class="module-wrap">
  <div class="module-header">
    <div><h1 class="module-title">Usuarios</h1><p class="module-sub">Gestión de accesos y roles del sistema</p></div>
    <div class="hdr-actions">
      <button class="btn-yellow" id="new-usr-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        Nuevo Usuario
      </button>
    </div>
  </div>

  <div class="module-body two-col">
    <div class="table-card">
      <div class="table-toolbar">
        <div class="tbl-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" id="usr-search" placeholder="Buscar usuario...">
        </div>
      </div>
      <div class="table-scroll">
        <table class="data-table">
          <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Cargo</th><th>Estado</th><th></th></tr></thead>
          <tbody id="usr-tbody">
          <?php foreach($usuarios as $u):
            $rolColor = $u['rol']==='Administrador' ? 'blue' : ($u['rol']==='Cajero' ? 'green' : 'orange');
          ?>
          <tr>
            <td><strong><?= htmlspecialchars($u['nombre'].' '.$u['apellido']) ?></strong></td>
            <td style="color:var(--text-muted);font-size:11px;"><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="badge <?= $rolColor ?>"><span class="bd"></span><?= htmlspecialchars($u['rol']) ?></span></td>
            <td style="color:var(--text-muted);"><?= htmlspecialchars($u['Cargo'] ?? '—') ?></td>
            <td><span class="badge <?= $u['activo'] ? 'green' : 'red' ?>"><span class="bd"></span><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
            <td>
              <div class="act-btns">
                <button class="act-b edit-usr"
                  data-id="<?= $u['id'] ?>"
                  data-nombre="<?= htmlspecialchars($u['nombre']) ?>"
                  data-apellido="<?= htmlspecialchars($u['apellido']) ?>"
                  data-email="<?= htmlspecialchars($u['email']) ?>"
                  data-rol="<?= $u['rol_id'] ?>"
                  data-cargo="<?= htmlspecialchars($u['Cargo'] ?? '') ?>"
                  title="Editar">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button class="act-b <?= !$u['activo'] ? '' : 'act-del' ?> toggle-usr"
                  data-id="<?= $u['id'] ?>"
                  title="<?= $u['activo'] ? 'Desactivar' : 'Activar' ?>">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" x2="12" y1="2" y2="12"/></svg>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Form panel -->
    <div class="form-panel" id="usr-panel">
      <div class="fp-header">
        <h3 id="usr-fp-title">Nuevo Usuario</h3>
        <span class="fp-badge new" id="usr-fp-badge">NUEVO</span>
      </div>
      <div class="fp-body">
        <div class="fp-avatar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
        </div>
        <div class="f-row">
          <div class="f-group"><label class="f-label">Nombre *</label><input class="f-input" type="text" id="usr-nombre" placeholder="Luis"></div>
          <div class="f-group"><label class="f-label">Apellido *</label><input class="f-input" type="text" id="usr-apellido" placeholder="Adolfo"></div>
        </div>
        <div class="f-group"><label class="f-label">Email *</label><input class="f-input" type="email" id="usr-email" placeholder="usuario@techstore.bo"></div>
        <div class="f-group">
          <label class="f-label">Rol *</label>
          <select class="f-select" id="usr-rol">
            <option value="">Seleccionar...</option>
            <?php foreach($roles as $r): ?>
            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="f-group"><label class="f-label">Cargo</label><input class="f-input" type="text" id="usr-cargo" placeholder="Cajero, Almacenero..."></div>

        <div style="background:var(--bg);border-radius:10px;padding:12px;display:flex;flex-direction:column;gap:8px;">
          <div style="font-size:10px;font-weight:700;letter-spacing:.6px;color:var(--text-muted);text-transform:uppercase;">
            Contraseña <span id="pwd-hint" style="color:var(--orange);">(requerida para nuevo usuario)</span>
          </div>
          <div class="f-group"><label class="f-label">Contraseña</label><input class="f-input" type="password" id="usr-pwd" placeholder="Mínimo 6 caracteres" autocomplete="new-password"></div>
          <div class="f-group"><label class="f-label">Confirmar</label><input class="f-input" type="password" id="usr-pwd2" placeholder="Repetir contraseña" autocomplete="new-password"></div>
        </div>
        <input type="hidden" id="usr-id" value="0">
      </div>
      <div class="fp-footer">
        <button class="btn-outline" id="usr-cancel">Limpiar</button>
        <button class="btn-yellow" id="usr-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
          Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<script>const APP_URL = '<?= APP_URL ?>';</script>
<?php include ROOT . '/app/Views/partials/footer.php'; ?>
