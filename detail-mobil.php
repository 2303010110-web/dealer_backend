<?php
require_once 'includes/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('mobil.php');

$stmt = $conn->prepare("SELECT * FROM mobil WHERE id = ? AND status = 'aktif'");
$stmt->bind_param('i', $id);
$stmt->execute();
$mobil = $stmt->get_result()->fetch_assoc();
if (!$mobil) redirect('mobil.php');

// Mobil serupa
$similar = $conn->prepare("SELECT * FROM mobil WHERE tipe = ? AND id != ? AND status = 'aktif' LIMIT 4");
$similar->bind_param('si', $mobil['tipe'], $id);
$similar->execute();
$similarMobil = $similar->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= sanitize($mobil['nama_mobil']) ?> | Dealer Mitsubishi</title>
  <meta name="description" content="<?= sanitize(substr($mobil['deskripsi'], 0, 150)) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/footer.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <style>
    .page-header{background:linear-gradient(135deg,#111827,#1f2937);padding:110px 0 50px;color:white;position:relative;overflow:hidden}
    .page-header::before{content:'';position:absolute;width:500px;height:500px;background:radial-gradient(rgba(220,38,38,.2),transparent 70%);top:-150px;right:-100px}
    .detail-grid{display:grid;grid-template-columns:1.1fr 0.9fr;gap:42px;align-items:start;margin-top:40px}
    .detail-gallery{position:sticky;top:100px}
    .main-img{width:100%;height:380px;object-fit:cover;border-radius:24px;box-shadow:var(--shadow-lg);transition:.4s}
    .main-img:hover{transform:scale(1.02)}
    .gallery-thumbs{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:14px}
    .gallery-thumb{width:100%;height:85px;object-fit:cover;border-radius:14px;cursor:pointer;border:2.5px solid transparent;transition:.3s;opacity:.7}
    .gallery-thumb.active,.gallery-thumb:hover{border-color:var(--primary);opacity:1}
    .detail-info{}
    .mobil-badge-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}
    .badge-tag{padding:6px 16px;border-radius:50px;font-size:12px;font-weight:700;letter-spacing:.5px}
    .badge-primary{background:linear-gradient(135deg,#dc2626,#ef4444);color:white}
    .badge-secondary{background:var(--lighter);color:var(--gray)}
    .detail-info h1{font-size:38px;font-weight:800;color:var(--dark);line-height:1.2;margin-bottom:10px}
    .detail-price{font-size:34px;font-weight:800;color:var(--primary);margin-bottom:22px;display:flex;align-items:center;gap:12px}
    .detail-desc{color:var(--gray);font-size:15px;line-height:1.9;margin-bottom:28px;border-left:4px solid var(--primary);padding-left:18px}
    .specs-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:28px}
    .spec-item{display:flex;align-items:center;gap:14px;background:var(--lighter);border-radius:16px;padding:16px;transition:.3s}
    .spec-item:hover{background:rgba(220,38,38,.06);transform:translateY(-2px)}
    .spec-icon{width:44px;height:44px;background:linear-gradient(135deg,#dc2626,#ef4444);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;flex-shrink:0}
    .spec-text label{font-size:11px;color:var(--gray);display:block;margin-bottom:3px;text-transform:uppercase;letter-spacing:.5px}
    .spec-text span{font-size:14px;font-weight:600;color:var(--dark)}
    .stok-info{display:flex;align-items:center;gap:10px;padding:14px 20px;background:rgba(22,163,74,.08);border:1px solid rgba(22,163,74,.2);border-radius:14px;color:#166534;font-size:14px;font-weight:600;margin-bottom:22px}
    .stok-info.low{background:rgba(217,119,6,.08);border-color:rgba(217,119,6,.2);color:#92400e}
    .stok-info.habis{background:rgba(220,38,38,.08);border-color:rgba(220,38,38,.2);color:#dc2626}
    .action-buttons{display:flex;gap:14px;flex-wrap:wrap}
    .action-buttons .btn{flex:1;min-width:160px;justify-content:center}
    .similar-section{margin-top:70px}
    .similar-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:30px}
    @media(max-width:900px){.detail-grid{grid-template-columns:1fr}.detail-gallery{position:static}.similar-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:600px){.detail-info h1{font-size:26px}.detail-price{font-size:24px}.specs-grid{grid-template-columns:1fr}.action-buttons{flex-direction:column}.similar-grid{grid-template-columns:1fr}}
  </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb" style="color:#9ca3af;display:flex;gap:10px;align-items:center;font-size:14px">
      <a href="index.php" style="color:#ef4444;text-decoration:none">Home</a>
      <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
      <a href="mobil.php" style="color:#ef4444;text-decoration:none">Koleksi Mitsubishi</a>
      <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
      <span><?= sanitize($mobil['nama_mobil']) ?></span>
    </div>
  </div>
</div>

<section class="section" style="padding-top:40px">
  <div class="container">
    <div class="detail-grid">

      <!-- GALLERY -->
      <div class="detail-gallery animate-on-scroll">
        <img id="mainImg" class="main-img" src="<?= imgUrl($mobil['gambar']) ?>" alt="<?= sanitize($mobil['nama_mobil']) ?>">
        <div class="gallery-thumbs">
          <?php
          $thumbs = [
            $mobil['gambar'],
            'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?w=400&q=80',
            'https://images.unsplash.com/photo-1489824904134-891ab64532f1?w=400&q=80',
          ];
          foreach ($thumbs as $i => $th):
            $thUrl = imgUrl($th);
          ?> 
          <img class="gallery-thumb <?= $i===0?'active':'' ?>"
               src="<?= $thUrl ?>"
               alt="Foto <?= $i+1 ?>"
               onclick="changeImg(this, '<?= $thUrl ?>')">
          <?php endforeach; ?>
        </div>
      </div>

      <!-- INFO -->
      <div class="detail-info animate-on-scroll">
        <div class="mobil-badge-row">
          <?php if ($mobil['badge']): ?>
          <span class="badge-tag badge-primary"><?= sanitize($mobil['badge']) ?></span>
          <?php endif; ?>
          <span class="badge-tag badge-secondary"><i class="fa-solid fa-car"></i> <?= sanitize($mobil['tipe']) ?></span>
          <span class="badge-tag badge-secondary"><i class="fa-solid fa-calendar"></i> <?= $mobil['tahun'] ?></span>
        </div>

        <h1><?= sanitize($mobil['nama_mobil']) ?></h1>
        <div class="detail-price">
          <?= rupiah($mobil['harga']) ?>
          <span style="font-size:14px;color:var(--gray);font-weight:400">(Harga OTR)</span>
        </div>

        <p class="detail-desc"><?= sanitize($mobil['deskripsi']) ?></p>

        <!-- STOK -->
        <?php if ($mobil['stok'] > 5): ?>
        <div class="stok-info"><i class="fa-solid fa-circle-check"></i> Stok Tersedia (<?= $mobil['stok'] ?> unit)</div>
        <?php elseif ($mobil['stok'] > 0): ?>
        <div class="stok-info low"><i class="fa-solid fa-triangle-exclamation"></i> Stok Terbatas (<?= $mobil['stok'] ?> unit tersisa)</div>
        <?php else: ?>
        <div class="stok-info habis"><i class="fa-solid fa-circle-xmark"></i> Stok Habis — Hubungi kami untuk indent</div>
        <?php endif; ?>

        <!-- SPECS -->
        <div class="specs-grid">
          <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-gears"></i></div>
            <div class="spec-text"><label>Transmisi</label><span><?= sanitize($mobil['transmisi']) ?></span></div>
          </div>
          <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-gas-pump"></i></div>
            <div class="spec-text"><label>Bahan Bakar</label><span><?= sanitize($mobil['bahan_bakar']) ?></span></div>
          </div>
          <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-engine"></i></div>
            <div class="spec-text"><label>Mesin</label><span><?= sanitize($mobil['mesin']) ?></span></div>
          </div>
          <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-palette"></i></div>
            <div class="spec-text"><label>Warna</label><span><?= sanitize($mobil['warna']) ?></span></div>
          </div>
          <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-car-side"></i></div>
            <div class="spec-text"><label>Merk</label><span><?= sanitize($mobil['merk']) ?></span></div>
          </div>
          <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="spec-text"><label>Tahun</label><span><?= $mobil['tahun'] ?></span></div>
          </div>
        </div>

        <div class="action-buttons">
          <?php if ($mobil['stok'] > 0): ?>
          <a href="booking.php?id=<?= $mobil['id'] ?>" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-calendar-check"></i> Booking Sekarang
          </a>
          <?php endif; ?>
          <a href="https://wa.me/<?= WA_NUMBER ?>?text=Halo,%20saya%20tertarik%20dengan%20<?= urlencode($mobil['nama_mobil']) ?>%20seharga%20<?= urlencode(rupiah($mobil['harga'])) ?>"
             target="_blank" class="btn btn-ghost btn-lg">
            <i class="fa-brands fa-whatsapp"></i> Tanya via WA
          </a>
        </div>
      </div>
    </div>

    <!-- SIMILAR CARS -->
    <?php if ($similarMobil->num_rows > 0): ?>
    <div class="similar-section">
      <h2 class="animate-on-scroll" style="font-size:26px;color:var(--dark)">Mitsubishi <span style="color:var(--primary)">Serupa</span></h2>
      <div class="similar-grid">
        <?php while ($s = $similarMobil->fetch_assoc()): ?>
        <div class="car-card animate-on-scroll">
          <?php if ($s['badge']): ?><div class="car-badge"><?= sanitize($s['badge']) ?></div><?php endif; ?>
          <div class="car-img">
            <img src="<?= imgUrl($s['gambar']) ?>" alt="<?= sanitize($s['nama_mobil']) ?>" loading="lazy">
            <div class="car-overlay">
              <a href="detail-mobil.php?id=<?= $s['id'] ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-eye"></i> Detail</a>
            </div>
          </div>
          <div class="car-info">
            <div class="car-meta"><span class="car-type"><?= sanitize($s['tipe']) ?></span><span class="car-year"><?= $s['tahun'] ?></span></div>
            <h3 class="car-name"><?= sanitize($s['nama_mobil']) ?></h3>
            <div class="car-footer">
              <div class="car-price"><?= rupiah($s['harga']) ?></div>
              <a href="booking.php?id=<?= $s['id'] ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-calendar-check"></i></a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
<script>
function changeImg(el, src) {
  document.getElementById('mainImg').src = src;
  document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
}
</script>
</body>
</html>
