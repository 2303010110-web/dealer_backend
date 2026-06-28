<?php
require_once '../includes/config.php';
requireAdmin();

$pageTitle    = 'Ubah Password';
$pageSubtitle = 'Perbarui kredensial login admin';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passLama = $_POST['password_lama'] ?? '';
    $passBaru = $_POST['password_baru'] ?? '';
    $passKonf = $_POST['password_konfirmasi'] ?? '';

    $adminId = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if (md5($passLama) !== $admin['password']) {
        $error = 'Password lama tidak sesuai.';
    } elseif (strlen($passBaru) < 6) {
        $error = 'Password baru minimal 6 karakter.';
    } elseif ($passBaru !== $passKonf) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $hashBaru = md5($passBaru);
        $upd = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $upd->bind_param('si', $hashBaru, $adminId);
        if ($upd->execute()) {
            $success = 'Password berhasil diperbarui. Gunakan password baru saat login berikutnya.';
        } else {
            $error = 'Terjadi kesalahan sistem.';
        }
    }
}

$adminData = $conn->query("SELECT * FROM admin WHERE id=" . (int)$_SESSION['admin_id'])->fetch_assoc();
?>
<?php include 'includes/header.php'; ?>

<?php if ($success): ?><div class="alert-a alert-success-a"><i class="fa-solid fa-circle-check"></i><?=$success?></div><?php endif; ?>
<?php if ($error): ?><div class="alert-a alert-error-a"><i class="fa-solid fa-circle-exclamation"></i><?=$error?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;max-width:900px">
  <div class="form-card">
    <h2 style="font-size:16px;color:var(--dark);margin-bottom:20px;display:flex;align-items:center;gap:10px">
      <i class="fa-solid fa-key" style="color:var(--primary)"></i> Ubah Password
    </h2>
    <form method="POST">
      <div class="form-group-a">
        <label>Password Lama</label>
        <input type="password" name="password_lama" required autocomplete="current-password">
      </div>
      <div class="form-group-a">
        <label>Password Baru</label>
        <input type="password" name="password_baru" required minlength="6" autocomplete="new-password">
      </div>
      <div class="form-group-a">
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="password_konfirmasi" required minlength="6" autocomplete="new-password">
      </div>
      <button type="submit" class="btn-admin btn-primary-a"><i class="fa-solid fa-save"></i> Perbarui Password</button>
    </form>
    <p style="font-size:12px;color:var(--gray);margin-top:14px">
      <i class="fa-solid fa-circle-info"></i> Password disimpan menggunakan hash MD5 di database.
    </p>
  </div>

  <div class="form-card">
    <h2 style="font-size:16px;color:var(--dark);margin-bottom:20px;display:flex;align-items:center;gap:10px">
      <i class="fa-solid fa-id-badge" style="color:var(--primary)"></i> Info Akun
    </h2>
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:22px">
      <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,#dc2626,#ef4444);display:flex;align-items:center;justify-content:center;color:white;font-size:24px;font-weight:700"><?=strtoupper(substr($adminData['nama_lengkap'],0,1))?></div>
      <div>
        <div style="font-weight:700;font-size:15px"><?=sanitize($adminData['nama_lengkap'])?></div>
        <div style="font-size:13px;color:var(--gray)">@<?=sanitize($adminData['username'])?></div>
      </div>
    </div>
    <?php foreach ([
      ['fa-user','Username',$adminData['username']],
      ['fa-envelope','Email',$adminData['email']?:'-'],
      ['fa-calendar','Bergabung',date('d M Y',strtotime($adminData['created_at']))],
    ] as [$ic,$lbl,$val]): ?>
    <div style="display:flex;gap:14px;padding:11px 0;border-bottom:1px solid var(--lighter)">
      <div style="width:34px;height:34px;background:rgba(220,38,38,.1);border-radius:9px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:13px;flex-shrink:0"><i class="fa-solid <?=$ic?>"></i></div>
      <div><div style="font-size:11px;color:var(--gray)"><?=$lbl?></div><div style="font-size:14px;font-weight:500"><?=sanitize((string)$val)?></div></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
