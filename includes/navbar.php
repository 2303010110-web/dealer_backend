<?php
// Tentukan halaman aktif
$current = basename($_SERVER['PHP_SELF'], '.php');
function navActive(string $page, string $current): string {
    return $page === $current ? 'active' : '';
}
?>
<nav class="navbar" id="navbar">
  <div class="nav-container">
    <a href="/mitsubishi/index.php" class="logo">
      <div class="logo-icon">
        <img src="/mitsubishi/assets/img/mitsubishi-logo.svg" alt="Mitsubishi" style="width:28px;height:28px;filter:brightness(0) invert(1);" onerror="this.parentElement.innerHTML='<i class=\'fa-solid fa-car-side\'></i>'">
      </div>
      <div class="logo-text">
        <h2>Dealer<span> Mitsubishi</span></h2>
        <p>Dealer Resmi Mitsubishi</p>
      </div>
    </a>

    <ul class="nav-menu" id="navMenu">
      <li><a href="/mitsubishi/index.php" class="<?= navActive('index', $current) ?>"><i class="fa-solid fa-house"></i> Home</a></li>
      <li><a href="/mitsubishi/mobil.php" class="<?= navActive('mobil', $current) ?>"><i class="fa-solid fa-car"></i> Mobil</a></li>
      <li><a href="/mitsubishi/tentang.php" class="<?= navActive('tentang', $current) ?>"><i class="fa-solid fa-circle-info"></i> Tentang</a></li>
      <li><a href="/mitsubishi/kontak.php" class="<?= navActive('kontak', $current) ?>"><i class="fa-solid fa-phone"></i> Kontak</a></li>
    </ul>

    <div class="nav-right">
      <form action="/mitsubishi/mobil.php" method="GET" style="display:flex;">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="q" placeholder="Cari mobil Mitsubishi..." value="<?= sanitize($_GET['q'] ?? '') ?>">
        </div>
      </form>
      <a href="/mitsubishi/mobil.php" class="nav-btn"><i class="fa-solid fa-car"></i> Jelajahi Mobil</a>
      <button class="mobile-toggle" id="mobileToggle"><i class="fa-solid fa-bars"></i></button>
    </div>
  </div>
</nav>
