<?php
require_once '../includes/config.php';
requireAdmin();

$pageTitle    = 'Pengaturan Website';
$pageSubtitle = 'Kelola informasi dan konfigurasi dealer';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['nama_dealer','alamat','telepon','email','jam_operasional','whatsapp','tagline','google_maps'];
    foreach ($keys as $k) {
        $val = sanitize($_POST[$k] ?? '');
        $stmt = $conn->prepare("INSERT INTO pengaturan (kunci,nilai) VALUES (?,?) ON DUPLICATE KEY UPDATE nilai=?");
        $stmt->bind_param('sss', $k, $val, $val);
        $stmt->execute();
    }
    $success = 'Pengaturan berhasil disimpan.';
}

// Ambil semua pengaturan
$settingsRaw = $conn->query("SELECT kunci, nilai FROM pengaturan");
$settings    = [];
while ($s = $settingsRaw->fetch_assoc()) $settings[$s['kunci']] = $s['nilai'];
?>
<?php include 'includes/header.php'; ?>

<?php if ($success): ?><div class="alert-a alert-success-a"><i class="fa-solid fa-circle-check"></i><?=$success?></div><?php endif; ?>
<?php if ($error): ?><div class="alert-a alert-error-a"><i class="fa-solid fa-circle-exclamation"></i><?=$error?></div><?php endif; ?>

<div class="form-card" style="max-width:720px">
  <h2 style="font-size:17px;color:var(--dark);margin-bottom:24px;display:flex;align-items:center;gap:10px">
    <i class="fa-solid fa-gear" style="color:var(--primary)"></i> Informasi Dealer
  </h2>
  <form method="POST">
    <div class="form-group-a">
      <label><i class="fa-solid fa-store"></i> Nama Dealer</label>
      <input type="text" name="nama_dealer" value="<?=sanitize($settings['nama_dealer']??'')?>">
    </div>
    <div class="form-group-a">
      <label><i class="fa-solid fa-quote-left"></i> Tagline / Slogan</label>
      <input type="text" name="tagline" value="<?=sanitize($settings['tagline']??'')?>">
    </div>
    <div class="form-group-a">
      <label><i class="fa-solid fa-location-dot"></i> Alamat Lengkap</label>
      <textarea name="alamat" rows="2"><?=sanitize($settings['alamat']??'')?></textarea>
    </div>
    <div class="form-row-a">
      <div class="form-group-a">
        <label><i class="fa-solid fa-phone"></i> No. Telepon</label>
        <input type="text" name="telepon" value="<?=sanitize($settings['telepon']??'')?>">
      </div>
      <div class="form-group-a">
        <label><i class="fa-brands fa-whatsapp"></i> No. WhatsApp (tanpa +)</label>
        <input type="text" name="whatsapp" placeholder="628xxxxxxxxxx" value="<?=sanitize($settings['whatsapp']??'')?>">
      </div>
    </div>
    <div class="form-group-a">
      <label><i class="fa-solid fa-envelope"></i> Email</label>
      <input type="email" name="email" value="<?=sanitize($settings['email']??'')?>">
    </div>
    <div class="form-group-a">
      <label><i class="fa-solid fa-clock"></i> Jam Operasional</label>
      <input type="text" name="jam_operasional" placeholder="Senin – Sabtu, 08:00 – 20:00 WIB" value="<?=sanitize($settings['jam_operasional']??'')?>">
    </div>
    <div class="form-group-a">
      <label><i class="fa-solid fa-map"></i> URL Google Maps (embed)</label>
      <input type="url" name="google_maps" placeholder="https://maps.google.com/..." value="<?=sanitize($settings['google_maps']??'')?>">
    </div>
    <button type="submit" class="btn-admin btn-primary-a"><i class="fa-solid fa-save"></i> Simpan Pengaturan</button>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
