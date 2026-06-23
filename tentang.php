<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Tentang Kami | Dealer Mitsubishi</title>
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
    .about-hero{display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;margin-top:50px}
    .about-img-wrap{position:relative}
    .about-img-wrap img{width:100%;height:460px;object-fit:cover;border-radius:28px;box-shadow:var(--shadow-lg)}
    .about-badge-float{position:absolute;bottom:-22px;right:-22px;background:linear-gradient(135deg,#dc2626,#ef4444);color:white;padding:22px 28px;border-radius:22px;box-shadow:0 12px 30px rgba(220,38,38,.35);text-align:center}
    .about-badge-float .num{font-size:38px;font-weight:800;line-height:1}
    .about-badge-float p{font-size:13px;opacity:.88;margin-top:4px}
    .about-dots{position:absolute;top:-22px;left:-22px;width:90px;height:90px;background-image:radial-gradient(rgba(220,38,38,.3) 2px,transparent 2px);background-size:16px 16px}
    .about-content .subtitle{display:inline-block;background:rgba(220,38,38,.1);color:var(--primary);padding:7px 20px;border-radius:50px;font-size:13px;font-weight:600;margin-bottom:18px;border:1px solid rgba(220,38,38,.2)}
    .about-content h2{font-size:40px;font-weight:800;color:var(--dark);line-height:1.2;margin-bottom:18px}
    .about-content h2 span{color:var(--primary)}
    .about-content p{color:var(--gray);font-size:15px;line-height:1.9;margin-bottom:14px}
    .about-list{list-style:none;margin:18px 0 28px}
    .about-list li{display:flex;align-items:center;gap:12px;color:var(--gray);font-size:15px;margin-bottom:12px}
    .about-list li i{color:var(--primary);font-size:16px;width:22px}
    .values-section{background:linear-gradient(135deg,#111827,#1f2937);border-radius:36px;padding:70px 50px}
    .values-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;margin-top:50px}
    .value-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);border-radius:22px;padding:30px 24px;color:white;text-align:center;transition:.35s}
    .value-card:hover{transform:translateY(-6px);border-color:rgba(220,38,38,.35);background:rgba(220,38,38,.08)}
    .value-icon{width:66px;height:66px;border-radius:20px;background:linear-gradient(135deg,#dc2626,#ef4444);display:flex;align-items:center;justify-content:center;font-size:26px;color:white;margin:0 auto 20px;box-shadow:0 8px 20px rgba(220,38,38,.3)}
    .value-card h3{font-size:17px;margin-bottom:10px}
    .value-card p{color:#9ca3af;font-size:13px;line-height:1.8}
    .milestone-section{margin-top:70px}
    .timeline{position:relative;padding:0 0 0 40px}
    .timeline::before{content:'';position:absolute;left:14px;top:0;bottom:0;width:3px;background:linear-gradient(180deg,#dc2626,#ef4444);border-radius:3px}
    .timeline-item{position:relative;margin-bottom:40px}
    .timeline-dot{position:absolute;left:-34px;top:4px;width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#dc2626,#ef4444);border:3px solid white;box-shadow:0 0 0 3px rgba(220,38,38,.2)}
    .timeline-content{background:white;border-radius:18px;padding:22px 24px;box-shadow:var(--shadow);transition:.3s}
    .timeline-content:hover{transform:translateX(6px);box-shadow:var(--shadow-lg)}
    .timeline-year{font-size:12px;color:var(--primary);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
    .timeline-content h4{font-size:16px;color:var(--dark);margin-bottom:8px}
    .timeline-content p{font-size:13px;color:var(--gray);line-height:1.7}
    .team-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;margin-top:40px}
    .team-card{background:white;border-radius:24px;overflow:hidden;box-shadow:var(--shadow);transition:.35s;text-align:center}
    .team-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg)}
    .team-avatar{width:100%;height:200px;background:linear-gradient(135deg,#1f2937,#374151);display:flex;align-items:center;justify-content:center;font-size:60px;color:rgba(220,38,38,.6)}
    .team-info{padding:22px 16px}
    .team-info h4{font-size:16px;color:var(--dark);margin-bottom:6px}
    .team-info span{font-size:13px;color:var(--gray)}
    .team-info .socials{display:flex;justify-content:center;gap:10px;margin-top:14px}
    .team-info .socials a{width:34px;height:34px;border-radius:10px;background:var(--lighter);display:flex;align-items:center;justify-content:center;color:var(--gray);transition:.3s;text-decoration:none;font-size:14px}
    .team-info .socials a:hover{background:var(--primary);color:white}
    .partners-grid{display:flex;flex-wrap:wrap;gap:20px;justify-content:center;margin-top:36px}
    .partner-card{background:white;border-radius:18px;padding:18px 30px;box-shadow:var(--shadow);display:flex;align-items:center;gap:12px;font-weight:600;color:var(--dark);font-size:14px;transition:.3s}
    .partner-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg)}
    .partner-card i{font-size:20px;color:var(--primary)}
    @media(max-width:900px){.about-hero{grid-template-columns:1fr}.about-badge-float{bottom:14px;right:14px}.values-grid{grid-template-columns:repeat(2,1fr)}.team-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:600px){.about-content h2{font-size:28px}.values-grid{grid-template-columns:1fr}.team-grid{grid-template-columns:1fr}.values-section{padding:40px 22px}}
  </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb" style="color:#9ca3af;display:flex;gap:10px;align-items:center;font-size:14px;margin-bottom:18px">
      <a href="index.php" style="color:#ef4444;text-decoration:none">Home</a>
      <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
      <span>Tentang Kami</span>
    </div>
    <h1 style="font-size:44px;font-weight:800;color:white;margin-bottom:12px">Tentang <span style="color:#ef4444">Dealer Kami</span></h1>
    <p style="color:#9ca3af;max-width:520px">Dealer resmi Mitsubishi terpercaya dengan pengalaman lebih dari 15 tahun melayani kebutuhan kendaraan Mitsubishi di Indonesia.</p>
  </div>
</div>

<section class="section" style="padding-top:50px">
  <div class="container">
    <div class="about-hero">
      <div class="about-img-wrap animate-on-scroll">
        <div class="about-dots"></div>
        <img src="https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&q=80" alt="Showroom Mitsubishi">
        <div class="about-badge-float">
          <div class="num">15+</div>
          <p>Tahun Berpengalaman</p>
        </div>
      </div>
      <div class="about-content animate-on-scroll">
        <span class="subtitle"><i class="fa-solid fa-star"></i> Dealer Resmi Mitsubishi</span>
        <h2>Kami Adalah Dealer Mitsubishi <span>Terpercaya</span></h2>
        <p>Dealer Mitsubishi kami telah berdiri sejak 2009 dan berkembang menjadi salah satu dealer Mitsubishi terkemuka di Indonesia. Kami berkomitmen menghadirkan pengalaman pembelian kendaraan yang menyenangkan, transparan, dan terpercaya.</p>
        <p>Dengan showroom modern, teknisi bersertifikat Mitsubishi, dan tim penjualan yang berpengalaman, kami siap membantu Anda menemukan Mitsubishi yang sempurna sesuai kebutuhan dan anggaran.</p>
        <ul class="about-list">
          <li><i class="fa-solid fa-check-circle"></i> Dealer Resmi Mitsubishi Motors Indonesia</li>
          <li><i class="fa-solid fa-check-circle"></i> Garansi resmi pabrik untuk semua unit</li>
          <li><i class="fa-solid fa-check-circle"></i> Bengkel servis resmi dengan teknisi tersertifikasi</li>
          <li><i class="fa-solid fa-check-circle"></i> Layanan kredit dengan 10+ leasing terpercaya</li>
          <li><i class="fa-solid fa-check-circle"></i> Spare part original Mitsubishi tersedia lengkap</li>
          <li><i class="fa-solid fa-check-circle"></i> Customer service 24/7 siap membantu</li>
        </ul>
        <div style="display:flex;gap:14px;flex-wrap:wrap">
          <a href="mobil.php" class="btn btn-primary btn-lg"><i class="fa-solid fa-car-side"></i> Lihat Koleksi</a>
          <a href="kontak.php" class="btn btn-ghost btn-lg"><i class="fa-solid fa-phone"></i> Hubungi Kami</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- NILAI PERUSAHAAN -->
<section class="section">
  <div class="container">
    <div class="values-section animate-on-scroll">
      <div class="section-title" style="margin-bottom:0">
        <span class="subtitle" style="color:#f87171">Nilai Kami</span>
        <h2 style="color:white">Komitmen & Nilai Perusahaan</h2>
        <p style="color:#9ca3af">Prinsip yang memandu kami dalam melayani setiap pelanggan Mitsubishi.</p>
      </div>
      <div class="values-grid">
        <div class="value-card animate-on-scroll">
          <div class="value-icon"><i class="fa-solid fa-shield-halved"></i></div>
          <h3>Integritas</h3>
          <p>Jujur dan transparan dalam setiap transaksi dan layanan kepada pelanggan Mitsubishi kami.</p>
        </div>
        <div class="value-card animate-on-scroll">
          <div class="value-icon"><i class="fa-solid fa-award"></i></div>
          <h3>Kualitas</h3>
          <p>Hanya menawarkan Mitsubishi berkualitas terjamin dengan standar dealer resmi yang ketat.</p>
        </div>
        <div class="value-card animate-on-scroll">
          <div class="value-icon"><i class="fa-solid fa-heart"></i></div>
          <h3>Kepuasan</h3>
          <p>Kepuasan pelanggan adalah prioritas utama kami dalam setiap aspek layanan.</p>
        </div>
        <div class="value-card animate-on-scroll">
          <div class="value-icon"><i class="fa-solid fa-rocket"></i></div>
          <h3>Inovasi</h3>
          <p>Terus berinovasi menghadirkan pengalaman berbelanja Mitsubishi yang lebih mudah dan modern.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MILESTONE -->
<section class="section" style="background:white">
  <div class="container">
    <div class="section-title animate-on-scroll">
      <span class="subtitle">Perjalanan Kami</span>
      <h2>Sejarah & Pencapaian</h2>
      <p>Perjalanan kami menjadi dealer Mitsubishi terpercaya selama lebih dari satu dekade.</p>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:50px;align-items:start;margin-top:40px">
      <div class="timeline animate-on-scroll">
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <div class="timeline-year">2009</div>
            <h4>Pendirian Dealer</h4>
            <p>Dealer Mitsubishi kami resmi berdiri di Jakarta dengan 5 unit showroom awal dan tim 12 orang.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <div class="timeline-year">2013</div>
            <h4>Ekspansi Pertama</h4>
            <p>Membuka 3 cabang baru di Surabaya, Bandung, dan Medan untuk melayani lebih banyak pelanggan Mitsubishi.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <div class="timeline-year">2017</div>
            <h4>1000+ Unit Terjual</h4>
            <p>Mencapai milestone penjualan 1000+ unit Mitsubishi per tahun dengan tingkat kepuasan pelanggan 98%.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <div class="timeline-year">2020</div>
            <h4>Platform Digital</h4>
            <p>Meluncurkan platform online untuk memudahkan pelanggan melihat koleksi dan booking Mitsubishi secara digital.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <div class="timeline-year">2024</div>
            <h4>Penghargaan Dealer Terbaik</h4>
            <p>Meraih penghargaan "Best Mitsubishi Dealer" dari Mitsubishi Motors Indonesia untuk ketiga kalinya berturut-turut.</p>
          </div>
        </div>
      </div>
      <div class="animate-on-scroll">
        <img src="https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=700&q=80" alt="Kantor Dealer" style="width:100%;border-radius:24px;box-shadow:var(--shadow-lg);margin-bottom:24px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div style="background:var(--lighter);border-radius:18px;padding:20px;text-align:center">
            <div style="font-size:30px;font-weight:800;color:var(--primary)" class="counter" data-target="1500">0</div>
            <div style="font-size:13px;color:var(--gray);margin-top:4px">Pelanggan Puas</div>
          </div>
          <div style="background:var(--lighter);border-radius:18px;padding:20px;text-align:center">
            <div style="font-size:30px;font-weight:800;color:var(--primary)" class="counter" data-target="15">0</div>
            <div style="font-size:13px;color:var(--gray);margin-top:4px">Tahun Pengalaman</div>
          </div>
          <div style="background:var(--lighter);border-radius:18px;padding:20px;text-align:center">
            <div style="font-size:30px;font-weight:800;color:var(--primary)" class="counter" data-target="8">0</div>
            <div style="font-size:13px;color:var(--gray);margin-top:4px">Cabang Aktif</div>
          </div>
          <div style="background:var(--lighter);border-radius:18px;padding:20px;text-align:center">
            <div style="font-size:30px;font-weight:800;color:var(--primary)">98%</div>
            <div style="font-size:13px;color:var(--gray);margin-top:4px">Kepuasan Pelanggan</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TIM -->
<section class="section">
  <div class="container">
    <div class="section-title animate-on-scroll">
      <span class="subtitle">Tim Kami</span>
      <h2>Kenali Tim <span style="color:var(--primary)">Profesional</span> Kami</h2>
      <p>Tim berpengalaman kami siap membantu Anda menemukan Mitsubishi yang tepat.</p>
    </div>
    <div class="team-grid">
      <?php
      $team = [
        ['nama'=>'Budi Santoso', 'jabatan'=>'Sales Manager', 'icon'=>'fa-solid fa-user-tie'],
        ['nama'=>'Siti Rahayu', 'jabatan'=>'Senior Sales', 'icon'=>'fa-solid fa-user'],
        ['nama'=>'Ahmad Fauzi', 'jabatan'=>'Finance Officer', 'icon'=>'fa-solid fa-user'],
        ['nama'=>'Dewi Lestari', 'jabatan'=>'Customer Service', 'icon'=>'fa-solid fa-user'],
      ];
      foreach ($team as $t):
      ?>
      <div class="team-card animate-on-scroll">
        <div class="team-avatar"><i class="<?= $t['icon'] ?>"></i></div>
        <div class="team-info">
          <h4><?= $t['nama'] ?></h4>
          <span><?= $t['jabatan'] ?></span>
          <div class="socials">
            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PARTNER -->
<section class="section" style="background:white">
  <div class="container">
    <div class="section-title animate-on-scroll">
      <span class="subtitle">Mitra Kami</span>
      <h2>Partner <span style="color:var(--primary)">Leasing & Keuangan</span></h2>
      <p>Kami bekerja sama dengan lembaga keuangan terpercaya untuk kemudahan kredit Mitsubishi Anda.</p>
    </div>
    <div class="partners-grid animate-on-scroll">
      <?php foreach (['BCA Finance','Mandiri Tunas Finance','Adira Finance','FIF Group','BAF','Maybank Finance','CIMB Niaga Auto Finance','OTO Finance'] as $p): ?>
      <div class="partner-card"><i class="fa-solid fa-building-columns"></i><?= $p ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
