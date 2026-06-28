<?php
require_once '../includes/config.php';
requireAdmin();

$pageTitle    = 'Testimonial';
$pageSubtitle = 'Kelola ulasan pelanggan Mitsubishi';

$action = sanitize($_GET['action'] ?? 'list');
$id     = (int)($_GET['id'] ?? 0);

if ($action === 'hapus' && $id) { $conn->query("DELETE FROM testimonial WHERE id=$id"); redirect('testimonial.php?success=hapus'); }
if ($action === 'toggle' && $id) { $conn->query("UPDATE testimonial SET status=CASE WHEN status='aktif' THEN 'nonaktif' ELSE 'aktif' END WHERE id=$id"); redirect('testimonial.php'); }

$editData = null;
if (($action==='edit') && $id) $editData = $conn->query("SELECT * FROM testimonial WHERE id=$id")->fetch_assoc();

$success = ''; $error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nama   = sanitize($_POST['nama']??'');
    $kota   = sanitize($_POST['kota']??'');
    $teks   = sanitize($_POST['teks']??'');
    $rating = (int)($_POST['rating']??5);
    $status = sanitize($_POST['status']??'aktif');
    $pid    = (int)($_POST['id']??0);
    if (!$nama||!$teks) { $error='Nama dan teks wajib diisi.'; }
    else {
        if ($pid) { $st=$conn->prepare("UPDATE testimonial SET nama=?,kota=?,teks=?,rating=?,status=? WHERE id=?"); $st->bind_param('sssisi',$nama,$kota,$teks,$rating,$status,$pid); }
        else { $st=$conn->prepare("INSERT INTO testimonial (nama,kota,teks,rating,status) VALUES (?,?,?,?,?)"); $st->bind_param('sssis',$nama,$kota,$teks,$rating,$status); }
        if ($st->execute()) redirect('testimonial.php?success='.($pid?'edit':'tambah'));
        else $error='Terjadi kesalahan.';
    }
}

$flashMsg = match($_GET['success']??'') { 'tambah'=>'Testimonial berhasil ditambahkan.','edit'=>'Testimonial berhasil diperbarui.','hapus'=>'Testimonial berhasil dihapus.',default=>'' };
$testimoni = $conn->query("SELECT * FROM testimonial ORDER BY id DESC");
?>
<?php include 'includes/header.php'; ?>

<?php if ($flashMsg): ?><div class="alert-a alert-success-a"><i class="fa-solid fa-circle-check"></i><?=$flashMsg?></div><?php endif; ?>
<?php if ($action==='tambah'||$action==='edit'): ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <h2 style="font-size:18px"><?=$action==='edit'?'Edit':'Tambah'?> Testimonial</h2>
  <a href="testimonial.php" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<?php if ($error): ?><div class="alert-a alert-error-a"><i class="fa-solid fa-circle-exclamation"></i><?=$error?></div><?php endif; ?>
<div class="form-card" style="max-width:600px">
  <form method="POST">
    <input type="hidden" name="id" value="<?=$editData['id']??0?>">
    <div class="form-row-a">
      <div class="form-group-a"><label>Nama *</label><input type="text" name="nama" required value="<?=sanitize($editData['nama']??'')?>"></div>
      <div class="form-group-a"><label>Kota</label><input type="text" name="kota" value="<?=sanitize($editData['kota']??'')?>"></div>
    </div>
    <div class="form-group-a"><label>Teks Testimonial *</label><textarea name="teks" rows="4" required><?=sanitize($editData['teks']??'')?></textarea></div>
    <div class="form-row-a">
      <div class="form-group-a">
        <label>Rating</label>
        <select name="rating">
          <?php for($r=5;$r>=1;$r--): ?>
          <option value="<?=$r?>" <?=($editData['rating']??5)==$r?'selected':''?>><?=$r?> Bintang</option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group-a">
        <label>Status</label>
        <select name="status">
          <option value="aktif" <?=($editData['status']??'aktif')==='aktif'?'selected':''?>>Aktif</option>
          <option value="nonaktif" <?=($editData['status']??'')==='nonaktif'?'selected':''?>>Nonaktif</option>
        </select>
      </div>
    </div>
    <div style="display:flex;gap:12px"><button type="submit" class="btn-admin btn-primary-a"><i class="fa-solid fa-save"></i> Simpan</button><a href="testimonial.php" class="btn-admin btn-outline">Batal</a></div>
  </form>
</div>

<?php else: ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <span style="font-size:14px;color:var(--gray)">Total: <strong><?=$testimoni->num_rows?></strong> testimonial</span>
  <a href="testimonial.php?action=tambah" class="btn-admin btn-primary-a"><i class="fa-solid fa-plus"></i> Tambah Testimonial</a>
</div>
<div class="card">
  <div class="card-body">
    <table>
      <thead><tr><th>Nama</th><th>Kota</th><th>Teks</th><th>Rating</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php while ($t=$testimoni->fetch_assoc()): ?>
        <tr>
          <td style="font-weight:600"><?=sanitize($t['nama'])?></td>
          <td><?=sanitize($t['kota'])?:'-'?></td>
          <td style="font-size:13px;max-width:260px;color:var(--gray)"><?=substr(sanitize($t['teks']),0,80)?>...</td>
          <td><span style="color:#f59e0b"><?=str_repeat('★',(int)$t['rating'])?></span></td>
          <td>
            <a href="testimonial.php?action=toggle&id=<?=$t['id']?>" class="badge <?=$t['status']==='aktif'?'badge-success':'badge-danger'?>" style="text-decoration:none">
              <?=$t['status']==='aktif'?'Aktif':'Nonaktif'?>
            </a>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="testimonial.php?action=edit&id=<?=$t['id']?>" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-pen"></i></a>
              <a href="testimonial.php?action=hapus&id=<?=$t['id']?>" class="btn-admin btn-danger btn-sm" onclick="return confirm('Hapus?')"><i class="fa-solid fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
