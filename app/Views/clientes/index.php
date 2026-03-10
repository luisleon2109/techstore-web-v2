<?php
$pageTitle = 'Gestión de Clientes';
$activeNav = 'clientes';
$extraJs   = ['clientes.js'];
include ROOT . '/app/Views/partials/header.php';
?>
<div class="module-wrap">

  <div class="module-header">
    <div>
      <h1 class="module-title">Clientes</h1>
      <p class="module-sub">Gestión de clientes y ficha de historial</p>
    </div>
    <div class="hdr-actions">
      <button class="btn-yellow" id="new-client-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        Nuevo Cliente
      </button>
    </div>
  </div>

  <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
    <?php
    $total    = count($clientes);
    $con_ventas = count(array_filter($clientes, fn($c)=>$c['total_ventas']>0));
    ?>
    <div class="stat-card"><div class="sc-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div class="sc-info"><div class="sc-val"><?= $total ?></div><div class="sc-lbl">Total Clientes</div></div></div>
    <div class="stat-card"><div class="sc-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></div><div class="sc-info"><div class="sc-val"><?= $con_ventas ?></div><div class="sc-lbl">Con Compras</div></div></div>
    </div>

  <div class="module-body two-col">

    <div class="table-card">
      <div class="table-toolbar">
        <div class="tbl-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" id="cli-search" placeholder="Buscar por nombre, CI, teléfono...">
        </div>
      </div>
      <div class="table-scroll">
        <table class="data-table" id="cli-table">
          <thead><tr><th>Nombre</th><th>CI</th><th>Teléfono</th><th>Ventas</th><th>Facturado</th><th></th></tr></thead>
          <tbody id="cli-tbody">
          <?php foreach($clientes as $c): ?>
          <tr data-id="<?= $c['ID_Cliente'] ?>" class="cli-row">
            <td><strong><?= htmlspecialchars($c['Nombre'].' '.$c['Apellido']) ?></strong><br><small style="color:var(--text-muted);"><?= htmlspecialchars($c['Email']??'') ?></small></td>
            <td><span class="code-pill"><?= htmlspecialchars($c['CI']??'—') ?></span></td>
            <td style="color:var(--text-muted);"><?= htmlspecialchars($c['Telefono']??'—') ?></td>
            <td><span class="badge blue"><?= $c['total_ventas'] ?> ventas</span></td>
            <td><strong>Bs <?= number_format($c['monto_total'],2) ?></strong></td>
            <td>
              <div class="act-btns">
                <button class="act-b edit-cli" data-id="<?= $c['ID_Cliente'] ?>" title="Editar">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button class="act-b act-del del-cli" data-id="<?= $c['ID_Cliente'] ?>" title="Eliminar">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="form-panel" id="cli-form-panel">
      <div class="fp-header">
        <h3 id="fp-title">Nuevo Cliente</h3>
        <span class="fp-badge new" id="fp-badge">NUEVO</span>
      </div>
      <div class="fp-body">
        <form id="cli-form">
          <input type="hidden" id="cli-id" name="id" value="0">
          <div class="fp-avatar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg></div>
          <div class="f-row">
            <div class="f-group"><label class="f-label">Nombre *</label><input class="f-input" type="text" id="cli-nombre" name="nombre" placeholder="Nombre" required></div>
            <div class="f-group"><label class="f-label">Apellido *</label><input class="f-input" type="text" id="cli-apellido" name="apellido" placeholder="Apellido" required></div>
          </div>
          <div class="f-group"><label class="f-label">CI (Carnet de Identidad)</label><input class="f-input" type="text" id="cli-ci" name="ci" placeholder="1234567 LP"></div>
          <div class="f-row">
            <div class="f-group"><label class="f-label">Teléfono</label><input class="f-input" type="text" id="cli-tel" name="telefono" placeholder="+591 7..."></div>
            <div class="f-group"><label class="f-label">Email</label><input class="f-input" type="email" id="cli-email" name="email" placeholder="correo@..."></div>
          </div>
          <div class="f-group"><label class="f-label">NIT (para facturación)</label><input class="f-input" type="text" id="cli-nit" name="nit" placeholder="NIT o CI del cliente"></div>
          <div class="f-group"><label class="f-label">Razón Social</label><input class="f-input" type="text" id="cli-razon" name="razon" placeholder="Empresa o nombre para factura"></div>

          <div class="hist-block">
            <div class="hist-title">Historial de Compras</div>
            <div id="cli-history"><p style="font-size:12px;color:var(--text-muted);text-align:center;padding:10px;">Selecciona un cliente para ver su historial</p></div>
          </div>
        </form>
      </div>
      <div class="fp-footer">
        <button class="btn-outline" id="cli-cancel">Limpiar</button>
        <button class="btn-yellow" id="cli-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Guardar Cliente
        </button>
      </div>
    </div>
  </div>

</div>

<script>
// Datos embebidos para JS
const CLIENTES_DATA = <?= json_encode($clientes, JSON_UNESCAPED_UNICODE) ?>;
const APP_URL = '<?= APP_URL ?>';
</script>
<?php include ROOT . '/app/Views/partials/footer.php'; ?>