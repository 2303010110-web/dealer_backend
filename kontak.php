<?php
require_once 'includes/config.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = sanitize($_POST['nama'] ?? '');
    $email  = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $no_hp  = sanitize($_POST['no_hp'] ?? '');
    $subjek = sanitize($_POST['subjek'] ?? '');
    $pesan  = sanitize($_POST['pesan'] ?? '');

    if (!$nama || !$email || !$pesan) {
        $error = 'Nama, email, dan pesan wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $ins = $conn->prepare("INSERT INTO kontak (nama, email, no_hp, subjek, pesan) VALUES (?,?,?,?,?)");
        $ins->bind_param('sssss', $nama, $email, $no_hp, $subjek, $pesan);
        if ($ins->execute()) {
            $success = 'Pesan Anda berhasil terkirim! Tim kami akan membalas dalam 1x24 jam kerja.';
        } else {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}

$alamat   = getSetting($conn, 'alamat');
$telepon  = getSetting($conn, 'telepon');
$emailSet = getSetting($conn, 'email');
$jam      = getSetting($conn, 'jam_operasional');
$wa       = getSetting($conn, 'whatsapp', WA_NUMBER);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kontak Kami | Dealer Mitsubishi</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/footer.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <style>
    .page-header{background:linear-gradient(135deg,#111827,#1f2937);padding:110px 0 56px;color:white;position:relative;overflow:hidden}
    .page-header::before{content:'';position:absolute;width:500px;height:500px;background:radial-gradient(rgba(220,38,38,.22),transparent 70%);top:-150px;right:-100px}
    .kontak-layout{display:grid;grid-template-columns:1fr 420px;gap:36px;align-items:start;margin-top:40px}
    .kontak-form-card{background:white;border-radius:28px;padding:40px;box-shadow:var(--shadow)}
    .kontak-form-card h2{font-size:22px;color:var(--dark);margin-bottom:28px;padding-bottom:16px;border-bottom:2px solid var(--lighter);display:flex;align-items:center;gap:12px}
    .kontak-form-card h2 i{color:var(--primary)}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .form-group{margin-bottom:20px}
    .form-group label{font-size:13px;font-weight:600;color:var(--dark);display:block;margin-bottom:8px}
    .form-group input,.form-group select,.form-group textarea{width:100%;padding:13px 16px;border:1.5px solid #e5e7eb;border-radius:14px;font-size:14px;font-family:'Poppins',sans-serif;color:var(--dark);background:#f9fafb;transition:.3s;box-sizing:border-box}
    .form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:var(--primary);background:white;box-shadow:0 0 0 3px rgba(220,38,38,.1)}
    .form-group textarea{resize:vertical;min-height:130px}
    .submit-btn{width:100%;padding:16px;background:linear-gradient(135deg,#dc2626,#ef4444);color:white;border:none;border-radius:16px;font-size:16px;font-weight:700;cursor:pointer;transition:.3s;font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;gap:10px;margin-top:6px}
    .submit-btn:hover{opacity:.88;transform:translateY(-2px);box-shadow:0 8px 22px rgba(220,38,38,.3)}
    .alert{padding:16px 20px;border-radius:16px;font-size:14px;margin-bottom:22px;display:flex;align-items:flex-start;gap:12px}
    .alert-error{background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);color:#dc2626}
    .alert-success{background:rgba(22,163,74,.08);border:1px solid rgba(22,163,74,.2);color:#166534}
    .kontak-sidebar{display:flex;flex-direction:column;gap:20px;position:sticky;top:100px}
    .info-card{background:white;border-radius:24px;padding:28px;box-shadow:var(--shadow)}
    .info-card h3{font-size:16px;color:var(--dark);margin-bottom:20px;display:flex;align-items:center;gap:10px}
    .info-card h3 i{color:var(--primary)}
    .contact-info-item{display:flex;align-items:flex-start;gap:14px;padding:14px 0;border-bottom:1px solid var(--lighter)}
    .contact-info-item:last-child{border:none;padding-bottom:0}
    .ci-icon-wrap{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#dc2626,#ef4444);display:flex;align-items:center;justify-content:center;color:white;font-size:16px;flex-shrink:0}
    .ci-text label{font-size:11px;color:var(--gray);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px}
    .ci-text span{font-size:14px;color:var(--dark);font-weight:500;line-height:1.6}
    .wa-btn{display:flex;align-items:center;justify-content:center;gap:12px;padding:16px;background:linear-gradient(135deg,#16a34a,#22c55e);color:white;border-radius:16px;text-decoration:none;font-weight:700;font-size:15px;transition:.3s;box-shadow:0 6px 18px rgba(22,163,74,.25)}
    .wa-btn:hover{opacity:.9;transform:translateY(-2px);box-shadow:0 10px 24px rgba(22,163,74,.35)}
    .wa-btn i{font-size:22px}
    .map-wrap{border-radius:22px;overflow:hidden;box-shadow:var(--shadow)}
    .map-wrap iframe{width:100%;height:220px;border:none;display:block}
    .faq-section{margin-top:70px}
    .faq-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-top:32px}
    .faq-item{background:white;border-radius:20px;padding:26px;box-shadow:var(--shadow);transition:.3s;border-left:4px solid transparent}
    .faq-item:hover{border-left-color:var(--primary);transform:translateY(-3px)}
    .faq-item h4{font-size:15px;color:var(--dark);margin-bottom:10px;display:flex;align-items:center;gap:10px}
    .faq-item h4 i{color:var(--primary)}
    .faq-item p{font-size:14px;color:var(--gray);line-height:1.8}
    @media(max-width:900px){.kontak-layout{grid-template-columns:1fr}.kontak-sidebar{position:static}.faq-grid{grid-template-columns:1fr}}
    @media(max-width:600px){.form-row{grid-template-columns:1fr}}
  </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb" style="color:#9ca3af;display:flex;gap:10px;align-items:center;font-size:14px;margin-bottom:18px">
      <a href="index.php" style="color:#ef4444;text-decoration:none">Home</a>
      <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
      <span>Kontak Kami</span>
    </div>
    <h1 style="font-size:44px;font-weight:800;color:white;margin-bottom:12px">Hubungi <span style="color:#ef4444">Kami</span></h1>
    <p style="color:#9ca3af;max-width:500px">Kami siap membantu Anda menemukan Mitsubishi yang sempurna. Jangan ragu untuk menghubungi tim kami.</p>
  </div>
</div>

<section class="section" style="padding-top:44px">
  <div class="container">
    <div class="kontak-layout">

      <!-- FORM -->
      <div class="kontak-form-card animate-on-scroll">
        <h2><i class="fa-solid fa-paper-plane"></i> Kirim Pesan</h2>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="form-row">
            <div class="form-group">
              <label><i class="fa-solid fa-user"></i> Nama Lengkap *</label>
              <input type="text" name="nama" placeholder="Nama Anda" required value="<?= sanitize($_POST['nama'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-envelope"></i> Email *</label>
              <input type="email" name="email" placeholder="email@example.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label><i class="fa-solid fa-phone"></i> No. HP</label>
              <input type="tel" name="no_hp" placeholder="08xxxxxxxxxx" value="<?= sanitize($_POST['no_hp'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-tag"></i> Subjek</label>
              <select name="subjek">
                <option value="">— Pilih Subjek —</option>
                <option value="Informasi Mitsubishi" <?= ($_POST['subjek']??'')==='Informasi Mitsubishi'?'selected':'' ?>>Informasi Mitsubishi</option>
                <option value="Harga & Promo" <?= ($_POST['subjek']??'')==='Harga & Promo'?'selected':'' ?>>Harga & Promo</option>
                <option value="Kredit Mitsubishi" <?= ($_POST['subjek']??'')==='Kredit Mitsubishi'?'selected':'' ?>>Kredit Mitsubishi</option>
                <option value="Test Drive" <?= ($_POST['subjek']??'')==='Test Drive'?'selected':'' ?>>Test Drive</option>
                <option value="Servis & Spare Part" <?= ($_POST['subjek']??'')==='Servis & Spare Part'?'selected':'' ?>>Servis & Spare Part</option>
                <option value="Lainnya" <?= ($_POST['subjek']??'')==='Lainnya'?'selected':'' ?>>Lainnya</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label><i class="fa-solid fa-comment-dots"></i> Pesan *</label>
            <textarea name="pesan" placeholder="Tuliskan pertanyaan atau kebutuhan Anda tentang Mitsubishi..." required><?= sanitize($_POST['pesan'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="submit-btn">
            <i class="fa-solid fa-paper-plane"></i> Kirim Pesan
          </button>
        </form>
      </div>

      <!-- SIDEBAR -->
      <div class="kontak-sidebar">
        <div class="info-card animate-on-scroll">
          <h3><i class="fa-solid fa-circle-info"></i> Info Kontak</h3>
          <div class="contact-info-item">
            <div class="ci-icon-wrap"><i class="fa-solid fa-location-dot"></i></div>
            <div class="ci-text"><label>Alamat</label><span><?= $alamat ?></span></div>
          </div>
          <div class="contact-info-item">
            <div class="ci-icon-wrap"><i class="fa-solid fa-phone"></i></div>
            <div class="ci-text"><label>Telepon</label><span><?= $telepon ?></span></div>
          </div>
          <div class="contact-info-item">
            <div class="ci-icon-wrap"><i class="fa-solid fa-envelope"></i></div>
            <div class="ci-text"><label>Email</label><span><?= $emailSet ?></span></div>
          </div>
          <div class="contact-info-item">
            <div class="ci-icon-wrap"><i class="fa-solid fa-clock"></i></div>
            <div class="ci-text"><label>Jam Operasional</label><span><?= $jam ?></span></div>
          </div>
        </div>

        <a href="https://wa.me/<?= $wa ?>?text=Halo,%20saya%20ingin%20menanyakan%20info%20Mitsubishi" target="_blank" class="wa-btn animate-on-scroll">
          <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp Sekarang
        </a>

        <div class="map-wrap animate-on-scroll">
          <iframe src="https://maps.google.com/maps?q=Jakarta+Pusat&output=embed" loading="lazy" allowfullscreen></iframe>
        </div>
      </div>
    </div>

    <!-- FAQ -->
    <div class="faq-section">
      <div class="section-title animate-on-scroll">
        <span class="subtitle">FAQ</span>
        <h2>Pertanyaan yang Sering Ditanyakan</h2>
        <p>Beberapa pertanyaan umum seputar pembelian Mitsubishi di dealer kami.</p>
      </div>
      <div class="faq-grid">
        <div class="faq-item animate-on-scroll">
          <h4><i class="fa-solid fa-circle-question"></i> Apakah tersedia fasilitas test drive?</h4>
          <p>Ya, kami menyediakan fasilitas test drive gratis untuk semua varian Mitsubishi. Hubungi kami untuk menjadwalkan test drive.</p>
        </div>
        <div class="faq-item animate-on-scroll">
          <h4><i class="fa-solid fa-circle-question"></i> Berapa DP minimal untuk kredit Mitsubishi?</h4>
          <p>DP minimal mulai dari 20% tergantung tipe kendaraan dan lembaga leasing yang dipilih. Kami bekerja sama dengan 10+ leasing terpercaya.</p>
        </div>
        <div class="faq-item animate-on-scroll">
          <h4><i class="fa-solid fa-circle-question"></i> Apakah ada garansi resmi Mitsubishi?</h4>
          <p>Semua kendaraan Mitsubishi yang dibeli di dealer kami mendapat garansi resmi dari Mitsubishi Motors selama 3 tahun atau 100.000 km.</p>
        </div>
        <div class="faq-item animate-on-scroll">
          <h4><i class="fa-solid fa-circle-question"></i> Berapa lama proses pembelian hingga unit siap?</h4>
          <p>Untuk pembelian cash dengan stok tersedia, unit siap dalam 3-5 hari kerja. Untuk indent, estimasi 4-8 minggu tergantung tipe.</p>
        </div>
        <div class="faq-item animate-on-scroll">
          <h4><i class="fa-solid fa-circle-question"></i> Apakah ada promo atau diskon khusus?</h4>
          <p>Promo dan diskon tersedia secara berkala. Ikuti media sosial kami atau hubungi CS untuk informasi promo terbaru Mitsubishi.</p>
        </div>
        <div class="faq-item animate-on-scroll">
          <h4><i class="fa-solid fa-circle-question"></i> Bisakah unit dikirim ke luar kota?</h4>
          <p>Ya, kami melayani pengiriman Mitsubishi ke seluruh Indonesia dengan biaya pengiriman yang kompetitif dan asuransi penuh.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
