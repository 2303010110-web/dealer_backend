<?php
require_once '../includes/config.php';
requireAdmin();

$pageTitle    = 'Pesan Masuk';
$pageSubtitle = 'Kelola pesan dari pengunjung website';

$action = sanitize($_GET['action'] ?? 'list');
$id     = (int)($_GET['id'] ?? 0);

if ($action === 'hapus' && $id) { $conn->query("DELETE FROM kontak WHERE id=$id"); redirect('kontak.php?success=hapus'); }
if ($action === 'baca' && $id)  { $conn->query("UPDATE kontak SET status='sudah_dibaca' WHERE id=$id"); }

$detailData = null;
if ($action === 'detail' && $id) {
    $conn->query("UPDATE kontak SET status='sudah_dibaca' WHERE id=$id");
    $detailData = $conn->query("SELECT * FROM kontak WHERE id=$id")->fetch_assoc();
}

$successMsg = ($_GET['success'] ?? '') === 'hapus' ? 'Pesan berhasil dihapus.' : '';
$fstatus = sanitize($_GET['status'] ?? '');
$search  = sanitize($_GET['q'] ?? '');
$where   = []; $params = []; $types = '';
if ($search)  { $where[] = "(nama LIKE ? OR email LIKE ? OR subjek LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; $types .= 'sss'; }
if ($fstatus) { $where[] = "status=?"; $params[] = $fstatus; $types .= 's'; }
$wsql  = $where ? 'WHERE '.implode(' AND ',$where) : '';
$page  = max(1,(int)($_GET['page']??1));
$limit = 10; $offset = ($page-1)*$limit;
if ($types) {
    $sc = $conn->prepare("SELECT COUNT(*) c FROM kontak $wsql");
    $sc->bind_param($types, ...$params);
    $sc->execute();
    $total = $sc->get_result()->fetch_assoc()['c'];
} else {
    $total = $conn->query("SELECT COUNT(*) c FROM kontak $wsql")->fetch_assoc()['c'];
}
$pages = ceil($total / $limit);

$stmt2 = $conn->prepare("SELECT * FROM kontak $wsql ORDER BY created_at DESC LIMIT ? OFFSET ?");
$allParams = array_merge($params, [$limit, $offset]);
$stmt2->bind_param($types . 'ii', ...$allParams);
$stmt2->execute();
$kontaks = $stmt2->get_result();
?>
<?php include 'includes/header.php'; ?>

<?php if ($successMsg): ?><div class="alert-a alert-success-a"><i class="fa-solid fa-circle-check"></i><?=$successMsg?></div><?php endif; ?>

<?php if ($detailData): ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px">
  <h2 style="font-size:18px">Detail Pesan</h2>
  <div style="display:flex;gap:8px">
    <a href="kontak.php?action=hapus&id=<?=$detailData['id']?>" class="btn-admin btn-danger btn-sm" onclick="return confirm('Hapus pesan ini?')"><i class="fa-solid fa-trash"></i> Hapus</a>
    <a href="kontak.php" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>
</div>
<div class="form-card" style="max-width:640px">
  <?php foreach ([
    ['fa-user','Nama',$detailData['nama']],
    ['fa-envelope','Email',$detailData['email']],
    ['fa-phone','No. HP',$detailData['no_hp']?:'-'],
    ['fa-tag','Subjek',$detailData['subjek']?:'-'],
    ['fa-clock','Dikirim',date('d M Y H:i',strtotime($detailData['created_at']))],
  ] as [$ic,$lbl,$val]): ?>
  <div style="display:flex;gap:14px;padding:12px 0;border-bottom:1px solid var(--lighter)">
    <div style="width:36px;height:36px;background:rgba(220,38,38,.1);border-radius:9px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:14px;flex-shrink:0"><i class="fa-solid <?=$ic?>"></i></div>
    <div><div style="font-size:11px;color:var(--gray)"><?=$lbl?></div><div style="font-size:14px;font-weight:500"><?=sanitize((string)$val)?></div></div>
  </div>
  <?php endforeach; ?>
  <div style="margin-top:18px">
    <div style="font-size:12px;color:var(--gray);margin-bottom:8px">PESAN</div>
    <div style="background:var(--lighter);border-radius:14px;padding:18px;font-size:14px;line-height:1.8;color:var(--dark)"><?=nl2br(sanitize($detailData['pesan']))?></div>
  </div>
  <div style="margin-top:18px">
    <a href="mailto:<?=sanitize($detailData['email'])?>" class="btn-admin btn-primary-a"><i class="fa-solid fa-reply"></i> Balas via Email</a>
  </div>
</div>

<?php else: ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:12px">
  <span style="font-size:14px;color:var(--gray)">Total: <strong style="color:var(--dark)"><?=$total?></strong> pesan</span>
  <div style="display:flex;gap:8px">
    <a href="kontak.php?status=belum_dibaca" class="btn-admin btn-outline btn-sm <?=$fstatus==='belum_dibaca'?'btn-primary-a':''?>">Belum Dibaca</a>
    <a href="kontak.php?status=sudah_dibaca" class="btn-admin btn-outline btn-sm <?=$fstatus==='sudah_dibaca'?'btn-primary-a':''?>">Sudah Dibaca</a>
    <a href="kontak.php" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-rotate-left"></i></a>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <table>
      <thead><tr><th>Nama</th><th>Email</th><th>Subjek</th><th>Waktu</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php while ($k=$kontaks->fetch_assoc()): ?>
        <tr style="<?=$k['status']==='belum_dibaca'?'font-weight:600;background:#fffbeb':''?>">
          <td><?=sanitize($k['nama'])?></td>
          <td style="font-size:13px"><?=sanitize($k['email'])?></td>
          <td style="font-size:13px"><?=sanitize($k['subjek']?:'-')?></td>
          <td style="font-size:12px;color:var(--gray)"><?=timeAgo($k['created_at'])?></td>
          <td><span class="badge <?=$k['status']==='belum_dibaca'?'badge-warning':'badge-success'?>"><?=$k['status']==='belum_dibaca'?'Baru':'Dibaca'?></span></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="kontak.php?action=detail&id=<?=$k['id']?>" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-eye"></i></a>
              <a href="kontak.php?action=hapus&id=<?=$k['id']?>" class="btn-admin btn-danger btn-sm" onclick="return confirm('Hapus?')"><i class="fa-solid fa-trash"></i></a>
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
