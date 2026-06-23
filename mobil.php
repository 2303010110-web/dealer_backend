<?php
require_once 'includes/config.php';

// Filter & Search
$q        = sanitize($_GET['q'] ?? '');
$tipe     = sanitize($_GET['tipe'] ?? '');
$transmisi = sanitize($_GET['transmisi'] ?? '');
$min_harga = (int)($_GET['min_harga'] ?? 0);
$max_harga = (int)($_GET['max_harga'] ?? 0);
$sort     = sanitize($_GET['sort'] ?? 'terbaru');
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 9;
$offset   = ($page - 1) * $per_page;

// Build Query
$where = ["status = 'aktif'"];
$params = [];
$types  = '';

if ($q) {
    $where[] = "(nama_mobil LIKE ? OR tipe LIKE ? OR deskripsi LIKE ?)";
    $like = "%$q%";
    array_push($params, $like, $like, $like);
    $types .= 'sss';
}
if ($tipe) {
    $where[] = "tipe = ?";
    $params[] = $tipe; $types .= 's';
}
if ($transmisi) {
    $where[] = "transmisi = ?";
    $params[] = $transmisi; $types .= 's';
}
if ($min_harga > 0) {
    $where[] = "harga >= ?";
    $params[] = $min_harga; $types .= 'i';
}
if ($max_harga > 0) {
    $where[] = "harga <= ?";
    $params[] = $max_harga; $types .= 'i';
}

$whereSQL = implode(' AND ', $where);

$orderSQL = match($sort) {
    'harga_asc'  => 'harga ASC',
    'harga_desc' => 'harga DESC',
    'nama_asc'   => 'nama_mobil ASC',
    default      => 'id DESC'
};

// Count total
$stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM mobil WHERE $whereSQL");
if ($types) $stmtCount->bind_param($types, ...$params);
$stmtCount->execute();
$total_rows = $stmtCount->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $per_page);

// Fetch mobil
$stmtMobil = $conn->prepare("SELECT * FROM mobil WHERE $whereSQL ORDER BY $orderSQL LIMIT ? OFFSET ?");
$allParams  = array_merge($params, [$per_page, $offset]);
$allTypes   = $types . 'ii';
$stmtMobil->bind_param($allTypes, ...$allParams);
$stmtMobil->execute();
$mobils = $stmtMobil->get_result();

// Ambil semua tipe unik
$tipeList = $conn->query("SELECT DISTINCT tipe FROM mobil WHERE status='aktif' ORDER BY tipe");
<<<<<<< HEAD
=======

// Fungsi untuk mendapatkan path gambar yang benar
function getGambarPath($gambar) {
    if (empty($gambar)) {
        return '';
    }
    
    // Path yang akan dicek
    $pathsToCheck = [
        $gambar, // Path langsung dari database
        'uploads/mobil/' . basename($gambar), // Uploads folder
        '../uploads/mobil/' . basename($gambar), // Dari folder atas
        '../' . $gambar, // Dari folder atas dengan path database
        'assets/images/' . basename($gambar), // Assets folder
        'images/' . basename($gambar), // Images folder
    ];
    
    // Cek setiap kemungkinan path
    foreach ($pathsToCheck as $path) {
        if (!empty($path) && file_exists(__DIR__ . '/' . $path)) {
            return $path;
        }
    }
    
    // Jika path dimulai dengan 'uploads/' dan tidak ditemukan, coba dengan '../'
    if (strpos($gambar, 'uploads/') === 0) {
        $altPath = '../' . $gambar;
        if (file_exists(__DIR__ . '/' . $altPath)) {
            return $altPath;
        }
    }
    
    // Jika tidak ditemukan sama sekali, return path asli
    return $gambar;
}

// Fungsi untuk format rupiah jika belum ada
if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Koleksi Mitsubishi | Dealer Resmi Mitsubishi</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/footer.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <style>
    .page-header{background:linear-gradient(135deg,#111827 0%,#1f2937 100%);padding:120px 0 60px;color:white;position:relative;overflow:hidden}
    .page-header::before{content:'';position:absolute;width:500px;height:500px;background:radial-gradient(rgba(220,38,38,.25),transparent 70%);top:-150px;right:-100px}
    .page-header h1{font-size:46px;font-weight:800;margin-bottom:14px}
    .page-header h1 span{color:#ef4444}
    .page-header p{color:#9ca3af;font-size:16px;max-width:540px}
    .breadcrumb{display:flex;align-items:center;gap:10px;margin-bottom:18px;font-size:14px;color:#6b7280}
    .breadcrumb a{color:#ef4444;text-decoration:none}
    .mobil-layout{display:grid;grid-template-columns:280px 1fr;gap:32px;align-items:start}
    .filter-sidebar{background:white;border-radius:24px;padding:28px;box-shadow:var(--shadow);position:sticky;top:100px}
    .filter-sidebar h3{font-size:17px;color:var(--dark);margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid var(--lighter);display:flex;align-items:center;gap:10px}
    .filter-group{margin-bottom:26px}
    .filter-group label{font-size:13px;font-weight:600;color:var(--dark);display:block;margin-bottom:10px}
    .filter-group select,.filter-group input[type=number]{width:100%;padding:10px 14px;border:1.5px solid var(--lighter);border-radius:12px;font-size:14px;color:var(--dark);font-family:'Poppins',sans-serif;background:var(--lighter);transition:.3s}
    .filter-group select:focus,.filter-group input:focus{outline:none;border-color:var(--primary);background:white}
    .filter-chips{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px}
<<<<<<< HEAD
    .filter-chip{padding:6px 14px;border-radius:50px;border:1.5px solid var(--lighter);background:white;font-size:12px;color:var(--gray);cursor:pointer;transition:.25s;font-family:'Poppins',sans-serif}
=======
    .filter-chip{padding:6px 14px;border-radius:50px;border:1.5px solid var(--lighter);background:white;font-size:12px;color:var(--gray);cursor:pointer;transition:.25s;font-family:'Poppins',sans-serif;text-decoration:none;display:inline-block}
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
    .filter-chip.active,.filter-chip:hover{background:var(--primary);border-color:var(--primary);color:white}
    .price-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .filter-btn{width:100%;padding:12px;background:linear-gradient(135deg,#dc2626,#ef4444);color:white;border:none;border-radius:14px;font-size:14px;font-weight:600;cursor:pointer;transition:.3s;font-family:'Poppins',sans-serif}
    .filter-btn:hover{opacity:.88;transform:translateY(-1px)}
<<<<<<< HEAD
    .filter-reset{width:100%;padding:10px;background:var(--lighter);color:var(--gray);border:none;border-radius:14px;font-size:13px;cursor:pointer;transition:.3s;margin-top:10px;font-family:'Poppins',sans-serif}
=======
    .filter-reset{width:100%;padding:10px;background:var(--lighter);color:var(--gray);border:none;border-radius:14px;font-size:13px;cursor:pointer;transition:.3s;margin-top:10px;font-family:'Poppins',sans-serif;text-align:center;display:block;text-decoration:none}
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
    .filter-reset:hover{background:#e5e7eb}
    .mobil-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:14px}
    .result-info{font-size:14px;color:var(--gray)}
    .result-info strong{color:var(--dark)}
    .sort-select{padding:10px 16px;border:1.5px solid var(--lighter);border-radius:12px;font-size:14px;font-family:'Poppins',sans-serif;color:var(--dark);background:white;cursor:pointer}
    .sort-select:focus{outline:none;border-color:var(--primary)}
    .mobil-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
<<<<<<< HEAD
=======
    
    /* Car Card Styles */
    .car-card{background:white;border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);transition:all .35s;position:relative}
    .car-card:hover{transform:translateY(-8px);box-shadow:0 12px 40px rgba(0,0,0,.12)}
    .car-img{position:relative;overflow:hidden;height:200px;background:#f1f5f9}
    .car-img img{width:100%;height:100%;object-fit:cover;transition:transform .5s}
    .car-card:hover .car-img img{transform:scale(1.05)}
    .car-badge{position:absolute;top:14px;left:14px;background:linear-gradient(135deg,#dc2626,#ef4444);color:white;padding:4px 14px;border-radius:50px;font-size:11px;font-weight:600;z-index:2;text-transform:uppercase;letter-spacing:.5px}
    .car-overlay{position:absolute;inset:0;background:rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .35s}
    .car-card:hover .car-overlay{opacity:1}
    .car-info{padding:18px 20px 20px}
    .car-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
    .car-type{font-size:12px;font-weight:600;color:var(--primary);background:#fef2f2;padding:2px 12px;border-radius:50px}
    .car-year{font-size:12px;color:var(--gray)}
    .car-name{font-size:17px;font-weight:700;color:var(--dark);margin-bottom:10px;line-height:1.3}
    .car-specs{display:flex;gap:14px;margin-bottom:14px;flex-wrap:wrap}
    .car-specs span{font-size:12px;color:var(--gray);display:flex;align-items:center;gap:5px}
    .car-specs span i{color:var(--primary);font-size:11px}
    .car-footer{display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1.5px solid var(--lighter)}
    .car-price{font-size:18px;font-weight:800;color:var(--primary)}
    
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
    .no-result{text-align:center;padding:70px 30px;background:white;border-radius:24px;box-shadow:var(--shadow)}
    .no-result i{font-size:60px;color:#e5e7eb;margin-bottom:20px;display:block}
    .no-result h3{font-size:20px;color:var(--dark);margin-bottom:10px}
    .no-result p{color:var(--gray);font-size:14px}
    .pagination{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:40px;flex-wrap:wrap}
    .pagination a,.pagination span{padding:10px 16px;border-radius:12px;font-size:14px;font-weight:500;text-decoration:none;transition:.25s}
    .pagination a{background:white;color:var(--dark);box-shadow:var(--shadow)}
    .pagination a:hover{background:var(--primary);color:white}
    .pagination .active{background:linear-gradient(135deg,#dc2626,#ef4444);color:white;font-weight:700}
    .pagination .disabled{background:#f3f4f6;color:#d1d5db;cursor:not-allowed}
<<<<<<< HEAD
=======
    
    .btn{display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:10px;font-weight:600;font-size:13px;text-decoration:none;transition:all .3s;border:none;cursor:pointer;font-family:'Poppins',sans-serif}
    .btn-primary{background:linear-gradient(135deg,#dc2626,#ef4444);color:white}
    .btn-primary:hover{opacity:.88;transform:translateY(-2px);box-shadow:0 8px 24px rgba(220,38,38,.3)}
    .btn-sm{padding:6px 16px;font-size:12px}
    
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
    @media(max-width:900px){.mobil-layout{grid-template-columns:1fr}.filter-sidebar{position:static}.mobil-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:600px){.mobil-grid{grid-template-columns:1fr}}
  </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a>
      <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
      <span>Koleksi Mitsubishi</span>
    </div>
    <h1>Koleksi <span>Mitsubishi</span> Kami</h1>
    <p>Temukan Mitsubishi impian Anda dari berbagai pilihan tipe, warna, dan harga yang tersedia di dealer resmi kami.</p>
  </div>
</div>

<section class="section" style="padding-top:50px">
  <div class="container">
    <div class="mobil-layout">

      <!-- SIDEBAR FILTER -->
      <aside class="filter-sidebar">
        <h3><i class="fa-solid fa-sliders" style="color:var(--primary)"></i> Filter Pencarian</h3>
        <form action="mobil.php" method="GET" id="filterForm">

          <div class="filter-group">
            <label><i class="fa-solid fa-magnifying-glass"></i> Cari Mobil</label>
            <input type="text" name="q" placeholder="Nama / tipe mobil..." value="<?= $q ?>">
          </div>

          <div class="filter-group">
            <label><i class="fa-solid fa-car"></i> Tipe Kendaraan</label>
            <div class="filter-chips">
              <a href="mobil.php" class="filter-chip <?= !$tipe ? 'active' : '' ?>">Semua</a>
<<<<<<< HEAD
              <?php while ($t = $tipeList->fetch_assoc()): ?>
=======
              <?php 
              $tipeList->data_seek(0);
              while ($t = $tipeList->fetch_assoc()): 
              ?>
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
              <a href="?tipe=<?= urlencode($t['tipe']) ?><?= $q ? '&q='.urlencode($q) : '' ?>"
                 class="filter-chip <?= $tipe === $t['tipe'] ? 'active' : '' ?>">
                <?= sanitize($t['tipe']) ?>
              </a>
              <?php endwhile; ?>
            </div>
          </div>

          <div class="filter-group">
            <label><i class="fa-solid fa-gears"></i> Transmisi</label>
            <select name="transmisi">
              <option value="">Semua Transmisi</option>
              <?php foreach (['Automatic','Manual','CVT'] as $tr): ?>
              <option value="<?= $tr ?>" <?= $transmisi === $tr ? 'selected' : '' ?>><?= $tr ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="filter-group">
            <label><i class="fa-solid fa-money-bill-wave"></i> Rentang Harga</label>
            <div class="price-row">
              <input type="number" name="min_harga" placeholder="Min (Rp)" value="<?= $min_harga ?: '' ?>" min="0">
              <input type="number" name="max_harga" placeholder="Max (Rp)" value="<?= $max_harga ?: '' ?>" min="0">
            </div>
          </div>

          <input type="hidden" name="sort" id="sortHidden" value="<?= $sort ?>">
          <button type="submit" class="filter-btn"><i class="fa-solid fa-filter"></i> Terapkan Filter</button>
<<<<<<< HEAD
          <a href="mobil.php"><button type="button" class="filter-reset"><i class="fa-solid fa-rotate-left"></i> Reset Filter</button></a>
=======
          <a href="mobil.php" class="filter-reset"><i class="fa-solid fa-rotate-left"></i> Reset Filter</a>
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
        </form>
      </aside>

      <!-- MOBIL LIST -->
      <div class="mobil-main">
        <div class="mobil-header">
          <div class="result-info">
            Menampilkan <strong><?= min($offset + $per_page, $total_rows) ?></strong> dari <strong><?= $total_rows ?></strong> Mitsubishi
            <?php if ($q): ?> untuk "<strong><?= $q ?></strong>"<?php endif; ?>
          </div>
          <select class="sort-select" onchange="changeSort(this.value)">
            <option value="terbaru"  <?= $sort==='terbaru'   ? 'selected':'' ?>>Terbaru</option>
            <option value="harga_asc" <?= $sort==='harga_asc' ? 'selected':'' ?>>Harga: Rendah</option>
            <option value="harga_desc"<?= $sort==='harga_desc'? 'selected':'' ?>>Harga: Tinggi</option>
            <option value="nama_asc" <?= $sort==='nama_asc'  ? 'selected':'' ?>>Nama A-Z</option>
          </select>
        </div>

        <?php if ($mobils->num_rows > 0): ?>
        <div class="mobil-grid">
<<<<<<< HEAD
          <?php while ($m = $mobils->fetch_assoc()): ?>
=======
          <?php while ($m = $mobils->fetch_assoc()): 
            // Dapatkan path gambar yang benar
            $gambarPath = getGambarPath($m['gambar']);
            
            // Cek apakah file benar-benar ada
            $fileExists = !empty($gambarPath) && file_exists(__DIR__ . '/' . $gambarPath);
          ?>
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
          <div class="car-card animate-on-scroll">
            <?php if ($m['badge']): ?>
            <div class="car-badge"><?= sanitize($m['badge']) ?></div>
            <?php endif; ?>
            <div class="car-img">
<<<<<<< HEAD
              <img src="<?= imgUrl($m['gambar']) ?>" alt="<?= sanitize($m['nama_mobil']) ?>" loading="lazy">
=======
              <?php if ($fileExists): ?>
                <img src="<?= $gambarPath ?>" alt="<?= sanitize($m['nama_mobil']) ?>" loading="lazy" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22><rect fill=%22%23f1f5f9%22 width=%22400%22 height=%22200%22/><text x=%22120%22 y=%22110%22 font-size=%2220%22 fill=%22%239ca3af%22 font-family=%22Poppins%22>No Image</text></svg>'">
              <?php else: ?>
                <img src="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22><rect fill=%22%23f1f5f9%22 width=%22400%22 height=%22200%22/><text x=%22120%22 y=%22110%22 font-size=%2220%22 fill=%22%239ca3af%22 font-family=%22Poppins%22>No Image</text></svg>" alt="No Image">
              <?php endif; ?>
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
              <div class="car-overlay">
                <a href="detail-mobil.php?id=<?= $m['id'] ?>" class="btn btn-primary btn-sm">
                  <i class="fa-solid fa-eye"></i> Lihat Detail
                </a>
              </div>
            </div>
            <div class="car-info">
              <div class="car-meta">
                <span class="car-type"><?= sanitize($m['tipe']) ?></span>
                <span class="car-year"><?= $m['tahun'] ?></span>
              </div>
              <h3 class="car-name"><?= sanitize($m['nama_mobil']) ?></h3>
              <div class="car-specs">
                <span><i class="fa-solid fa-gears"></i> <?= sanitize($m['transmisi']) ?></span>
                <span><i class="fa-solid fa-gas-pump"></i> <?= sanitize($m['bahan_bakar']) ?></span>
                <span><i class="fa-solid fa-layer-group"></i> Stok: <?= $m['stok'] ?></span>
              </div>
              <div class="car-footer">
                <div class="car-price"><?= rupiah($m['harga']) ?></div>
                <a href="booking.php?id=<?= $m['id'] ?>" class="btn btn-primary btn-sm">
                  <i class="fa-solid fa-calendar-check"></i> Booking
                </a>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>

        <!-- PAGINATION -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <?php
          $qStr = http_build_query(array_filter(['q'=>$q,'tipe'=>$tipe,'transmisi'=>$transmisi,'min_harga'=>$min_harga,'max_harga'=>$max_harga,'sort'=>$sort]));
          ?>
          <?php if ($page > 1): ?>
          <a href="?<?= $qStr ?>&page=<?= $page-1 ?>"><i class="fa-solid fa-chevron-left"></i></a>
          <?php else: ?>
          <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
          <?php endif; ?>

          <?php for ($i = max(1,$page-2); $i <= min($total_pages,$page+2); $i++): ?>
          <a href="?<?= $qStr ?>&page=<?= $i ?>" class="<?= $i===$page?'active':'' ?>"><?= $i ?></a>
          <?php endfor; ?>

          <?php if ($page < $total_pages): ?>
          <a href="?<?= $qStr ?>&page=<?= $page+1 ?>"><i class="fa-solid fa-chevron-right"></i></a>
          <?php else: ?>
          <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="no-result animate-on-scroll">
          <i class="fa-solid fa-car-burst"></i>
          <h3>Mitsubishi Tidak Ditemukan</h3>
          <p>Coba ubah kata kunci pencarian atau hapus beberapa filter yang diterapkan.</p>
          <a href="mobil.php" class="btn btn-primary" style="margin-top:20px;display:inline-flex">
            <i class="fa-solid fa-rotate-left"></i> Reset Pencarian
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
<script>
function changeSort(v) {
  document.getElementById('sortHidden').value = v;
  document.getElementById('filterForm').submit();
}
<<<<<<< HEAD
</script>
</body>
</html>
=======

// Animasi scroll
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});
</script>
</body>
</html>
>>>>>>> 20c1e223d846345e893658d18c2bd0949006bcee
