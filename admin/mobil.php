<?php
require_once '../includes/config.php';
requireAdmin();

$pageTitle    = 'Data Mitsubishi';
$pageSubtitle = 'Kelola koleksi kendaraan Mitsubishi';

$action  = sanitize($_GET['action'] ?? 'list');
$editId  = (int)($_GET['id'] ?? 0);
$success = '';
$error   = '';

// HAPUS
if ($action === 'hapus' && $editId) {
    // Hapus file gambar jika ada
    $row = $conn->query("SELECT gambar FROM mobil WHERE id=$editId")->fetch_assoc();
    if ($row && $row['gambar'] && file_exists('../' . $row['gambar'])) {
        unlink('../' . $row['gambar']);
    }
    $conn->query("DELETE FROM mobil WHERE id=$editId");
    redirect('mobil.php?success=hapus');
}

// TOGGLE STATUS
if ($action === 'toggle' && $editId) {
    $conn->query("UPDATE mobil SET status = CASE WHEN status='aktif' THEN 'nonaktif' ELSE 'aktif' END WHERE id=$editId");
    redirect('mobil.php');
}

// TOGGLE FEATURED
if ($action === 'featured' && $editId) {
    $conn->query("UPDATE mobil SET featured = CASE WHEN featured=1 THEN 0 ELSE 1 END WHERE id=$editId");
    redirect('mobil.php');
}

// SIMPAN (tambah/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = sanitize($_POST['nama_mobil'] ?? '');
    $tipe       = sanitize($_POST['tipe'] ?? '');
    $tahun      = (int)($_POST['tahun'] ?? date('Y'));
    $warna      = sanitize($_POST['warna'] ?? '');
    $mesin      = sanitize($_POST['mesin'] ?? '');
    $transmisi  = sanitize($_POST['transmisi'] ?? '');
    $bbkar      = sanitize($_POST['bahan_bakar'] ?? '');
    $harga      = (int)preg_replace('/\D/', '', $_POST['harga'] ?? '0');
    $stok       = (int)($_POST['stok'] ?? 0);
    $deskripsi  = sanitize($_POST['deskripsi'] ?? '');
    $badge      = sanitize($_POST['badge'] ?? '');
    $featured   = isset($_POST['featured']) ? 1 : 0;
    $status     = sanitize($_POST['status'] ?? 'aktif');
    $postId     = (int)($_POST['id'] ?? 0);

    // Handle upload gambar
    $gambar = sanitize($_POST['gambar_lama'] ?? '');
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['gambar'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','gif','webp'];
        $maxSize  = 5 * 1024 * 1024; // 5MB

        if (!in_array($ext, $allowed)) {
            $error = 'Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.';
        } elseif ($file['size'] > $maxSize) {
            $error = 'Ukuran gambar terlalu besar. Maksimal 5MB.';
        } else {
            $uploadDir = '../assets/uploads/mobil/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $newName = 'mobil_' . time() . '_' . uniqid() . '.' . $ext;
            $destPath = $uploadDir . $newName;
            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                // Hapus gambar lama jika ada
                $old = sanitize($_POST['gambar_lama'] ?? '');
                if ($old && strpos($old, 'assets/uploads/') !== false && file_exists('../' . $old)) {
                    unlink('../' . $old);
                }
                $gambar = 'assets/uploads/mobil/' . $newName;
            } else {
                $error = 'Gagal menyimpan gambar. Periksa permission folder uploads.';
            }
        }
    }

    if (!$error) {
        if (!$nama || !$tipe || !$harga) {
            $error = 'Nama, tipe, dan harga wajib diisi.';
        } else {
            if ($postId) {
                $stmt = $conn->prepare("UPDATE mobil SET nama_mobil=?,tipe=?,tahun=?,warna=?,mesin=?,transmisi=?,bahan_bakar=?,harga=?,stok=?,gambar=?,deskripsi=?,badge=?,featured=?,status=? WHERE id=?");
                $stmt->bind_param('ssissssiisssisi',$nama,$tipe,$tahun,$warna,$mesin,$transmisi,$bbkar,$harga,$stok,$gambar,$deskripsi,$badge,$featured,$status,$postId);
            } else {
                $stmt = $conn->prepare("INSERT INTO mobil (nama_mobil,tipe,tahun,warna,mesin,transmisi,bahan_bakar,harga,stok,gambar,deskripsi,badge,featured,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param('ssissssiisssis',$nama,$tipe,$tahun,$warna,$mesin,$transmisi,$bbkar,$harga,$stok,$gambar,$deskripsi,$badge,$featured,$status);
            }
            if ($stmt->execute()) {
                redirect('mobil.php?success=' . ($postId ? 'edit' : 'tambah'));
            } else {
                $error = 'Terjadi kesalahan: ' . $conn->error;
            }
        }
    }
}

// Ambil data untuk edit
$editData = null;
if (($action === 'edit' || $action === 'tambah') && $editId) {
    $editData = $conn->query("SELECT * FROM mobil WHERE id=$editId")->fetch_assoc();
}

// Flash messages
$successMsg = match($_GET['success'] ?? '') {
    'tambah' => 'Mitsubishi berhasil ditambahkan.',
    'edit'   => 'Mitsubishi berhasil diperbarui.',
    'hapus'  => 'Mitsubishi berhasil dihapus.',
    default  => ''
};

// Filter & list
$search  = sanitize($_GET['q'] ?? '');
$ftipe   = sanitize($_GET['tipe'] ?? '');
$where   = [];
$params  = [];
$types   = '';
if ($search) { $where[] = "nama_mobil LIKE ?"; $params[] = "%$search%"; $types .= 's'; }
if ($ftipe)  { $where[] = "tipe = ?"; $params[] = $ftipe; $types .= 's'; }
$wsql  = $where ? 'WHERE '.implode(' AND ',$where) : '';
$page  = max(1,(int)($_GET['page']??1));
$limit = 10; $offset = ($page-1)*$limit;
$total = $conn->query("SELECT COUNT(*) c FROM mobil $wsql")->fetch_assoc()['c'];
$pages = ceil($total/$limit);

if ($types) {
    $stmt2 = $conn->prepare("SELECT * FROM mobil $wsql ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt2->bind_param($types.'ii', ...[...$params, $limit, $offset]);
    $stmt2->execute();
    $mobils = $stmt2->get_result();
} else {
    $mobils = $conn->query("SELECT * FROM mobil ORDER BY id DESC LIMIT $limit OFFSET $offset");
}

$tipeList = $conn->query("SELECT DISTINCT tipe FROM mobil ORDER BY tipe");
?>
<?php include 'includes/header.php'; ?>
<style>
  .car-thumb{width:52px;height:40px;border-radius:8px;object-fit:cover}
  .search-bar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px}
  .search-bar input,.search-bar select{padding:9px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:13px;font-family:'Poppins',sans-serif;background:white;color:var(--dark)}
  .search-bar input:focus,.search-bar select:focus{outline:none;border-color:var(--primary)}
  .pagination{display:flex;gap:6px;flex-wrap:wrap;margin-top:18px}
  .pagination a,.pagination span{padding:7px 13px;border-radius:8px;font-size:13px;text-decoration:none;transition:.2s}
  .pagination a{background:white;color:var(--dark);border:1.5px solid #e5e7eb}
  .pagination a:hover{border-color:var(--primary);color:var(--primary)}
  .pagination .cur{background:var(--primary);color:white;border-color:var(--primary)}
  .pagination .dis{background:#f3f4f6;color:#d1d5db;cursor:not-allowed}
  .upload-area{border:2px dashed #e5e7eb;border-radius:14px;padding:20px;text-align:center;cursor:pointer;transition:.3s;background:#fafafa;position:relative}
  .upload-area:hover{border-color:var(--primary);background:#fff5f5}
  .upload-area input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
  .upload-area i{font-size:28px;color:#d1d5db;margin-bottom:8px;display:block}
  .upload-area p{font-size:13px;color:#6b7280;margin:0}
  .upload-area span{font-size:12px;color:#9ca3af}
  .preview-img{width:100%;max-height:200px;object-fit:cover;border-radius:10px;margin-top:10px;display:none}
  .preview-wrap{position:relative;display:none}
  .preview-wrap img{width:100%;max-height:200px;object-fit:cover;border-radius:10px}
  .preview-wrap .remove-img{position:absolute;top:8px;right:8px;background:rgba(220,38,38,.9);color:white;border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center}
</style>

<?php if ($successMsg): ?>
<div class="alert-a alert-success-a"><i class="fa-solid fa-circle-check"></i><?= $successMsg ?></div>
<?php endif; ?>

<?php if ($action === 'tambah' || $action === 'edit'): ?>
<!-- FORM TAMBAH/EDIT -->
<div class="form-card">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 style="font-size:18px;color:var(--dark)"><?= $action==='edit' ? 'Edit' : 'Tambah' ?> Mitsubishi</h2>
    <a href="mobil.php" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>
  <?php if ($error): ?><div class="alert-a alert-error-a"><i class="fa-solid fa-circle-exclamation"></i><?= $error ?></div><?php endif; ?>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $editData['id'] ?? 0 ?>">
    <input type="hidden" name="gambar_lama" value="<?= sanitize($editData['gambar'] ?? '') ?>">
    <div class="form-row-a">
      <div class="form-group-a">
        <label>Nama Mitsubishi *</label>
        <input type="text" name="nama_mobil" placeholder="cth: Mitsubishi Xpander" required value="<?= sanitize($editData['nama_mobil'] ?? $_POST['nama_mobil'] ?? '') ?>">
      </div>
      <div class="form-group-a">
        <label>Tipe Kendaraan *</label>
        <select name="tipe" required>
          <option value="">— Pilih Tipe —</option>
          <?php foreach (['SUV','MPV','Hatchback','Sedan','Pick Up','Van','Truck'] as $t): ?>
          <option value="<?= $t ?>" <?= ($editData['tipe']??'')===$t?'selected':'' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row-a">
      <div class="form-group-a">
        <label>Tahun</label>
        <input type="number" name="tahun" min="2000" max="<?= date('Y')+1 ?>" value="<?= $editData['tahun'] ?? date('Y') ?>">
      </div>
      <div class="form-group-a">
        <label>Warna</label>
        <input type="text" name="warna" placeholder="cth: Putih Pearl" value="<?= sanitize($editData['warna'] ?? '') ?>">
      </div>
    </div>
    <div class="form-group-a">
      <label>Mesin</label>
      <input type="text" name="mesin" placeholder="cth: 1.5L MIVEC" value="<?= sanitize($editData['mesin'] ?? '') ?>">
    </div>
    <div class="form-row-a">
      <div class="form-group-a">
        <label>Transmisi</label>
        <select name="transmisi">
          <?php foreach (['Automatic','Manual','CVT'] as $tr): ?>
          <option value="<?= $tr ?>" <?= ($editData['transmisi']??'')===$tr?'selected':'' ?>><?= $tr ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group-a">
        <label>Bahan Bakar</label>
        <select name="bahan_bakar">
          <?php foreach (['Bensin','Solar','Hybrid','Listrik'] as $bb): ?>
          <option value="<?= $bb ?>" <?= ($editData['bahan_bakar']??'')===$bb?'selected':'' ?>><?= $bb ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row-a">
      <div class="form-group-a">
        <label>Harga (Rp) *</label>
        <input type="number" name="harga" placeholder="cth: 298000000" required value="<?= $editData['harga'] ?? '' ?>">
      </div>
      <div class="form-group-a">
        <label>Stok (unit)</label>
        <input type="number" name="stok" min="0" value="<?= $editData['stok'] ?? 0 ?>">
      </div>
    </div>

    <!-- UPLOAD GAMBAR -->
    <div class="form-group-a">
      <label>Foto Mobil <span style="color:#9ca3af;font-weight:400">(JPG, PNG, WEBP, GIF — Maks. 5MB)</span></label>

      <?php
        $gambarLama = $editData['gambar'] ?? '';
        $isLocal = $gambarLama && strpos($gambarLama, 'http') !== 0;
        $isUrl   = $gambarLama && strpos($gambarLama, 'http') === 0;
      ?>

      <?php if ($gambarLama): ?>
      <div class="preview-wrap" id="previewWrap" style="display:block;margin-bottom:12px">
        <img id="previewImg"
          src="<?= $isLocal ? '../' . sanitize($gambarLama) : sanitize($gambarLama) ?>"
          alt="Preview"
          onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22120%22><rect fill=%22%23f1f5f9%22 width=%22200%22 height=%22120%22/><text x=%2250%%22 y=%2250%%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 dy=%22.3em%22>No Image</text></svg>'">
        <button type="button" class="remove-img" onclick="removePreview()" title="Hapus foto"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <?php else: ?>
      <div class="preview-wrap" id="previewWrap">
        <img id="previewImg" src="" alt="Preview">
        <button type="button" class="remove-img" onclick="removePreview()" title="Hapus foto"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <?php endif; ?>

      <div class="upload-area" id="uploadArea" <?= $gambarLama ? 'style="display:none"' : '' ?>>
        <input type="file" name="gambar" id="gambarFile" accept="image/*" onchange="previewImage(this)">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        <p><strong>Klik untuk upload foto</strong> atau drag & drop ke sini</p>
        <span>Format: JPG, PNG, WEBP, GIF • Ukuran maks: 5MB</span>
      </div>

      <?php if ($isUrl): ?>
      <p style="font-size:12px;color:#f59e0b;margin-top:8px"><i class="fa-solid fa-circle-info"></i> Foto saat ini menggunakan URL eksternal. Upload foto baru untuk menggantinya dengan file lokal.</p>
      <?php endif; ?>
    </div>

    <div class="form-group-a">
      <label>Deskripsi</label>
      <textarea name="deskripsi" rows="4" placeholder="Deskripsi singkat Mitsubishi..."><?= sanitize($editData['deskripsi'] ?? '') ?></textarea>
    </div>
    <div class="form-row-a">
      <div class="form-group-a">
        <label>Badge (opsional)</label>
        <input type="text" name="badge" placeholder="cth: BEST SELLER, NEW 2024" value="<?= sanitize($editData['badge'] ?? '') ?>">
      </div>
      <div class="form-group-a">
        <label>Status</label>
        <select name="status">
          <option value="aktif" <?= ($editData['status']??'aktif')==='aktif'?'selected':'' ?>>Aktif</option>
          <option value="nonaktif" <?= ($editData['status']??'')==='nonaktif'?'selected':'' ?>>Nonaktif</option>
        </select>
      </div>
    </div>
    <div class="form-group-a" style="display:flex;align-items:center;gap:12px">
      <input type="checkbox" name="featured" id="feat" style="width:auto" <?= ($editData['featured']??0)?'checked':'' ?>>
      <label for="feat" style="margin:0;cursor:pointer">Tampilkan di Halaman Utama (Featured)</label>
    </div>
    <div style="display:flex;gap:12px;margin-top:8px">
      <button type="submit" class="btn-admin btn-primary-a"><i class="fa-solid fa-save"></i> Simpan Mitsubishi</button>
      <a href="mobil.php" class="btn-admin btn-outline">Batal</a>
    </div>
  </form>
</div>

<script>
function previewImage(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    // Validasi ukuran di sisi client
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran file terlalu besar! Maksimal 5MB.');
      input.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
      const wrap = document.getElementById('previewWrap');
      const img  = document.getElementById('previewImg');
      img.src = e.target.result;
      wrap.style.display = 'block';
      document.getElementById('uploadArea').style.display = 'none';
    };
    reader.readAsDataURL(file);
  }
}

function removePreview() {
  const wrap  = document.getElementById('previewWrap');
  const input = document.getElementById('gambarFile');
  const lama  = document.querySelector('input[name=gambar_lama]');
  wrap.style.display = 'none';
  document.getElementById('uploadArea').style.display = 'block';
  if (input) { input.value = ''; }
  if (lama)  { lama.value = ''; }
}

// Drag & Drop
const uploadArea = document.getElementById('uploadArea');
if (uploadArea) {
  uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.style.borderColor = 'var(--primary)'; });
  uploadArea.addEventListener('dragleave', () => { uploadArea.style.borderColor = '#e5e7eb'; });
  uploadArea.addEventListener('drop', e => {
    e.preventDefault();
    uploadArea.style.borderColor = '#e5e7eb';
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
      const input = document.getElementById('gambarFile');
      const dt = new DataTransfer();
      dt.items.add(file);
      input.files = dt.files;
      previewImage(input);
    }
  });
}
</script>

<?php else: ?>
<!-- DAFTAR MOBIL -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
  <div>
    <span style="font-size:14px;color:var(--gray)">Total: <strong style="color:var(--dark)"><?= $total ?></strong> Mitsubishi</span>
  </div>
  <a href="mobil.php?action=tambah" class="btn-admin btn-primary-a"><i class="fa-solid fa-plus"></i> Tambah Mitsubishi</a>
</div>

<form action="mobil.php" method="GET" class="search-bar">
  <input type="text" name="q" placeholder="Cari nama mobil..." value="<?= $search ?>">
  <select name="tipe">
    <option value="">Semua Tipe</option>
    <?php $tipeList->data_seek(0); while ($t=$tipeList->fetch_assoc()): ?>
    <option value="<?= $t['tipe'] ?>" <?= $ftipe===$t['tipe']?'selected':'' ?>><?= $t['tipe'] ?></option>
    <?php endwhile; ?>
  </select>
  <button type="submit" class="btn-admin btn-primary-a"><i class="fa-solid fa-search"></i> Cari</button>
  <a href="mobil.php" class="btn-admin btn-outline"><i class="fa-solid fa-rotate-left"></i> Reset</a>
</form>

<div class="card">
  <div class="card-body">
    <table>
      <thead>
        <tr><th>Foto</th><th>Nama Mitsubishi</th><th>Tipe</th><th>Harga</th><th>Stok</th><th>Featured</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php while ($m = $mobils->fetch_assoc()): ?>
        <?php
          $imgSrc = $m['gambar'];
          // Jika path lokal (bukan URL), tambahkan prefix ../
          if ($imgSrc && strpos($imgSrc, 'http') !== 0) {
            $imgSrc = '../' . $imgSrc;
          }
        ?>
        <tr>
          <td><img class="car-thumb" src="<?= sanitize($imgSrc) ?>" alt="" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2252%22 height=%2240%22><rect fill=%22%23f1f5f9%22 width=%2252%22 height=%2240%22/></svg>'"></td>
          <td>
            <div style="font-weight:600"><?= sanitize($m['nama_mobil']) ?></div>
            <div style="font-size:11px;color:var(--gray)"><?= $m['tahun'] ?> · <?= $m['transmisi'] ?></div>
          </td>
          <td><span class="badge badge-info"><?= sanitize($m['tipe']) ?></span></td>
          <td style="font-weight:700;color:var(--primary);font-size:13px"><?= rupiah($m['harga']) ?></td>
          <td><?= $m['stok'] ?> unit</td>
          <td>
            <a href="mobil.php?action=featured&id=<?= $m['id'] ?>" class="badge <?= $m['featured']?'badge-success':'badge-secondary' ?>" style="text-decoration:none">
              <?= $m['featured'] ? '<i class="fa-solid fa-star"></i> Ya' : '<i class="fa-regular fa-star"></i> Tidak' ?>
            </a>
          </td>
          <td>
            <a href="mobil.php?action=toggle&id=<?= $m['id'] ?>" class="badge <?= $m['status']==='aktif'?'badge-success':'badge-danger' ?>" style="text-decoration:none">
              <?= $m['status']==='aktif' ? 'Aktif' : 'Nonaktif' ?>
            </a>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="mobil.php?action=edit&id=<?= $m['id'] ?>" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-pen"></i></a>
              <a href="mobil.php?action=hapus&id=<?= $m['id'] ?>" class="btn-admin btn-danger btn-sm" onclick="return confirm('Hapus Mitsubishi ini?')"><i class="fa-solid fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php if ($pages > 1): ?>
<div class="pagination">
  <?php $q2 = http_build_query(['q'=>$search,'tipe'=>$ftipe]); ?>
  <?php if ($page>1): ?><a href="?<?=$q2?>&page=<?=$page-1?>"><i class="fa-solid fa-chevron-left"></i></a><?php else: ?><span class="dis"><i class="fa-solid fa-chevron-left"></i></span><?php endif; ?>
  <?php for($i=max(1,$page-2);$i<=min($pages,$page+2);$i++): ?>
  <a href="?<?=$q2?>&page=<?=$i?>" class="<?=$i===$page?'cur':''?>"><?=$i?></a>
  <?php endfor; ?>
  <?php if ($page<$pages): ?><a href="?<?=$q2?>&page=<?=$page+1?>"><i class="fa-solid fa-chevron-right"></i></a><?php else: ?><span class="dis"><i class="fa-solid fa-chevron-right"></i></span><?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
