  </main><!-- /page-content -->
</div><!-- /main -->

<!-- Toast global -->
<div id="toast" class="toast"></div>

<!-- JS global -->
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
<?php if (isset($extraJs)): foreach($extraJs as $js): ?>
<script src="<?= APP_URL ?>/assets/js/modules/<?= $js ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
