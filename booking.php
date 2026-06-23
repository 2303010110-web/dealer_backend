<?php
require_once 'includes/config.php';

$id = (int)($_GET['id'] ?? 0);

// Ambil semua mobil aktif untuk dropdown
$allMobil = $conn->query("SELECT id, nama_mobil, harga, stok FROM mobil WHERE status='aktif' AND stok > 0 ORDER BY nama_mobil");

// Ambil mobil yang dipilih
$selectedMobil = null;
if ($id) {
    $stmtM = $conn->prepare("SELECT * FROM mobil WHERE id = ? AND status = 'aktif'");
    $stmtM->bind_param('i', $id);
    $stmtM->execute();
    $selectedMobil = $stmtM->get_result()->fetch_assoc();
}

$error   = '';
$success = '';
$kodeBooking = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobil_id  = (int)$_POST['mobil_id'];
    $nama      = sanitize($_POST['nama'] ?? '');
    $email     = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $no_hp     = sanitize($_POST['no_hp'] ?? '');
    $alamat    = sanitize($_POST['alamat'] ?? '');
    $jumlah    = max(1, (int)($_POST['jumlah'] ?? 1));
    $metode    = sanitize($_POST['metode'] ?? '');
    $tgl       = sanitize($_POST['tanggal_booking'] ?? '');
    $catatan   = sanitize($_POST['catatan'] ?? '');

    // Validasi
    if (!$mobil_id || !$nama || !$email || !$no_hp || !$alamat || !$metode || !$tgl) {
        $error = 'Semua field wajib diisi dengan lengkap.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Ambil harga mobil
        $stmtH = $conn->prepare("SELECT harga, stok FROM mobil WHERE id = ?");
        $stmtH->bind_param('i', $mobil_id);
        $stmtH->execute();
        $mData = $stmtH->get_result()->fetch_assoc();

        if (!$mData) {
            $error = 'Mobil tidak ditemukan.';
        } elseif ($jumlah > $mData['stok']) {
            $error = 'Jumlah melebihi stok yang tersedia (' . $mData['stok'] . ' unit).';
        } else {
            $total = $mData['harga'] * $jumlah;
            $kode  = generateKodeBooking();

            $ins = $conn->prepare("INSERT INTO booking (kode_booking, mobil_id, nama, email, no_hp, alamat, jumlah, metode, tanggal_booking, total_harga, catatan) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $ins->bind_param('sissssissis', $kode, $mobil_id, $nama, $email, $no_hp, $alamat, $jumlah, $metode, $tgl, $total, $catatan);

            if ($ins->execute()) {
                $kodeBooking = $kode;
                $success = 'Booking berhasil! Kode booking Anda: <strong>' . $kode . '</strong>. Tim kami akan menghubungi Anda segera.';
            } else {
                $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Booking Mitsubishi | Dealer Resmi Mitsubishi</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/footer.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <style>
    .page-header{background:linear-gradient(135deg,#111827,#1f2937);padding:110px 0 50px;color:white;position:relative;overflow:hidden}
    .page-header::before{content:'';position:absolute;width:500px;height:500px;background:radial-gradient(rgba(220,38,38,.25),transparent 70%);top:-150px;right:-100px}
    .booking-layout{display:grid;grid-template-columns:1fr 380px;gap:34px;align-items:start;margin-top:40px}
    .booking-form-card{background:white;border-radius:28px;padding:38px;box-shadow:var(--shadow)}
    .booking-form-card h2{font-size:22px;color:var(--dark);margin-bottom:28px;padding-bottom:18px;border-bottom:2px solid var(--lighter);display:flex;align-items:center;gap:12px}
    .booking-form-card h2 i{color:var(--primary)}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .form-group{margin-bottom:20px}
    .form-group label{font-size:13px;font-weight:600;color:var(--dark);display:block;margin-bottom:8px}
    .form-group input,.form-group select,.form-group textarea{width:100%;padding:13px 16px;border:1.5px solid #e5e7eb;border-radius:14px;font-size:14px;font-family:'Poppins',sans-serif;color:var(--dark);background:var(--lighter);transition:.3s;box-sizing:border-box}
    .form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:var(--primary);background:white;box-shadow:0 0 0 3px rgba(220,38,38,.1)}
    .form-group textarea{resize:vertical;min-height:90px}
    .metode-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
    .metode-opt{position:relative}
    .metode-opt input{position:absolute;opacity:0;width:0;height:0}
    .metode-opt label{display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 10px;border:2px solid #e5e7eb;border-radius:16px;cursor:pointer;transition:.3s;background:var(--lighter);text-align:center}
    .metode-opt label i{font-size:22px;color:var(--gray)}
    .metode-opt label span{font-size:12px;font-weight:600;color:var(--gray)}
    .metode-opt input:checked + label{border-color:var(--primary);background:rgba(220,38,38,.06)}
    .metode-opt input:checked + label i,.metode-opt input:checked + label span{color:var(--primary)}
    .submit-btn{width:100%;padding:16px;background:linear-gradient(135deg,#dc2626,#ef4444);color:white;border:none;border-radius:16px;font-size:16px;font-weight:700;cursor:pointer;transition:.3s;font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;gap:10px;margin-top:8px}
    .submit-btn:hover{opacity:.9;transform:translateY(-2px);box-shadow:0 8px 22px rgba(220,38,38,.3)}
    .alert{padding:16px 20px;border-radius:16px;font-size:14px;margin-bottom:22px;display:flex;align-items:flex-start;gap:12px}
    .alert-error{background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);color:#dc2626}
    .alert-success{background:rgba(22,163,74,.08);border:1px solid rgba(22,163,74,.2);color:#166534}
    .booking-sidebar-card{background:white;border-radius:28px;padding:28px;box-shadow:var(--shadow);position:sticky;top:100px}
    .car-preview img{width:100%;height:180px;object-fit:cover;border-radius:18px;margin-bottom:16px}
    .car-preview h3{font-size:18px;color:var(--dark);margin-bottom:6px}
    .car-preview .price{font-size:22px;font-weight:800;color:var(--primary);margin-bottom:14px}
    .summary-item{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--lighter);font-size:14px}
    .summary-item:last-child{border:none}
    .summary-item span:first-child{color:var(--gray)}
    .summary-item span:last-child{color:var(--dark);font-weight:600}
    .summary-total{display:flex;justify-content:space-between;align-items:center;padding:16px;background:linear-gradient(135deg,rgba(220,38,38,.08),rgba(239,68,68,.05));border-radius:14px;margin-top:10px}
    .summary-total span:first-child{font-weight:600;color:var(--dark)}
    .summary-total span:last-child{font-size:20px;font-weight:800;color:var(--primary)}
    .booking-steps{display:flex;flex-direction:column;gap:14px;margin-top:22px}
    .step-item{display:flex;align-items:flex-start;gap:14px}
    .step-num{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#dc2626,#ef4444);color:white;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0}
    .step-text h4{font-size:13px;color:var(--dark);font-weight:600;margin-bottom:3px}
    .step-text p{font-size:12px;color:var(--gray);line-height:1.6}
    .success-box{text-align:center;padding:40px 20px}
    .success-icon{width:80px;height:80px;background:rgba(22,163,74,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px;color:#16a34a;margin:0 auto 20px}
    @media(max-width:900px){.booking-layout{grid-template-columns:1fr}.booking-sidebar-card{position:static}}
    @media(max-width:600px){.form-row{grid-template-columns:1fr}.metode-grid{grid-template-columns:1fr}}
  </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb" style="color:#9ca3af;display:flex;gap:10px;align-items:center;font-size:14px;margin-bottom:16px">
      <a href="index.php" style="color:#ef4444;text-decoration:none">Home</a>
      <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
      <span>Booking Mitsubishi</span>
    </div>
    <h1 style="font-size:40px;font-weight:800;color:white;margin-bottom:10px">Form <span style="color:#ef4444">Booking</span></h1>
    <p style="color:#9ca3af">Isi formulir di bawah ini untuk memesan Mitsubishi impian Anda.</p>
  </div>
</div>

<section class="section" style="padding-top:40px">
  <div class="container">
    <?php if ($success): ?>
    <div class="booking-form-card animate-on-scroll" style="max-width:640px;margin:0 auto">
      <div class="success-box">
        <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
        <h2 style="color:var(--dark);margin-bottom:12px">Booking Berhasil!</h2>
        <p style="color:var(--gray);margin-bottom:20px"><?= $success ?></p>
        <div style="background:var(--lighter);border-radius:16px;padding:20px;margin-bottom:24px">
          <div style="font-size:13px;color:var(--gray);margin-bottom:6px">Kode Booking Anda</div>
          <div style="font-size:28px;font-weight:800;color:var(--primary);letter-spacing:2px"><?= $kodeBooking ?></div>
        </div>
        <p style="font-size:13px;color:var(--gray);margin-bottom:26px">Simpan kode ini untuk tracking pesanan. Tim kami akan menghubungi Anda dalam 1x24 jam kerja.</p>
        <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
          <a href="index.php" class="btn btn-primary"><i class="fa-solid fa-house"></i> Kembali ke Home</a>
          <a href="mobil.php" class="btn btn-ghost"><i class="fa-solid fa-car"></i> Lihat Mobil Lain</a>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="booking-layout">
      <div class="booking-form-card animate-on-scroll">
        <h2><i class="fa-solid fa-calendar-check"></i> Form Booking Mitsubishi</h2>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" id="bookingForm">
          <div class="form-group">
            <label><i class="fa-solid fa-car"></i> Pilih Mitsubishi *</label>
            <select name="mobil_id" id="mobilSelect" required onchange="updateSummary()">
              <option value="">— Pilih Mitsubishi —</option>
              <?php
              $allMobil->data_seek(0);
              while ($m = $allMobil->fetch_assoc()):
              ?>
              <option value="<?= $m['id'] ?>"
                      data-harga="<?= $m['harga'] ?>"
                      data-nama="<?= sanitize($m['nama_mobil']) ?>"
                      <?= $selectedMobil && $selectedMobil['id'] == $m['id'] ? 'selected' : '' ?>>
                <?= sanitize($m['nama_mobil']) ?> — <?= rupiah($m['harga']) ?> (Stok: <?= $m['stok'] ?>)
              </option>
              <?php endwhile; ?>
            </select>
          </div>

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
              <label><i class="fa-solid fa-phone"></i> No. HP / WhatsApp *</label>
              <input type="tel" name="no_hp" placeholder="08xxxxxxxxxx" required value="<?= sanitize($_POST['no_hp'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-hashtag"></i> Jumlah Unit *</label>
              <input type="number" name="jumlah" id="jumlahInput" min="1" value="<?= (int)($_POST['jumlah'] ?? 1) ?>" required onchange="updateSummary()">
            </div>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-location-dot"></i> Alamat Lengkap *</label>
            <textarea name="alamat" placeholder="Alamat pengiriman / alamat rumah Anda" required><?= sanitize($_POST['alamat'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-calendar"></i> Tanggal Pengambilan *</label>
            <input type="date" name="tanggal_booking" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required value="<?= sanitize($_POST['tanggal_booking'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-credit-card"></i> Metode Pembayaran *</label>
            <div class="metode-grid">
              <div class="metode-opt">
                <input type="radio" name="metode" id="cash" value="cash" <?= ($_POST['metode']??'')==='cash'?'checked':'' ?> required>
                <label for="cash"><i class="fa-solid fa-money-bill-wave"></i><span>Cash</span></label>
              </div>
              <div class="metode-opt">
                <input type="radio" name="metode" id="kredit" value="kredit" <?= ($_POST['metode']??'')==='kredit'?'checked':'' ?>>
                <label for="kredit"><i class="fa-solid fa-credit-card"></i><span>Kredit</span></label>
              </div>
              <div class="metode-opt">
                <input type="radio" name="metode" id="leasing" value="leasing" <?= ($_POST['metode']??'')==='leasing'?'checked':'' ?>>
                <label for="leasing"><i class="fa-solid fa-building-columns"></i><span>Leasing</span></label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-comment"></i> Catatan (opsional)</label>
            <textarea name="catatan" placeholder="Warna yang diinginkan, pertanyaan, atau catatan khusus..."><?= sanitize($_POST['catatan'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="submit-btn">
            <i class="fa-solid fa-calendar-check"></i> Kirim Booking Sekarang
          </button>
        </form>
      </div>

      <!-- SIDEBAR -->
      <div class="booking-sidebar-card animate-on-scroll">
        <?php if ($selectedMobil): ?>
        <div class="car-preview">
          <img src="<?= sanitize($selectedMobil['gambar']) ?>" alt="<?= sanitize($selectedMobil['nama_mobil']) ?>">
          <h3><?= sanitize($selectedMobil['nama_mobil']) ?></h3>
          <div class="price"><?= rupiah($selectedMobil['harga']) ?></div>
        </div>
        <?php endif; ?>

        <h3 style="font-size:16px;color:var(--dark);margin-bottom:16px;display:flex;align-items:center;gap:10px">
          <i class="fa-solid fa-file-invoice" style="color:var(--primary)"></i> Ringkasan
        </h3>
        <div class="summary-item">
          <span>Mitsubishi</span>
          <span id="sumNama">—</span>
        </div>
        <div class="summary-item">
          <span>Harga/unit</span>
          <span id="sumHarga">—</span>
        </div>
        <div class="summary-item">
          <span>Jumlah</span>
          <span id="sumJumlah">1</span>
        </div>
        <div class="summary-total">
          <span>Total</span>
          <span id="sumTotal">—</span>
        </div>

        <div class="booking-steps" style="margin-top:26px">
          <h4 style="font-size:14px;color:var(--dark);margin-bottom:4px"><i class="fa-solid fa-list-check" style="color:var(--primary)"></i> Proses Booking</h4>
          <div class="step-item">
            <div class="step-num">1</div>
            <div class="step-text"><h4>Isi Formulir</h4><p>Lengkapi data diri dan pilih Mitsubishi yang diinginkan.</p></div>
          </div>
          <div class="step-item">
            <div class="step-num">2</div>
            <div class="step-text"><h4>Konfirmasi</h4><p>Tim kami menghubungi Anda dalam 1x24 jam kerja.</p></div>
          </div>
          <div class="step-item">
            <div class="step-num">3</div>
            <div class="step-text"><h4>Pembayaran</h4><p>Selesaikan pembayaran sesuai metode yang dipilih.</p></div>
          </div>
          <div class="step-item">
            <div class="step-num">4</div>
            <div class="step-text"><h4>Pengambilan</h4><p>Ambil Mitsubishi pada tanggal yang ditentukan.</p></div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
<script>
const mobilSelect  = document.getElementById('mobilSelect');
const jumlahInput  = document.getElementById('jumlahInput');

function updateSummary() {
  const opt    = mobilSelect?.options[mobilSelect.selectedIndex];
  const harga  = parseInt(opt?.dataset.harga || 0);
  const nama   = opt?.dataset.nama || '—';
  const jumlah = parseInt(jumlahInput?.value || 1);
  const total  = harga * jumlah;

  document.getElementById('sumNama').textContent   = nama;
  document.getElementById('sumHarga').textContent  = harga ? 'Rp ' + harga.toLocaleString('id-ID') : '—';
  document.getElementById('sumJumlah').textContent = jumlah;
  document.getElementById('sumTotal').textContent  = total ? 'Rp ' + total.toLocaleString('id-ID') : '—';
}

document.addEventListener('DOMContentLoaded', updateSummary);
</script>
</body>
</html>
