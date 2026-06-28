<?php
require_once '../includes/config.php';
requireAdmin();

$pageTitle    = 'Dashboard';
$pageSubtitle = 'Ringkasan data dan statistik dealer';

// Stats
$totalMobil    = $conn->query("SELECT COUNT(*) c FROM mobil WHERE status='aktif'")->fetch_assoc()['c'];
$totalBooking  = $conn->query("SELECT COUNT(*) c FROM booking")->fetch_assoc()['c'];
$pendingBooking= $conn->query("SELECT COUNT(*) c FROM booking WHERE status='pending'")->fetch_assoc()['c'];
$totalKontak   = $conn->query("SELECT COUNT(*) c FROM kontak WHERE status='belum_dibaca'")->fetch_assoc()['c'];
$totalPendapatan= $conn->query("SELECT SUM(total_harga) s FROM booking WHERE status='selesai'")->fetch_assoc()['s'] ?? 0;

// Booking terbaru
$recentBooking = $conn->query("SELECT b.*, m.nama_mobil FROM booking b JOIN mobil m ON b.mobil_id=m.id ORDER BY b.created_at DESC LIMIT 7");

// Pesan terbaru
$recentKontak  = $conn->query("SELECT * FROM kontak ORDER BY created_at DESC LIMIT 5");

// Chart data - booking per bulan (6 bulan terakhir)
$chartData = [];
for ($i = 5; $i >= 0; $i--) {
    $bln  = date('Y-m', strtotime("-$i months"));
    $res  = $conn->query("SELECT COUNT(*) c FROM booking WHERE DATE_FORMAT(created_at,'%Y-%m')='$bln'")->fetch_assoc();
    $chartData[] = ['bulan' => date('M Y', strtotime("-$i months")), 'total' => (int)$res['c']];
}

// Booking by status
$statusData = $conn->query("SELECT status, COUNT(*) c FROM booking GROUP BY status");
$statusArr  = [];
while ($s = $statusData->fetch_assoc()) $statusArr[$s['status']] = $s['c'];
?>
<?php include 'includes/header.php'; ?>
<style>
  .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px}
  .chart-grid{display:grid;grid-template-columns:2fr 1fr;gap:22px;margin-bottom:28px}
  .chart-card{background:white;border-radius:var(--radius);padding:24px;box-shadow:var(--shadow)}
  .chart-card h3{font-size:15px;font-weight:700;color:var(--dark);margin-bottom:20px;display:flex;align-items:center;gap:8px}
  .chart-card h3 i{color:var(--primary)}
  .bar-chart{display:flex;align-items:flex-end;gap:10px;height:160px}
  .bar-item{flex:1;display:flex;flex-direction:column;align-items:center;gap:6px}
  .bar{width:100%;background:linear-gradient(180deg,#dc2626,#ef4444);border-radius:6px 6px 0 0;transition:height .6s ease;min-height:4px}
  .bar-label{font-size:10px;color:var(--gray);text-align:center}
  .bar-val{font-size:11px;font-weight:700;color:var(--dark)}
  .donut-wrap{display:flex;flex-direction:column;gap:12px;margin-top:8px}
  .donut-item{display:flex;align-items:center;justify-content:space-between;font-size:13px}
  .donut-dot{width:12px;height:12px;border-radius:50%;flex-shrink:0}
  .donut-label{display:flex;align-items:center;gap:8px;color:var(--gray)}
  .donut-val{font-weight:700;color:var(--dark)}
  .tables-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:22px}
  .action-bar{display:flex;gap:10px;flex-wrap:wrap}
  @media(max-width:1200px){.stats-grid{grid-template-columns:repeat(2,1fr)}.chart-grid{grid-template-columns:1fr}.tables-grid{grid-template-columns:1fr}}
  @media(max-width:600px){.stats-grid{grid-template-columns:1fr}}
</style>

<!-- STAT CARDS -->
<div class="stats-grid">
  <div class="stat-card" style="border-left-color:#ef4444">
    <div class="stat-icon" style="background:linear-gradient(135deg,#dc2626,#ef4444)"><i class="fa-solid fa-car-side"></i></div>
    <div class="stat-info"><h3><?= $totalMobil ?></h3><p>Total Mitsubishi Aktif</p></div>
  </div>
  <div class="stat-card" style="border-left-color:#2563eb">
    <div class="stat-icon" style="background:linear-gradient(135deg,#2563eb,#3b82f6)"><i class="fa-solid fa-calendar-check"></i></div>
    <div class="stat-info"><h3><?= $totalBooking ?></h3><p>Total Booking</p></div>
  </div>
  <div class="stat-card" style="border-left-color:#f59e0b">
    <div class="stat-icon" style="background:linear-gradient(135deg,#d97706,#f59e0b)"><i class="fa-solid fa-clock"></i></div>
    <div class="stat-info"><h3><?= $pendingBooking ?></h3><p>Booking Pending</p></div>
  </div>
  <div class="stat-card" style="border-left-color:#16a34a">
    <div class="stat-icon" style="background:linear-gradient(135deg,#16a34a,#22c55e)"><i class="fa-solid fa-money-bill-trend-up"></i></div>
    <div class="stat-info"><h3 style="font-size:18px"><?= $totalPendapatan > 0 ? 'Rp '.number_format($totalPendapatan/1000000,0,',','.').'Jt' : 'Rp 0' ?></h3><p>Pendapatan Selesai</p></div>
  </div>
</div>

<!-- CHARTS -->
<div class="chart-grid">
  <div class="chart-card">
    <h3><i class="fa-solid fa-chart-bar"></i> Booking 6 Bulan Terakhir</h3>
    <?php
    $maxVal = max(array_column($chartData, 'total'), 1);
    ?>
    <div class="bar-chart">
      <?php foreach ($chartData as $c): ?>
      <div class="bar-item">
        <div class="bar-val"><?= $c['total'] ?></div>
        <div class="bar" style="height:<?= max(4, ($c['total']/$maxVal)*130) ?>px"></div>
        <div class="bar-label"><?= $c['bulan'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="chart-card">
    <h3><i class="fa-solid fa-chart-pie"></i> Status Booking</h3>
    <div class="donut-wrap">
      <?php
      $statusCfg = [
        'pending'     => ['#f59e0b','Pending'],
        'dikonfirmasi'=> ['#2563eb','Dikonfirmasi'],
        'selesai'     => ['#16a34a','Selesai'],
        'ditolak'     => ['#dc2626','Ditolak'],
      ];
      $totalB = array_sum($statusArr) ?: 1;
      foreach ($statusCfg as $key => [$color, $label]):
        $cnt = $statusArr[$key] ?? 0;
        $pct = round(($cnt/$totalB)*100);
      ?>
      <div class="donut-item">
        <div class="donut-label"><div class="donut-dot" style="background:<?= $color ?>"></div><?= $label ?></div>
        <div class="donut-val"><?= $cnt ?> <span style="font-size:11px;color:var(--gray)">(<?= $pct ?>%)</span></div>
      </div>
      <div style="background:#f1f5f9;border-radius:4px;height:6px;overflow:hidden;margin-top:-6px">
        <div style="height:100%;width:<?= $pct ?>%;background:<?= $color ?>;border-radius:4px;transition:width .6s ease"></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- TABLES -->
<div class="tables-grid">
  <div class="card">
    <div class="card-header">
      <h3><i class="fa-solid fa-calendar-check"></i> Booking Terbaru</h3>
      <a href="booking.php" class="btn-admin btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="card-body">
      <table>
        <thead><tr><th>Kode</th><th>Nama</th><th>Mitsubishi</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
          <?php while ($b = $recentBooking->fetch_assoc()): ?>
          <tr>
            <td><code style="font-size:11px;background:#f1f5f9;padding:3px 7px;border-radius:6px"><?= $b['kode_booking'] ?></code></td>
            <td><?= sanitize($b['nama']) ?></td>
            <td style="font-size:12px;color:var(--gray)"><?= sanitize($b['nama_mobil']) ?></td>
            <td style="font-weight:700;color:var(--primary);font-size:13px"><?= rupiah($b['total_harga']) ?></td>
            <td>
              <?php
              $bc = ['pending'=>'warning','dikonfirmasi'=>'info','selesai'=>'success','ditolak'=>'danger'];
              $bl = ['pending'=>'Pending','dikonfirmasi'=>'Konfirmasi','selesai'=>'Selesai','ditolak'=>'Ditolak'];
              ?>
              <span class="badge badge-<?= $bc[$b['status']] ?? 'secondary' ?>"><?= $bl[$b['status']] ?? $b['status'] ?></span>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3><i class="fa-solid fa-envelope"></i> Pesan Terbaru</h3>
      <a href="kontak.php" class="btn-admin btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="card-body">
      <table>
        <thead><tr><th>Nama</th><th>Subjek</th><th>Waktu</th></tr></thead>
        <tbody>
          <?php while ($k = $recentKontak->fetch_assoc()): ?>
          <tr>
            <td>
              <div style="font-weight:600"><?= sanitize($k['nama']) ?></div>
              <div style="font-size:11px;color:var(--gray)"><?= sanitize($k['email']) ?></div>
            </td>
            <td style="font-size:12px"><?= sanitize($k['subjek'] ?: '-') ?></td>
            <td style="font-size:11px;color:var(--gray)"><?= timeAgo($k['created_at']) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ACTION BUTTONS -->
<div class="action-bar" style="margin-top:24px">
  <a href="mobil.php?action=tambah" class="btn-admin btn-primary-a"><i class="fa-solid fa-plus"></i> Tambah Mitsubishi</a>
  <a href="booking.php" class="btn-admin btn-outline"><i class="fa-solid fa-calendar-check"></i> Kelola Booking</a>
  <a href="kontak.php" class="btn-admin btn-outline"><i class="fa-solid fa-envelope"></i> Baca Pesan <?php if ($totalKontak): ?><span style="background:#ef4444;color:white;padding:1px 7px;border-radius:50px;font-size:10px"><?= $totalKontak ?></span><?php endif; ?></a>
  <a href="pengaturan.php" class="btn-admin btn-outline"><i class="fa-solid fa-gear"></i> Pengaturan</a>
</div>

<?php include 'includes/footer.php'; ?>
