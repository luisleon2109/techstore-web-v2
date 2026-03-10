<?php
$pageTitle = 'Productos';
$activeNav = 'productos';
$extraJs   = ['productos.js'];
include ROOT . '/app/Views/partials/header.php';
?>
<div class="module-wrap">

  <div class="module-header">
    <div><h1 class="module-title">Productos</h1><p class="module-sub">Registro y edición de productos</p></div>
    <div class="hdr-actions">
      <button class="btn-yellow" id="new-prod-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        Nuevo Producto
      </button>
    </div>
  </div>

  <div class="module-body two-col">

    <!-- Tabla -->
    <div class="table-card">
      <div class="table-toolbar">
        <div class="tbl-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" id="prod-search" placeholder="Buscar por nombre o SKU...">
        </div>
      </div>
      <div class="table-scroll">
        <table class="data-table">
          <thead><tr><th>SKU</th><th>Producto</th><th>Tipo</th><th>Marca</th><th>Stock</th><th>Precio Venta</th><th>Estado</th><th></th></tr></thead>
          <tbody id="prod-tbody">
          <?php foreach($productos as $p):
            $sc = $p['Stock_Actual']==0?'red':($p['Stock_Actual']<=$p['Stock_Minimo']?'orange':'green');
            $sl = $p['Stock_Actual']==0?'Agotado':($p['Stock_Actual']<=$p['Stock_Minimo']?'Stock Bajo':'En Stock');
          ?>
          <tr class="prod-row" data-id="<?= $p['ID_Producto'] ?>">
            <td><span class="code-pill"><?= htmlspecialchars($p['SKU'] ?? '—') ?></span></td>
            <td><strong><?= htmlspecialchars($p['Nombre']) ?></strong><br><small style="color:var(--text-muted);"><?= htmlspecialchars($p['modelo']) ?></small></td>
            <td style="color:var(--text-muted);"><?= htmlspecialchars($p['tipo']) ?></td>
            <td style="color:var(--text-muted);"><?= htmlspecialchars($p['marca']) ?></td>
            <td><strong style="color:<?= $p['Stock_Actual']==0?'var(--red)':($p['Stock_Actual']<=$p['Stock_Minimo']?'var(--orange)':'var(--green)') ?>;"><?= $p['Stock_Actual'] ?> uds.</strong></td>
            <td><strong style="color:var(--blue);font-family:'Syne',sans-serif;">Bs <?= number_format($p['Precio_Venta'],2) ?></strong></td>
            <td><span class="badge <?= $sc ?>"><span class="bd"></span><?= $sl ?></span></td>
            <td>
              <div class="act-btns">
                <button class="act-b edit-prod" data-id="<?= $p['ID_Producto'] ?>" title="Editar">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button class="act-b act-del del-prod" data-id="<?= $p['ID_Producto'] ?>" title="Eliminar">
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

    <!-- Formulario -->
    <div class="form-panel" id="prod-form-panel">
      <div class="fp-header">
        <h3 id="prod-fp-title">Nuevo Producto</h3>
        <span class="fp-badge new" id="prod-fp-badge">NUEVO</span>
      </div>
      <div class="fp-body">
        <form id="prod-form">
          <input type="hidden" id="prod-id" name="id" value="0">

          <div class="f-row">
            <div class="f-group"><label class="f-label">SKU</label><input class="f-input" type="text" id="prod-sku" name="sku" placeholder="IPH-15PM"></div>
            <div class="f-group"><label class="f-label">Código de Barras</label><input class="f-input" type="text" id="prod-barras" name="codigo_barras" placeholder="0194253716471"></div>
          </div>

          <div class="f-group"><label class="f-label">Nombre del Producto *</label><input class="f-input" type="text" id="prod-nombre" name="nombre" placeholder="Nombre completo" required></div>

          <div class="f-group"><label class="f-label">Descripción</label><textarea class="f-textarea" id="prod-desc" name="descripcion" placeholder="Descripción opcional..." style="min-height:56px;"></textarea></div>

          <div class="f-row">
            <div class="f-group">
              <label class="f-label">Tipo de Producto *</label>
              <select class="f-select" id="prod-tipo" name="tipo_id" required>
                <option value="">Seleccionar...</option>
                <?php foreach($tipos as $t): ?>
                <option value="<?= $t['ID_Tipo'] ?>"><?= htmlspecialchars($t['Nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Modelo *</label>
              <select class="f-select" id="prod-modelo" name="modelo_id" required>
                <option value="">Seleccionar...</option>
                <?php foreach($modelos as $m): ?>
                <option value="<?= $m['ID_Modelo'] ?>"><?= htmlspecialchars($m['Nombre']) ?> (<?= htmlspecialchars($m['marca']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="f-row">
            <div class="f-group"><label class="f-label">Precio Costo (Bs)</label><input class="f-input" type="number" id="prod-costo" name="costo" placeholder="0.00" min="0" step="0.01"></div>
            <div class="f-group"><label class="f-label">Precio Venta (Bs) *</label><input class="f-input" type="number" id="prod-precio" name="precio" placeholder="0.00" min="0" step="0.01" required></div>
          </div>

          <div class="f-row">
            <div class="f-group"><label class="f-label">Voltaje</label><input class="f-input" type="text" id="prod-voltaje" name="voltaje" placeholder="110-220V"></div>
            <div class="f-group"><label class="f-label">Potencia (Watts)</label><input class="f-input" type="text" id="prod-potencia" name="potencia" placeholder="25W"></div>
          </div>

          <div class="f-row">
            <div class="f-group"><label class="f-label">Garantía (meses)</label><input class="f-input" type="number" id="prod-garantia" name="garantia" placeholder="12" value="12" min="0"></div>
            <div class="f-group"><label class="f-label">Stock Mínimo</label><input class="f-input" type="number" id="prod-stockmin" name="stock_minimo" placeholder="5" value="5" min="0"></div>
          </div>

          <!-- Solo visible en modo NUEVO -->
          <div id="stock-inicial-row">
            <div class="f-group"><label class="f-label">Stock Inicial</label><input class="f-input" type="number" id="prod-stockini" name="stock_inicial" placeholder="0" value="0" min="0"></div>
          </div>

        </form>
      </div>
      <div class="fp-footer">
        <button class="btn-outline" id="prod-cancel">Limpiar</button>
        <button class="btn-yellow" id="prod-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Guardar Producto
        </button>
      </div>
    </div>

  </div>
</div>

<script>
const PROD_DATA   = <?= json_encode($productos, JSON_UNESCAPED_UNICODE) ?>;
const APP_URL     = '<?= APP_URL ?>';
</script>
<?php include ROOT . '/app/Views/partials/footer.php'; ?>
