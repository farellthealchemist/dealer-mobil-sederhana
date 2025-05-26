<?php
// Include data mobil
include 'data/mobil.php';

// Ambil ID mobil dari parameter URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Dapatkan data mobil berdasarkan ID
$mobil = getMobilById($id);

// Jika mobil tidak ditemukan, redirect ke halaman utama
if (!$mobil) {
    header('Location: index.php');
    exit;
}

// Set page title
$page_title = $mobil['nama'];

// Include header
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="bg-light py-3">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
            <li class="breadcrumb-item active"><?php echo $mobil['nama']; ?></li>
        </ol>
    </div>
</nav>

<!-- Detail Mobil -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Gambar Mobil -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <img src="<?php echo $mobil['gambar']; ?>" class="card-img-top" alt="<?php echo $mobil['nama']; ?>" 
                         style="height: 400px; object-fit: cover;">
                </div>
            </div>
            
            <!-- Informasi Mobil -->
            <div class="col-lg-6">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="h2 fw-bold"><?php echo $mobil['nama']; ?></h1>
                    <span class="badge bg-primary fs-6"><?php echo $mobil['kategori']; ?></span>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-primary fw-bold"><?php echo formatHarga($mobil['harga']); ?></h3>
                </div>
                
                <div class="mb-4">
                    <h5>Deskripsi</h5>
                    <p class="text-muted"><?php echo $mobil['deskripsi_lengkap']; ?></p>
                </div>
                
                <!-- Tombol Aksi -->
                <div class="d-grid gap-2 d-md-flex mb-4">
                    <a href="pesan.php?id=<?php echo $mobil['id']; ?>" class="btn btn-success btn-lg flex-md-fill">
                        <i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang
                    </a>
                    <button class="btn btn-outline-primary btn-lg flex-md-fill" onclick="window.history.back()">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </button>
                </div>
                
                <!-- Informasi Kontak -->
                <div class="alert alert-info">
                    <h6><i class="fas fa-phone me-2"></i>Butuh Bantuan?</h6>
                    <p class="mb-0">Hubungi kami di <strong>0812-3456-7890</strong> atau WhatsApp untuk konsultasi lebih lanjut.</p>
                </div>
            </div>
        </div>
        
        <!-- Spesifikasi -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Spesifikasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($mobil['spesifikasi'] as $key => $value): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <strong class="me-3" style="min-width: 120px;"><?php echo $key; ?>:</strong>
                                        <span><?php echo $value; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobil Lainnya -->
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="mb-4">Mobil Lainnya</h4>
                <div class="row">
                    <?php 
                    // Tampilkan 3 mobil lainnya (selain mobil yang sedang dilihat)
                    $mobil_lain = getAllMobil();
                    $count = 0;
                    foreach ($mobil_lain as $m): 
                        if ($m['id'] != $mobil['id'] && $count < 3):
                            $count++;
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo $m['gambar']; ?>" class="card-img-top" alt="<?php echo $m['nama']; ?>" 
                                     style="height: 150px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title"><?php echo $m['nama']; ?></h6>
                                    <p class="card-text text-muted small"><?php echo $m['deskripsi_singkat']; ?></p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-primary"><?php echo formatHarga($m['harga']); ?></strong>
                                        </div>
                                        <a href="detail.php?id=<?php echo $m['id']; ?>" class="btn btn-primary btn-sm w-100">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>