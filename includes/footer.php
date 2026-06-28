<footer class="footer">
  <div class="footer-top">
    <div class="container footer-grid">
      <div class="footer-col">
        <div class="footer-logo">
          <div class="footer-logo-icon"><i class="fa-solid fa-car-side"></i></div>
          <div class="footer-logo-text">
            <h2>Dealer<span>Mobil</span></h2>
            <p>Dealer Resmi Mitsubishi</p>
          </div>
        </div>
        <p class="footer-desc">Dealer Resmi Mitsubishi menyediakan berbagai pilihan kendaraan Mitsubishi berkualitas dengan harga terbaik, pelayanan profesional, dan proses pembelian yang aman serta nyaman.</p>
        <div class="footer-social">
          <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#"><i class="fa-brands fa-instagram"></i></a>
          <a href="#"><i class="fa-brands fa-youtube"></i></a>
          <a href="https://wa.me/<?= getSetting($conn, 'whatsapp', WA_NUMBER) ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
          <a href="#"><i class="fa-brands fa-tiktok"></i></a>
        </div>
      </div>
      <div class="footer-col">
        <h3>Menu Cepat</h3>
        <ul>
          <li><a href="/mitsubishi/index.php">Beranda</a></li>
          <li><a href="/mitsubishi/mobil.php">Daftar Mobil</a></li>
          <li><a href="/mitsubishi/tentang.php">Tentang Kami</a></li>
          <li><a href="/mitsubishi/booking.php">Booking</a></li>
          <li><a href="/mitsubishi/kontak.php">Kontak</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3>Layanan</h3>
        <ul>
          <li><a href="/mitsubishi/booking.php">Booking Mobil</a></li>
          <li><a href="/mitsubishi/mobil.php?tipe=SUV">SUV Premium</a></li>
          <li><a href="/mitsubishi/mobil.php?tipe=MPV">MPV Keluarga</a></li>
          <li><a href="/mitsubishi/kontak.php">Kredit Mobil</a></li>
          <li><a href="/mitsubishi/kontak.php">Test Drive</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3>Kontak Kami</h3>
        <div class="contact-item">
          <div class="ci-icon"><i class="fa-solid fa-location-dot"></i></div>
          <span><?= getSetting($conn, 'alamat', 'Jl. Raya Mitsubishi No. 99, Jakarta') ?></span>
        </div>
        <div class="contact-item">
          <div class="ci-icon"><i class="fa-solid fa-phone"></i></div>
          <span><?= getSetting($conn, 'telepon', '+62 812-3456-7890') ?></span>
        </div>
        <div class="contact-item">
          <div class="ci-icon"><i class="fa-solid fa-envelope"></i></div>
          <span><?= getSetting($conn, 'email', 'info@mitsubishidealer.com') ?></span>
        </div>
        <div class="contact-item">
          <div class="ci-icon"><i class="fa-solid fa-clock"></i></div>
          <span><?= getSetting($conn, 'jam_operasional', 'Senin – Sabtu, 08:00 – 20:00 WIB') ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container footer-bottom-content">
      <p>©
        <span id="year"><?= date('Y') ?></span>
        <a id="adminSecretLink"
           href="/mitsubishi/admin/login.php"
           title="Admin"
           style="color:inherit;text-decoration:none;cursor:pointer;">DealerMobil</a>.
        All Rights Reserved.
      </p>
      <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms &amp; Conditions</a>
        <a href="#">Sitemap</a>
      </div>
    </div>
  </div>
</footer>

<button id="backToTop" title="Kembali ke atas"><i class="fa-solid fa-arrow-up"></i></button>
<a href="https://wa.me/<?= getSetting($conn, 'whatsapp', WA_NUMBER) ?>" target="_blank" class="wa-float" title="Chat WhatsApp">
  <i class="fa-brands fa-whatsapp"></i>
</a>
