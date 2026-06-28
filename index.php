<?php
require_once 'includes/config.php';

// Ambil mobil featured
$featured = $conn->query("SELECT * FROM mobil WHERE featured = 1 AND status = 'aktif' LIMIT 6");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dealer Mitsubishi | Home — Showroom Mitsubishi Premium</title>
  <meta name="description" content="Dealer resmi Mitsubishi terpercaya dengan koleksi kendaraan premium, harga kompetitif, dan pelayanan profesional.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/footer.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <style>
    .hero{position:relative;min-height:96vh;background:linear-gradient(rgba(17,24,39,.84),rgba(17,24,39,.80)),url("https://images.unsplash.com/photo-1489824904134-891ab64532f1?w=1400&q=80");background-size:cover;background-position:center;display:flex;align-items:center;overflow:hidden;padding:100px 0 60px}
    .hero::before{content:'';position:absolute;width:600px;height:600px;background:radial-gradient(rgba(220,38,38,.3),transparent 70%);top:-150px;right:-150px;pointer-events:none}
    .hero-grid{display:grid;grid-template-columns:1.15fr 0.85fr;gap:50px;align-items:center;width:100%}
    .hero-content{position:relative;z-index:2}
    .hero-badge{display:inline-flex;align-items:center;gap:10px;background:rgba(220,38,38,.15);border:1px solid rgba(255,255,255,.1);padding:10px 20px;border-radius:50px;color:#fca5a5;font-size:13px;font-weight:600;margin-bottom:26px;backdrop-filter:blur(6px)}
    .hero-content h1{font-size:60px;line-height:1.12;color:white;font-weight:800;margin-bottom:22px}
    .hero-content h1 span{color:#ef4444}
    .hero-content>p{color:#d1d5db;font-size:17px;line-height:1.85;max-width:580px;margin-bottom:36px}
    .hero-buttons{display:flex;gap:16px;flex-wrap:wrap}
    .hero-card{position:relative;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);backdrop-filter:blur(12px);border-radius:30px;padding:32px;color:white;z-index:2;animation:floatCard 4s ease-in-out infinite}
    @keyframes floatCard{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
    .hero-card img{width:100%;height:200px;object-fit:cover;border-radius:20px;margin-bottom:20px}
    .hero-card h3{font-size:22px;margin-bottom:8px}
    .hero-card p{color:#d1d5db;font-size:14px;line-height:1.7;margin-bottom:18px}
    .hero-info{display:flex;justify-content:space-between;align-items:center}
    .hero-price{font-size:20px;font-weight:700;color:#f87171}
    .hero-dots{position:absolute;top:-20px;right:-20px;width:100px;height:100px;background-image:radial-gradient(rgba(255,255,255,.2) 2px,transparent 2px);background-size:16px 16px;z-index:-1}
    .stats{margin-top:-55px;position:relative;z-index:5}
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
    .stats-card{background:white;border-radius:22px;padding:26px;box-shadow:var(--shadow);transition:.35s ease;position:relative;overflow:hidden}
    .stats-card::before{content:'';position:absolute;top:0;left:0;width:4px;height:0;background:linear-gradient(180deg,#dc2626,#ef4444);border-radius:0 0 4px 0;transition:height .4s ease}
    .stats-card:hover::before{height:100%}
    .stats-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg)}
    .stats-icon{width:58px;height:58px;border-radius:16px;background:linear-gradient(135deg,#dc2626,#ef4444);display:flex;align-items:center;justify-content:center;color:white;font-size:22px;margin-bottom:18px;box-shadow:0 8px 18px rgba(220,38,38,.25)}
    .stats-card h2{font-size:34px;font-weight:800;color:var(--dark);margin-bottom:6px}
    .stats-card h2 sup{font-size:18px;color:var(--primary)}
    .stats-card p{color:var(--gray);font-size:14px}
    .features-bg{background:linear-gradient(135deg,#111827,#1f2937);border-radius:36px;padding:70px 50px}
    .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;margin-top:50px}
    .feature-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);border-radius:22px;padding:32px 26px;color:white;transition:.35s ease;position:relative;overflow:hidden}
    .feature-card:hover{transform:translateY(-6px);border-color:rgba(220,38,38,.35)}
    .feature-icon{width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#dc2626,#ef4444);display:flex;align-items:center;justify-content:center;font-size:22px;color:white;margin-bottom:20px;box-shadow:0 8px 18px rgba(220,38,38,.3);position:relative;z-index:1}
    .feature-card h3{font-size:18px;margin-bottom:10px;position:relative;z-index:1}
    .feature-card p{color:#9ca3af;font-size:14px;line-height:1.8;position:relative;z-index:1}
    .testimonial-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:26px}
    .testimonial-card{background:white;border-radius:24px;padding:30px;box-shadow:var(--shadow);transition:.35s ease}
    .testimonial-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg)}
    .testimonial-card .quote-icon{font-size:40px;color:rgba(220,38,38,.15);margin-bottom:16px;line-height:1}
    .testimonial-card p{color:var(--gray);font-size:14px;line-height:1.9;margin-bottom:22px;font-style:italic}
    .testimonial-author{display:flex;align-items:center;gap:14px}
    .author-avatar{width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#dc2626,#ef4444);display:flex;align-items:center;justify-content:center;color:white;font-size:18px;font-weight:700;flex-shrink:0}
    .author-info h4{font-size:15px;color:var(--dark);margin-bottom:2px}
    .author-info span{font-size:12px;color:var(--gray)}
    .stars{color:#f59e0b;font-size:13px;margin-bottom:6px}
    .cta-section{background:linear-gradient(135deg,#111827,#1f2937);border-radius:36px;padding:80px 50px;text-align:center;color:white;position:relative;overflow:hidden}
    .cta-section::before{content:'';position:absolute;width:500px;height:500px;background:radial-gradient(rgba(220,38,38,.2),transparent 70%);top:-200px;right:-100px}
    .cta-section h2{font-size:44px;font-weight:800;margin-bottom:16px;position:relative;z-index:1}
    .cta-section h2 span{color:#ef4444}
    .cta-section p{color:#d1d5db;font-size:17px;max-width:640px;margin:0 auto 36px;line-height:1.8;position:relative;z-index:1}
    .cta-buttons{display:flex;justify-content:center;gap:16px;flex-wrap:wrap;position:relative;z-index:1}
    .view-all-wrap{text-align:center;margin-top:50px}
    .mobil-section{background:var(--lighter)}

  </style>
</head>
<body>

<div id="loadingScreen">
  <div class="loader-logo">
    <div class="loader-icon"><i class="fa-solid fa-car-side"></i></div>
    <div class="loader-text">
      <h2>Dealer<span>Mobil</span></h2>
      <p>Showroom Mitsubishi Premium</p>
    </div>
  </div>
  <div class="loader-bar"><div class="loader-progress"></div></div>
</div>

<?php include 'includes/navbar.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <div class="hero-grid">
      <div class="hero-content animate-on-scroll">
        <div class="hero-badge"><i class="fa-solid fa-fire"></i> Dealer Resmi Mitsubishi Indonesia</div>
        <h1>Temukan <span>Mitsubishi Impian</span> Anda Hari Ini</h1>
        <p>Dealer resmi Mitsubishi dengan koleksi kendaraan terbaik, harga kompetitif, kualitas terjamin, dan pelayanan profesional untuk kebutuhan Anda.</p>
        <div class="hero-buttons">
          <a href="mobil.php" class="btn btn-primary btn-lg"><i class="fa-solid fa-car"></i> Lihat Semua Mobil</a>
          <a href="#mobil-section" class="btn btn-ghost btn-lg"><i class="fa-solid fa-arrow-down"></i> Jelajahi Sekarang</a>
        </div>
      </div>
      <div class="hero-card animate-on-scroll">
        <div class="hero-dots"></div>

        <img src="https://storage.googleapis.com/gcmkscsp001/public/media-assets/483f91da-09c5-4fc8-9e8f-6de540f6949d/conversions/26my-xp-exterior-front-left-rhd-u33-f-optimized-optimized.webp?GoogleAccessId=bsidevops%40gp-prod-mmksi-web-01.iam.gserviceaccount.com&Expires=1782809183&Signature=FHLJ1boFzVUW5HrLZk1%2Fnr7gG7gcfhApECnlNMdRFo%2FhcQNyQjkifpZgMoo3x6MZKZbqWUN0ZCWt%2BCUj5VURcEp1YX%2BFHNmeYeGc6l1sTlc0YtYcrPqkkBnTYh2GOJ4ITOJ6N6whGFUtoHMJwkdTUOhrc%2B0GHuRrLz25P%2BEtUO43XZLWF30TRikSK6W6ZeHMQQeh6xHu9eT4MOODRV%2FmrmGuCTvt0zSsDUWv1%2Fapki3VuynTDriQpEYCJRFKZhSiBE%2FBs3RXUIMCxFeb%2BMhU44dVc2GAtRHbQpx0xY1GaHttSn%2FLtO%2FPgy4fSOwcwtQ2a%2BuCIoQTSrpF7NQBcSSP0A%3D%3D" alt="Mitsubishi Xpander">

        <?php
        // Ambil satu mobil untuk hero card
        $heroMobil = $conn->query("SELECT * FROM mobil WHERE status='aktif' ORDER BY id DESC LIMIT 1");
        if ($heroMobil && $heroMobil->num_rows > 0) {
            $heroData = $heroMobil->fetch_assoc();
            $heroGambar = getGambarPath($heroData['gambar']);
        ?>
        <img src="<?= $heroGambar ?>" alt="<?= sanitize($heroData['nama_mobil']) ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22600%22 height=%22200%22><rect fill=%22%231f2937%22 width=%22600%22 height=%22200%22/><text x=%22200%22 y=%22110%22 font-size=%2220%22 fill=%22%239ca3af%22 font-family=%22Poppins%22>No Image</text></svg>'">
        <h3><?= sanitize($heroData['nama_mobil']) ?></h3>
        <p><?= substr(sanitize($heroData['deskripsi']), 0, 100) ?>...</p>
        <div class="hero-info">
        <h3>Mitsubishi Xpander 2024</h3>
        <p>MPV terlaris dengan desain sporty, mesin MIVEC bertenaga, dan kabin lega untuk keluarga aktif Anda.</p>
        <div class="hero-info">
          <div class="hero-price"><i class="fa-solid fa-tag"></i> Mulai 298 Juta</div>
          <a href="mobil.php" class="btn btn-primary btn-sm">Explore</a>
        </div>

        <?php } ?>
      </div>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="stats">
  <div class="container">
    <div class="stats-grid">
      <div class="stats-card animate-on-scroll">
        <div class="stats-icon"><i class="fa-solid fa-car-side"></i></div>
        <h2><span class="counter" data-target="<?= $total_mobil ?>">0</span><sup>+</sup></h2>
        <p>Koleksi Mitsubishi</p>
      </div>
      <div class="stats-card animate-on-scroll">
        <div class="stats-icon"><i class="fa-solid fa-users"></i></div>
        <h2><span class="counter" data-target="1500">0</span><sup>+</sup></h2>
        <p>Pelanggan Puas</p>
      </div>
      <div class="stats-card animate-on-scroll">
        <div class="stats-icon"><i class="fa-solid fa-award"></i></div>
        <h2><span class="counter" data-target="15">0</span><sup>+</sup></h2>
        <p>Tahun Pengalaman</p>
      </div>
      <div class="stats-card animate-on-scroll">
        <div class="stats-icon"><i class="fa-solid fa-headset"></i></div>
        <h2>24<sup>/7</sup></h2>
        <p>Customer Support</p>
      </div>
    </div>
  </div>
</section>

<!-- MOBIL FEATURED -->
<section class="section mobil-section" id="mobil-section">
  <div class="container">
    <div class="section-title animate-on-scroll">
      <span class="subtitle">Mobil Terbaru</span>
      <h2>Koleksi Mitsubishi Pilihan</h2>
      <p>Pilihan kendaraan Mitsubishi berkualitas terbaik dengan harga bersaing, performa maksimal, dan kondisi prima siap pakai.</p>
    </div>
    <div class="grid-auto">

    </div>
    <div class="view-all-wrap animate-on-scroll">
      <a href="mobil.php" class="btn btn-primary btn-lg"><i class="fa-solid fa-car-side"></i> Lihat Semua Koleksi</a>
    </div>
  </div>
</section>

<!-- KEUNGGULAN -->
<section class="section">
  <div class="container">
    <div class="features-bg animate-on-scroll">
      <div class="section-title" style="margin-bottom:0">
        <span class="subtitle" style="color:#f87171">Kenapa Pilih Kami</span>
        <h2 style="color:white">Keunggulan Dealer Mitsubishi Kami</h2>
        <p style="color:#9ca3af">Kami berkomitmen memberikan pengalaman pembelian Mitsubishi terbaik dengan layanan profesional dan transparan.</p>
      </div>
      <div class="features-grid">
        <div class="feature-card animate-on-scroll">
          <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
          <h3>Dealer Resmi</h3>
          <p>Kami adalah dealer resmi Mitsubishi Motors Indonesia dengan garansi resmi pabrik dan layanan after-sales terpercaya.</p>
        </div>
        <div class="feature-card animate-on-scroll">
          <div class="feature-icon"><i class="fa-solid fa-tag"></i></div>
          <h3>Harga Transparan</h3>
          <p>Tidak ada biaya tersembunyi. Harga yang tertera adalah harga final dengan semua dokumen lengkap dan resmi.</p>
        </div>
        <div class="feature-card animate-on-scroll">
          <div class="feature-icon"><i class="fa-solid fa-handshake"></i></div>
          <h3>Kredit Mudah</h3>
          <p>Bekerja sama dengan 10+ lembaga keuangan terpercaya untuk kemudahan cicilan Mitsubishi dengan bunga kompetitif.</p>
        </div>
        <div class="feature-card animate-on-scroll">
          <div class="feature-icon"><i class="fa-solid fa-wrench"></i></div>
          <h3>Servis Resmi</h3>
          <p>Bengkel servis resmi Mitsubishi dengan teknisi bersertifikat dan spare part original bergaransi.</p>
        </div>
        <div class="feature-card animate-on-scroll">
          <div class="feature-icon"><i class="fa-solid fa-headset"></i></div>
          <h3>Support 24/7</h3>
          <p>Tim customer service kami siap membantu Anda kapan saja melalui telepon, WhatsApp, maupun live chat.</p>
        </div>
        <div class="feature-card animate-on-scroll">
          <div class="feature-icon"><i class="fa-solid fa-truck-fast"></i></div>
          <h3>Pengiriman Cepat</h3>
          <p>Layanan pengiriman Mitsubishi ke seluruh Indonesia dengan armada terpercaya dan asuransi pengiriman penuh.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIAL -->
<section class="section" style="background:white">
  <div class="container">
    <div class="section-title animate-on-scroll">
      <span class="subtitle">Testimonial</span>
      <h2>Apa Kata Pelanggan Mitsubishi Kami</h2>
      <p>Ribuan pelanggan puas telah mempercayakan kebutuhan Mitsubishinya kepada kami.</p>
    </div>
    <div class="testimonial-grid">

        <div class="testimonial-author">
          <div class="author-avatar"><?= strtoupper(substr($t['nama'], 0, 1)) ?></div>
          <div class="author-info">
            <h4><?= sanitize($t['nama']) ?></h4>
<<<<<<< HEAD
            <span><i class="fa-solid fa-location-dot"></i> <?= sanitize($t['kota']) ?></span>
          </div>
        </div>
      </div>
      <?php endwhile; ?>

    </div>
  </div>
</section>

<!-- CTA -->
<section class="section">
  <div class="container">
    <div class="cta-section animate-on-scroll">
      <h2>Siap Memiliki <span>Mitsubishi Impian?</span></h2>
      <p>Hubungi dealer resmi Mitsubishi kami sekarang juga dan dapatkan penawaran eksklusif serta kemudahan cicilan untuk Mitsubishi pilihan Anda.</p>
      <div class="cta-buttons">
        <a href="mobil.php" class="btn btn-primary btn-lg"><i class="fa-solid fa-car-side"></i> Lihat Koleksi Mitsubishi</a>
        <a href="kontak.php" class="btn btn-ghost btn-lg"><i class="fa-solid fa-phone"></i> Hubungi Kami</a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
<script>
// Counter animation
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    const speed = 200;
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const increment = target / speed;
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.textContent = Math.ceil(current);
                setTimeout(updateCounter, 20);
            } else {
                counter.textContent = target;
            }
        };
        
        // Start counter when visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });
        observer.observe(counter);
    });
});
</script>
</body>
</html>

