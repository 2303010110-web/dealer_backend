<?php
require_once '../includes/config.php';

// Redirect jika sudah login
if (isAdminLoggedIn()) redirect('/mitsubishi/admin/dashboard.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = md5($_POST['password'] ?? '');

    if (!$username || !$_POST['password']) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['nama_lengkap'];
            $_SESSION['admin_user'] = $admin['username'];
            redirect('/mitsubishi/admin/dashboard.php');
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Admin Login | Dealer Mitsubishi</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Poppins',sans-serif;min-height:100vh;background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);display:flex;align-items:center;justify-content:center;padding:20px;position:relative;overflow:hidden}
    body::before{content:'';position:absolute;width:700px;height:700px;background:radial-gradient(rgba(220,38,38,.15),transparent 70%);top:-200px;right:-200px;pointer-events:none}
    body::after{content:'';position:absolute;width:500px;height:500px;background:radial-gradient(rgba(99,102,241,.1),transparent 70%);bottom:-150px;left:-150px;pointer-events:none}
    .login-wrap{width:100%;max-width:440px;position:relative;z-index:2}
    .login-card{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);border-radius:28px;padding:46px 40px;box-shadow:0 25px 60px rgba(0,0,0,.4)}
    .login-logo{text-align:center;margin-bottom:36px}
    .login-logo-icon{width:72px;height:72px;background:linear-gradient(135deg,#dc2626,#ef4444);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:28px;color:white;margin:0 auto 16px;box-shadow:0 10px 28px rgba(220,38,38,.4)}
    .login-logo h1{font-size:22px;color:white;font-weight:700}
    .login-logo p{font-size:13px;color:rgba(255,255,255,.5);margin-top:4px}
    .login-title{text-align:center;margin-bottom:30px}
    .login-title h2{font-size:20px;color:white;font-weight:600;margin-bottom:6px}
    .login-title p{font-size:13px;color:rgba(255,255,255,.45)}
    .form-group{margin-bottom:20px}
    .form-group label{display:block;font-size:13px;color:rgba(255,255,255,.7);margin-bottom:8px;font-weight:500}
    .input-wrap{position:relative}
    .input-wrap i{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.4);font-size:16px}
    .input-wrap input{width:100%;padding:13px 16px 13px 46px;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.1);border-radius:14px;color:white;font-size:14px;font-family:'Poppins',sans-serif;transition:.3s;outline:none}
    .input-wrap input::placeholder{color:rgba(255,255,255,.3)}
    .input-wrap input:focus{border-color:rgba(220,38,38,.6);background:rgba(255,255,255,.11);box-shadow:0 0 0 3px rgba(220,38,38,.15)}
    .eye-toggle{position:absolute;right:16px;top:50%;transform:translateY(-50%);cursor:pointer;color:rgba(255,255,255,.4);font-size:15px;background:none;border:none;padding:0}
    .alert-error{background:rgba(220,38,38,.12);border:1px solid rgba(220,38,38,.3);color:#fca5a5;padding:13px 18px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:10px;margin-bottom:20px}
    .login-btn{width:100%;padding:15px;background:linear-gradient(135deg,#dc2626,#ef4444);color:white;border:none;border-radius:14px;font-size:15px;font-weight:700;cursor:pointer;transition:.3s;font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;gap:10px;margin-top:8px}
    .login-btn:hover{opacity:.9;transform:translateY(-2px);box-shadow:0 10px 24px rgba(220,38,38,.35)}
    .login-footer{text-align:center;margin-top:28px;font-size:12px;color:rgba(255,255,255,.25)}
    .login-footer a{color:rgba(220,38,38,.6);text-decoration:none}
    .particles{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;overflow:hidden}
    .particle{position:absolute;width:4px;height:4px;background:rgba(220,38,38,.3);border-radius:50%;animation:float linear infinite}
    @keyframes float{0%{transform:translateY(100vh) rotate(0);opacity:0}10%{opacity:1}90%{opacity:1}100%{transform:translateY(-100px) rotate(720deg);opacity:0}}
<<<<<<< HEAD

    /* Info akun default */
    .info-akun{background:rgba(99,102,241,.12);border:1px solid rgba(99,102,241,.3);border-radius:14px;padding:14px 18px;margin-bottom:22px}
    .info-akun-title{font-size:12px;color:rgba(165,180,252,.8);font-weight:600;margin-bottom:8px;display:flex;align-items:center;gap:6px}
    .info-akun-row{display:flex;align-items:center;gap:8px;margin-bottom:4px}
    .info-akun-row:last-child{margin-bottom:0}
    .info-akun-label{font-size:11px;color:rgba(255,255,255,.4);width:70px;flex-shrink:0}
    .info-akun-val{font-size:12px;color:rgba(255,255,255,.85);font-family:monospace;background:rgba(255,255,255,.07);padding:3px 10px;border-radius:6px;letter-spacing:.5px;font-weight:600}
    .info-akun-copy{background:none;border:none;color:rgba(165,180,252,.6);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;transition:.2s}
    .info-akun-copy:hover{color:#a5b4fc;background:rgba(99,102,241,.2)}
=======
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
  </style>
</head>
<body>
<div class="particles" id="particles"></div>

<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-icon"><i class="fa-solid fa-car-side"></i></div>
      <h1>Dealer Mitsubishi</h1>
      <p>Panel Administrasi</p>
    </div>

    <div class="login-title">
      <h2>Selamat Datang</h2>
      <p>Masuk ke panel admin untuk mengelola sistem</p>
    </div>

    <?php if ($error): ?>
    <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <div class="input-wrap">
          <i class="fa-solid fa-user"></i>
          <input type="text" name="username" placeholder="Masukkan username" required autocomplete="username" value="<?= sanitize($_POST['username'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="input-wrap">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" id="passwordInput" placeholder="Masukkan password" required autocomplete="current-password">
          <button type="button" class="eye-toggle" onclick="togglePwd()"><i class="fa-solid fa-eye" id="eyeIcon"></i></button>
        </div>
      </div>
      <button type="submit" class="login-btn"><i class="fa-solid fa-right-to-bracket"></i> Masuk ke Panel Admin</button>
    </form>

    <div class="login-footer">
      <p>© <?= date('Y') ?> Dealer Mitsubishi — Panel Admin</p>
      <p style="margin-top:6px"><a href="/mitsubishi/index.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke Website</a></p>
    </div>
  </div>
</div>

<script>
function togglePwd() {
  const inp  = document.getElementById('passwordInput');
  const icon = document.getElementById('eyeIcon');
  if (inp.type === 'password') { inp.type = 'text'; icon.className = 'fa-solid fa-eye-slash'; }
  else { inp.type = 'password'; icon.className = 'fa-solid fa-eye'; }
}

<<<<<<< HEAD
function copyText(text, btnId) {
  navigator.clipboard.writeText(text).then(() => {
    const btn = document.getElementById(btnId);
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-check"></i>';
    btn.style.color = '#86efac';
    setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; }, 1500);
  }).catch(() => {
    // Fallback
    const el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
  });
}

=======
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
// Floating particles
const container = document.getElementById('particles');
for (let i = 0; i < 18; i++) {
  const p = document.createElement('div');
  p.className = 'particle';
  p.style.cssText = `left:${Math.random()*100}%;width:${Math.random()*6+2}px;height:${Math.random()*6+2}px;animation-duration:${Math.random()*12+8}s;animation-delay:${Math.random()*8}s;opacity:${Math.random()*.5+.1}`;
  container.appendChild(p);
}
</script>
</body>
</html>
