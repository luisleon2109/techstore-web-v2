<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión — TechStore POS</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/main.css">
<style>
body{background:var(--blue);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}
.login-card{background:white;border-radius:20px;padding:40px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.3);}
.login-logo{display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:28px;}
.login-logo-mark{width:48px;height:48px;background:var(--yellow);border-radius:12px;display:flex;align-items:center;justify-content:center;}
.login-logo-mark svg{width:26px;height:26px;}
.login-title{font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--blue);}
.login-title span{color:var(--yellow-dark);}
.login-sub{text-align:center;font-size:13px;color:var(--text-muted);margin-bottom:28px;}
.form-group{margin-bottom:16px;}
.form-label{display:block;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;font-family:'Syne',sans-serif;}
.form-input{width:100%;height:44px;border:1.5px solid var(--border);border-radius:10px;padding:0 14px;font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text);outline:none;transition:all .2s;background:var(--bg);}
.form-input:focus{border-color:var(--blue-light);background:white;box-shadow:0 0 0 3px rgba(46,68,168,.08);}
.login-btn{width:100%;height:48px;background:var(--yellow);border:none;border-radius:12px;font-family:'Syne',sans-serif;font-size:15px;font-weight:800;color:var(--blue);cursor:pointer;transition:all .2s;box-shadow:0 4px 14px rgba(245,200,0,.4);margin-top:8px;}
.login-btn:hover{background:var(--yellow-h);transform:translateY(-1px);}
.error-msg{background:#FFF0F0;color:#E5484D;border:1px solid #FFCDD2;border-radius:9px;padding:10px 14px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.hints{margin-top:20px;background:var(--bg);border-radius:10px;padding:14px;font-size:12px;color:var(--text-muted);}
.hints strong{display:block;margin-bottom:6px;color:var(--text);font-family:'Syne',sans-serif;}
.hints p{margin:2px 0;}
</style>
</head>
<body>
<div class="login-card">
  <div class="login-logo">
    <div class="login-logo-mark">
      <svg viewBox="0 0 24 24" fill="none" stroke="#1A2B5F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
    </div>
    <div class="login-title">Tech<span>Store</span> POS</div>
  </div>
  <p class="login-sub">Ingresa tus credenciales para continuar</p>

  <?php if (!empty($error)): ?>
  <div class="error-msg">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
    <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="index.php?r=login/login">
    <div class="form-group">
      <label class="form-label">Correo Electrónico</label>
      <input class="form-input" type="email" name="email" placeholder="usuario@techstore.bo"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
    </div>
    <div class="form-group">
      <label class="form-label">Contraseña</label>
      <input class="form-input" type="password" name="password" placeholder="••••••••" required>
    </div>
    <button type="submit" class="login-btn">Iniciar Sesión →</button>
  </form>

  <div class="hints">
    <strong>Credenciales actualizadas:</strong>
    <p>👑 Admin: admin@techstore.bo / <strong>admin123</strong></p>
    <p>💰 Cajero: cajero@techstore.bo / <strong>password</strong></p>
    <p>📦 Almacén: almacen@techstore.bo / <strong>password</strong></p>
  </div>
</div>
</body>
</html>