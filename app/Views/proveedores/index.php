<?php
// app/Views/proveedores/index.php
$pageTitle = 'Proveedores';
$activeNav = 'proveedores';
$extraJs   = ['proveedores.js'];
include ROOT . '/app/Views/partials/header.php';
?>
<div class="module-wrap">
  <div class="module-header">
    <div><h1 class="module-title">Proveedores</h1><p class="module-sub">Gestión de proveedores y condiciones comerciales</p></div>
    <div class="hdr-actions">
      <button class="btn-yellow" id="new-prov-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        Nuevo Proveedor
      </button>
    </div>
  </div>

  <div class="module-body two-col">
    <div class="table-card">
      <div class="table-toolbar">
        <div class="tbl-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" id="prov-search" placeholder="Buscar proveedor...">
        </div>
      </div>
      <div class="table-scroll">
        <table class="data-table">
          <thead><tr><th>Proveedor</th><th>NIT</th><th>Teléfono</th><th>País</th><th>Cond. Pago</th><th>Compras</th><th>Total</th><th></th></tr></thead>
          <tbody id="prov-tbody">
          <?php foreach($proveedores as $p): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($p['Nombre']) ?></strong>
              <?php if($p['Email']): ?><br><small style="color:var(--text-muted);font-size:10px;"><?= htmlspecialchars($p['Email']) ?></small><?php endif; ?>
            </td>
            <td><span class="code-pill"><?= htmlspecialchars($p['NIT'] ?? '—') ?></span></td>
            <td style="color:var(--text-muted);"><?= htmlspecialchars($p['Telefono'] ?? '—') ?></td>
            <td style="color:var(--text-muted);"><?= htmlspecialchars($p['pais_nombre'] ?? '—') ?></td>
            <td><span class="badge blue"><?= htmlspecialchars($p['Condicion_Pago'] ?? '—') ?></span></td>
            <td style="color:var(--text-muted);"><?= $p['total_compras'] ?></td>
            <td><strong style="color:var(--blue);font-family:'Syne',sans-serif;font-size:12px;">Bs <?= number_format($p['monto_total'],2) ?></strong></td>
            <td>
              <div class="act-btns">
                <button class="act-b edit-prov"
                  data-id="<?= $p['ID_Proveedor'] ?>"
                  data-nombre="<?= htmlspecialchars($p['Nombre']) ?>"
                  data-nit="<?= htmlspecialchars($p['NIT']??'') ?>"
                  data-tel="<?= htmlspecialchars($p['Telefono']??'') ?>"
                  data-email="<?= htmlspecialchars($p['Email']??'') ?>"
                  data-dir="<?= htmlspecialchars($p['Direccion']??'') ?>"
                  data-pais="<?= $p['ID_Pais']??'' ?>"
                  data-cond="<?= htmlspecialchars($p['Condicion_Pago']??'') ?>"
                  title="Editar">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button class="act-b act-del del-prov" data-id="<?= $p['ID_Proveedor'] ?>" title="Eliminar">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($proveedores)): ?>
          <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:30px;">Sin proveedores registrados</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Form panel -->
    <div class="form-panel" id="prov-panel">
      <div class="fp-header">
        <h3 id="prov-fp-title">Nuevo Proveedor</h3>
        <span class="fp-badge new" id="prov-fp-badge">NUEVO</span>
      </div>
      <div class="fp-body">
        <div class="f-group"><label class="f-label">Nombre / Razón Social *</label><input class="f-input" type="text" id="prov-nombre" placeholder="TechDistrib Bolivia S.R.L."></div>
        <div class="f-row">
          <div class="f-group"><label class="f-label">NIT</label><input class="f-input" type="text" id="prov-nit" placeholder="1234567890"></div>
          <div class="f-group"><label class="f-label">Teléfono</label><input class="f-input" type="text" id="prov-tel" placeholder="+591 3..."></div>
        </div>
        <div class="f-group"><label class="f-label">Email</label><input class="f-input" type="email" id="prov-email" placeholder="ventas@proveedor.com"></div>
        <div class="f-group"><label class="f-label">Dirección</label><textarea class="f-textarea" id="prov-dir" placeholder="Dirección..." style="min-height:52px;"></textarea></div>
        <div class="f-row">
          <div class="f-group">
            <label class="f-label">País</label>
            <select class="f-select" id="prov-pais">
              <option value="">Seleccionar...</option>
              <?php foreach($paises as $pa): ?>
              <option value="<?= $pa['ID_Pais'] ?>"><?= htmlspecialchars($pa['Nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="f-group"><label class="f-label">Cond. Pago</label><input class="f-input" type="text" id="prov-cond" placeholder="Contado, 30 días..."></div>
        </div>

        <!-- Historial compras -->
        <div class="hist-block">
          <div class="hist-title">Últimas Órdenes de Compra</div>
          <div id="prov-hist"><p style="font-size:11px;color:var(--text-muted);text-align:center;padding:8px 0;">Seleccioná un proveedor para ver su historial</p></div>
        </div>
        <input type="hidden" id="prov-id" value="0">
      </div>
      <div class="fp-footer">
        <button class="btn-outline" id="prov-cancel">Limpiar</button>
        <button class="btn-yellow" id="prov-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
          Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<script>const APP_URL = '<?= APP_URL ?>';</script>
<?php include ROOT . '/app/Views/partials/footer.php'; ?>
