<?php
// Include data mobil
include 'data/mobil.php';

// Set page title
$page_title = 'Beranda';

// Ambil parameter pencarian dan filter
$keyword = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'semua';

// Dapatkan data mobil berdasarkan pencarian atau filter
if (!empty($keyword)) {
    $daftar_mobil = cariMobil($keyword);
} else {
    $daftar_mobil = filterMobilByKategori($kategori);
}

// Include header
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Temukan Mobil Impian Anda</h1>
                <p class="lead mb-4">Ivy Garage menyediakan berbagai pilihan mobil berkualitas dengan harga terbaik. Dapatkan mobil impian Anda sekarang juga!</p>
                <a href="#katalog" class="btn btn-light btn-lg">
                    <i class="fas fa-car me-2"></i>Lihat Katalog
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-car display-1"></i>
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="py-4 bg-light" id="katalog">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3>
                    <?php 
                    if (!empty($keyword)) {
                        echo 'Hasil Pencarian: "' . htmlspecialchars($keyword) . '"';
                    } elseif ($kategori != 'semua') {
                        echo 'Kategori: ' . ucfirst($kategori);
                    } else {
                        echo 'Katalog Mobil';
                    }
                    ?>
                    <small class="text-muted">(<?php echo count($daftar_mobil); ?> mobil)</small>
                </h3>
            </div>
            <div class="col-md-6">
                <form method="GET" action="index.php" class="d-flex gap-2">
                    <select name="kategori" class="form-select" onchange="this.form.submit()">
                        <option value="semua" <?php echo $kategori == 'semua' ? 'selected' : ''; ?>>Semua Kategori</option>
                        <option value="sport" <?php echo $kategori == 'sport' ? 'selected' : ''; ?>>Sport</option>
                        <option value="luxury sedan" <?php echo $kategori == 'luxury sedan' ? 'selected' : ''; ?>>Luxury Sedan</option>
                        <option value="hypercar" <?php echo $kategori == 'hypercar' ? 'selected' : ''; ?>>Hypercar</option>
                    </select>
                    <?php if (!empty($keyword)): ?>
                        <input type="hidden" name="cari" value="<?php echo htmlspecialchars($keyword); ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Katalog Mobil -->
<section class="py-5">
    <div class="container">
        <?php if (empty($daftar_mobil)): ?>
            <div class="text-center py-5">
                <i class="fas fa-search display-1 text-muted mb-3"></i>
                <h4 class="text-muted">Tidak ada mobil yang ditemukan</h4>
                <p class="text-muted">Coba gunakan kata kunci yang berbeda atau lihat semua katalog.</p>
                <a href="index.php" class="btn btn-primary">Lihat Semua Mobil</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($daftar_mobil as $mobil): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo $mobil['gambar']; ?>" class="card-img-top" alt="<?php echo $mobil['nama']; ?>" 
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0"><?php echo $mobil['nama']; ?></h5>
                                    <span class="badge bg-secondary"><?php echo $mobil['kategori']; ?></span>
                                </div>
                                <p class="card-text text-muted small flex-grow-1"><?php echo $mobil['deskripsi_singkat']; ?></p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="text-primary mb-0 fw-bold"><?php echo formatHarga($mobil['harga']); ?></h6>
                                    </div>
                                    <div class="mt-3 d-grid gap-2 d-md-flex">
                                        <a href="detail.php?id=<?php echo $mobil['id']; ?>" class="btn btn-primary flex-fill">
                                            <i class="fas fa-eye me-1"></i>Lihat Detail
                                        </a>
                                        <a href="pesan.php?id=<?php echo $mobil['id']; ?>" class="btn btn-success flex-fill">
                                            <i class="fas fa-shopping-cart me-1"></i>Pesan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Keunggulan Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Mengapa Memilih Ivy Garage?</h2>
            <p class="text-muted">Kami memberikan pelayanan terbaik untuk kepuasan pelanggan</p>
        </div>
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="fas fa-certificate fa-2x"></i>
                </div>
                <h5>Garansi Resmi</h5>
                <p class="text-muted">Semua mobil dilengkapi garansi resmi dari pabrikan</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
                <h5>Harga Terbaik</h5>
                <p class="text-muted">Dapatkan harga terbaik dengan berbagai promo menarik</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="fas fa-tools fa-2x"></i>
                </div>
                <h5>Service Center</h5>
                <p class="text-muted">Layanan purna jual dan service center di berbagai kota</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="fas fa-handshake fa-2x"></i>
                </div>
                <h5>Pelayanan Prima</h5>
                <p class="text-muted">Tim profesional siap membantu Anda 24/7</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>