<?php
require_once '../includes/config.php';
requireAdmin();

$pageTitle    = 'Data Booking';
$pageSubtitle = 'Kelola semua pesanan kendaraan Mitsubishi';

$action = sanitize($_GET['action'] ?? 'list');
$id     = (int)($_GET['id'] ?? 0);

// Update status
if ($action === 'status' && $id && isset($_GET['s'])) {
    $s = sanitize($_GET['s']);
    if (in_array($s, ['pending','dikonfirmasi','ditolak','selesai'])) {
        $conn->query("UPDATE booking SET status='$s' WHERE id=$id");
    }
    redirect('booking.php');
}

// Hapus
if ($action === 'hapus' && $id) {
    $conn->query("DELETE FROM booking WHERE id=$id");
    redirect('booking.php?success=hapus');
}

// View detail
$detailData = null;
if ($action === 'detail' && $id) {
    $stmtD = $conn->prepare("SELECT b.*, m.nama_mobil, m.gambar, m.tipe FROM booking b JOIN mobil m ON b.mobil_id=m.id WHERE b.id=?");
    $stmtD->bind_param('i', $id);
    $stmtD->execute();
    $detailData = $stmtD->get_result()->fetch_assoc();
}



// Filter & List
$search  = sanitize($_GET['q'] ?? '');
$fstatus = sanitize($_GET['status'] ?? '');
$where   = [];
$params  = [];
$types   = '';
if ($search)  { $where[] = "(b.nama LIKE ? OR b.kode_booking LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $types .= 'ss'; }
if ($fstatus) { $where[] = "b.status=?"; $params[] = $fstatus; $types .= 's'; }
$wsql  = $where ? 'WHERE '.implode(' AND ',$where) : '';
$page  = max(1,(int)($_GET['page']??1));
$limit = 10; $offset = ($page-1)*$limit;

$cnt   = $conn->prepare("SELECT COUNT(*) c FROM booking b JOIN mobil m ON b.mobil_id=m.id $wsql");
if ($types) $cnt->bind_param($types, ...$params);
$cnt->execute();
$total = $cnt->get_result()->fetch_assoc()['c'];
$pages = ceil($total/$limit);

$stmt2 = $conn->prepare("SELECT b.*, m.nama_mobil FROM booking b JOIN mobil m ON b.mobil_id=m.id $wsql ORDER BY b.created_at DESC LIMIT ? OFFSET ?");
$allParams = array_merge($params, [$limit, $offset]);
$stmt2->bind_param($types.'ii', ...$allParams);
$stmt2->execute();
$bookings = $stmt2->get_result();

$statusCfg = ['pending'=>['warning','Pending'],'dikonfirmasi'=>['info','Dikonfirmasi'],'selesai'=>['success','Selesai'],'ditolak'=>['danger','Ditolak']];
?>
<?php include 'includes/header.php'; ?>

<?php if ($detailData): ?>
<!-- DETAIL BOOKING -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px">
  <h2 style="font-size:18px">Detail Booking: <span style="color:var(--primary)"><?= $detailData['kode_booking'] ?></span></h2>
  <a href="booking.php" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px">
  <div class="form-card">
    <h3 style="font-size:15px;margin-bottom:18px;color:var(--dark)"><i class="fa-solid fa-car" style="color:var(--primary)"></i> Info Mitsubishi</h3>
    <img src="<?= sanitize($detailData['gambar']) ?>" style="width:100%;height:160px;object-fit:cover;border-radius:14px;margin-bottom:16px">
    <p><strong><?= sanitize($detailData['nama_mobil']) ?></strong> (<?= sanitize($detailData['tipe']) ?>)</p>
    <p style="margin-top:8px;font-size:22px;font-weight:800;color:var(--primary)"><?= rupiah($detailData['total_harga']) ?></p>
    <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap">
      <?php foreach (['dikonfirmasi'=>'btn-success','selesai'=>'btn-success','ditolak'=>'btn-danger','pending'=>'btn-outline'] as $s=>$cls): ?>
      <?php if ($detailData['status'] !== $s): ?>
      <a href="booking.php?action=status&id=<?=$detailData['id']?>&s=<?=$s?>" class="btn-admin <?=$cls?> btn-sm">
        <?= ucfirst($s) ?>
      </a>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="form-card">
    <h3 style="font-size:15px;margin-bottom:18px;color:var(--dark)"><i class="fa-solid fa-user" style="color:var(--primary)"></i> Info Pemesan</h3>
    <?php foreach ([
      ['fa-user','Nama', $detailData['nama']],
      ['fa-envelope','Email', $detailData['email']],
      ['fa-phone','No. HP', $detailData['no_hp']],
      ['fa-location-dot','Alamat', $detailData['alamat']],
      ['fa-credit-card','Metode', ucfirst($detailData['metode'])],
      ['fa-calendar','Tgl Booking', date('d M Y', strtotime($detailData['tanggal_booking']))],
      ['fa-hashtag','Jumlah', $detailData['jumlah'].' unit'],
      ['fa-comment','Catatan', $detailData['catatan'] ?: '-'],
    ] as [$ic, $lbl, $val]): ?>
    <div style="display:flex;align-items:flex-start;gap:14px;padding:11px 0;border-bottom:1px solid var(--lighter)">
      <div style="width:34px;height:34px;background:rgba(220,38,38,.1);border-radius:9px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:14px;flex-shrink:0"><i class="fa-solid <?=$ic?>"></i></div>
      <div><div style="font-size:11px;color:var(--gray);margin-bottom:2px"><?=$lbl?></div><div style="font-size:14px;font-weight:500"><?=sanitize((string)$val)?></div></div>
    </div>
    <?php endforeach; ?>
    <div style="margin-top:14px">
      <span class="badge badge-<?=$statusCfg[$detailData['status']][0]?>" style="font-size:13px;padding:6px 16px">
        Status: <?=$statusCfg[$detailData['status']][1]?>
      </span>
    </div>
  </div>
</div>

<?php else: ?>


<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:12px">
  <span style="font-size:14px;color:var(--gray)">Total: <strong style="color:var(--dark)"><?=$total?></strong> booking</span>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <?php foreach ($statusCfg as $s=>[$cls,$lbl]): ?>
    <a href="booking.php?status=<?=$s?>" class="badge badge-<?=$cls?>" style="text-decoration:none;padding:6px 14px;font-size:12px;<?=$fstatus===$s?'border:2px solid currentColor':''?>"><?=$lbl?></a>
    <?php endforeach; ?>
    <a href="booking.php" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-rotate-left"></i></a>
  </div>
</div>

<form action="booking.php" method="GET" style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
  <input type="text" name="q" placeholder="Cari nama / kode booking..." value="<?=$search?>" style="padding:9px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:13px;font-family:'Poppins',sans-serif;background:white">
  <input type="hidden" name="status" value="<?=$fstatus?>">
  <button type="submit" class="btn-admin btn-primary-a btn-sm"><i class="fa-solid fa-search"></i> Cari</button>
</form>

<div class="card">
  <div class="card-body">
    <table>
      <thead><tr><th>Kode</th><th>Pemesan</th><th>Mitsubishi</th><th>Metode</th><th>Total</th><th>Tgl Booking</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php while ($b = $bookings->fetch_assoc()): ?>
        <tr>
          <td><code style="font-size:11px;background:#f1f5f9;padding:3px 7px;border-radius:6px"><?=$b['kode_booking']?></code></td>
          <td>
            <div style="font-weight:600"><?=sanitize($b['nama'])?></div>
            <div style="font-size:11px;color:var(--gray)"><?=sanitize($b['email'])?></div>
          </td>
          <td style="font-size:13px"><?=sanitize($b['nama_mobil'])?></td>
          <td><span class="badge badge-secondary"><?=ucfirst($b['metode'])?></span></td>
          <td style="font-weight:700;color:var(--primary);font-size:13px"><?=rupiah($b['total_harga'])?></td>
          <td style="font-size:12px"><?=date('d M Y',strtotime($b['tanggal_booking']))?></td>
          <td><span class="badge badge-<?=$statusCfg[$b['status']][0]?>"><?=$statusCfg[$b['status']][1]?></span></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="booking.php?action=detail&id=<?=$b['id']?>" class="btn-admin btn-outline btn-sm"><i class="fa-solid fa-eye"></i></a>
              <?php if ($b['status']==='pending'): ?>
              <a href="booking.php?action=status&id=<?=$b['id']?>&s=dikonfirmasi" class="btn-admin btn-success btn-sm" title="Konfirmasi"><i class="fa-solid fa-check"></i></a>
              <a href="booking.php?action=status&id=<?=$b['id']?>&s=ditolak" class="btn-admin btn-danger btn-sm" title="Tolak"><i class="fa-solid fa-xmark"></i></a>
              <?php endif; ?>
              <a href="booking.php?action=hapus&id=<?=$b['id']?>" class="btn-admin btn-danger btn-sm" onclick="return confirm('Hapus booking ini?')"><i class="fa-solid fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php if ($pages > 1): ?>
<div class="pagination" style="margin-top:16px">
  <?php $q2=http_build_query(['q'=>$search,'status'=>$fstatus]); ?>
  <?php if($page>1): ?><a href="?<?=$q2?>&page=<?=$page-1?>"><i class="fa-solid fa-chevron-left"></i></a><?php else: ?><span class="dis"><i class="fa-solid fa-chevron-left"></i></span><?php endif; ?>
  <?php for($i=max(1,$page-2);$i<=min($pages,$page+2);$i++): ?>
  <a href="?<?=$q2?>&page=<?=$i?>" class="<?=$i===$page?'cur':''?>"><?=$i?></a>
  <?php endfor; ?>
  <?php if($page<$pages): ?><a href="?<?=$q2?>&page=<?=$page+1?>"><i class="fa-solid fa-chevron-right"></i></a><?php else: ?><span class="dis"><i class="fa-solid fa-chevron-right"></i></span><?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
