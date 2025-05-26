<?php
// Include data mobil
include 'data/mobil.php';

// Konfigurasi Fonnte WhatsApp API
$fonnte_token = 'jDggjBDYP8CzNSJbkwi2'; // Ganti dengan token Fonnte Anda
$fonnte_url = 'https://api.fonnte.com/send';

// Fungsi untuk generate nomor invoice
function generateInvoice() {
    return 'INV-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));
}

// Fungsi untuk kirim WhatsApp via Fonnte
function kirimWhatsApp($nomor, $pesan, $token) {
    $curl = curl_init();
    
    // Format nomor telepon (hapus karakter non-digit dan tambahkan 62 jika dimulai dengan 0)
    $nomor = preg_replace('/[^0-9]/', '', $nomor);
    if (substr($nomor, 0, 1) === '0') {
        $nomor = '62' . substr($nomor, 1);
    }
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'target' => $nomor,
            'message' => $pesan,
            'countryCode' => '62',
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $token
        ),
    ));
    
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    return json_decode($response, true);
}

// Fungsi untuk membuat pesan struk
function buatPesanStruk($nama, $email, $telepon, $alamat, $mobil, $invoice, $metode_pembayaran, $catatan) {
    $tanggal = date('d/m/Y');
    
    $pesan = "🚗 *STRUK PEMESANAN MOBIL* 🚗\n\n";
    $pesan .= "📋 *Detail Pemesanan:*\n";
    $pesan .= "━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "📄 Invoice: *{$invoice}*\n";
    $pesan .= "📅 Tanggal: {$tanggal}\n\n";
    
    $pesan .= "👤 *Data Pemesan:*\n";
    $pesan .= "━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "👨‍💼 Nama: {$nama}\n";
    $pesan .= "📧 Email: {$email}\n";
    $pesan .= "📱 Telepon: {$telepon}\n";
    $pesan .= "🏠 Alamat: {$alamat}\n\n";
    
    $pesan .= "🚙 *Detail Mobil:*\n";
    $pesan .= "━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "🚗 Mobil: *{$mobil['nama']}*\n";
    $pesan .= "💰 Harga: *" . formatHarga($mobil['harga']) . "*\n";
    $pesan .= "🏷️ Kategori: {$mobil['kategori']}\n";
    
    if (!empty($metode_pembayaran)) {
        $metodeBayar = '';
        switch($metode_pembayaran) {
            case 'cash': $metodeBayar = '💰 Cash/Tunai'; break;
            case 'kredit': $metodeBayar = '💳 Kredit/Cicilan'; break;
            case 'trade_in': $metodeBayar = '🔄 Trade In'; break;
            default: $metodeBayar = $metode_pembayaran;
        }
        $pesan .= "💳 Pembayaran: {$metodeBayar}\n";
    }
    
    if (!empty($catatan)) {
        $pesan .= "📝 Catatan: {$catatan}\n";
    }
    
    $pesan .= "\n🔔 *Informasi Penting:*\n";
    $pesan .= "━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "• Tim sales akan menghubungi dalam 1x24 jam\n";
    $pesan .= "• Harap simpan nomor invoice untuk referensi\n";
    $pesan .= "• Harga dapat berubah, akan dikonfirmasi sales\n\n";
    
    $pesan .= "💳 *Info Pembayaran:*\n";
    $pesan .= "━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "🏦 Transfer Bank: BCA 1234567890\n";
    $pesan .= "    a.n. PT Ivy Garage\n";
    $pesan .= "💰 Cash: Bayar saat pengambilan\n";
    $pesan .= "💳 Kredit: Bunga mulai 0%\n\n";
    
    $pesan .= "📞 *Kontak:*\n";
    $pesan .= "━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "📱 WhatsApp: 0895-3868-47897\n";
    $pesan .= "📧 Email: farell@gmail.com\n\n";
    
    $pesan .= "Terima kasih telah mempercayai kami! 🙏\n";
    return $pesan;
}

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
$page_title = 'Pesan ' . $mobil['nama'];

// Proses form jika ada data yang dikirim
$pesan_sukses = false;
$invoice_number = '';
$whatsapp_status = null;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi sederhana
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $metode_pembayaran = $_POST['metode_pembayaran'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');
    
    if (!empty($nama) && !empty($email) && !empty($telepon) && !empty($alamat)) {
        $pesan_sukses = true;
        $invoice_number = generateInvoice();
        
        // Di sini seharusnya data disimpan ke database
        // Contoh: simpanPemesanan($nama, $email, $telepon, $alamat, $mobil['id'], $invoice_number, $metode_pembayaran, $catatan);
        
        // Kirim struk via WhatsApp
        if (!empty($fonnte_token) && $fonnte_token !== 'YOUR_FONNTE_TOKEN_HERE') {
            $pesan_struk = buatPesanStruk($nama, $email, $telepon, $alamat, $mobil, $invoice_number, $metode_pembayaran, $catatan);
            $whatsapp_status = kirimWhatsApp($telepon, $pesan_struk, $fonnte_token);
        } else {
            $error_message = 'Token Fonnte belum dikonfigurasi.';
        }
    }
}

// Include header HANYA jika pesan belum sukses
if (!$pesan_sukses) {
    if (file_exists('includes/header.php')) {
        include 'includes/header.php';
    }
    if (file_exists('includes/navbar.php')) {
        include 'includes/navbar.php';
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $page_title; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
    <?php
}
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="bg-light py-3">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
            <li class="breadcrumb-item"><a href="detail.php?id=<?php echo $mobil['id']; ?>"><?php echo $mobil['nama']; ?></a></li>
            <li class="breadcrumb-item active">Pemesanan</li>
        </ol>
    </div>
</nav>

<section class="py-5">
    <div class="container">
        <?php if ($pesan_sukses): ?>
            <!-- Halaman Success -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white text-center">
                            <h4 class="mb-0">
                                <i class="fas fa-check-circle me-2"></i>Pemesanan Berhasil!
                            </h4>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h5>Terima kasih, <?php echo htmlspecialchars($nama); ?>!</h5>
                            <p class="mb-4">Pemesanan Anda untuk <strong><?php echo $mobil['nama']; ?></strong> telah kami terima.</p>
                            
                            <!-- Info Invoice -->
                            <div class="alert alert-primary">
                                <h6><i class="fas fa-receipt me-2"></i>Nomor Invoice</h6>
                                <h4 class="mb-0 text-primary"><?php echo $invoice_number; ?></h4>
                            </div>
                            
                            <!-- Status WhatsApp -->
                            <?php if ($whatsapp_status): ?>
                                <?php if (isset($whatsapp_status['status']) && $whatsapp_status['status']): ?>
                                    <div class="alert alert-success">
                                        <i class="fab fa-whatsapp me-2"></i>
                                        <strong>Struk berhasil dikirim ke WhatsApp Anda!</strong>
                                        <br><small>Periksa pesan WhatsApp di nomor <?php echo htmlspecialchars($telepon); ?></small>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Pemesanan berhasil, tetapi struk WhatsApp gagal dikirim.</strong>
                                        <br><small>Tim kami akan menghubungi Anda secara langsung.</small>
                                        <?php if (isset($whatsapp_status['reason'])): ?>
                                            <br><small class="text-muted">Alasan: <?php echo htmlspecialchars($whatsapp_status['reason']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php elseif (!empty($error_message)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Pemesanan berhasil!</strong>
                                    <br><small><?php echo htmlspecialchars($error_message); ?></small>
                                </div>
                            <?php endif; ?>
                            
                            <p class="text-muted">Tim kami akan segera menghubungi Anda melalui nomor telepon <strong><?php echo htmlspecialchars($telepon); ?></strong> untuk konfirmasi lebih lanjut.</p>
                            
                            <div class="alert alert-info text-start mt-4">
                                <strong><i class="fas fa-info-circle me-2"></i>Langkah Selanjutnya:</strong>
                                <ol class="mb-0 mt-2">
                                    <li>Tim sales akan menghubungi Anda dalam 1x24 jam</li>
                                    <li>Konfirmasi detail pemesanan dan pembayaran</li>
                                    <li>Proses dokumen dan pengiriman</li>
                                </ol>
                            </div>
                            
                            <!-- Detail Pembayaran -->
                            <div class="alert alert-warning text-start">
                                <h6><i class="fas fa-credit-card me-2"></i>Informasi Pembayaran</h6>
                                <p class="mb-2">Metode pembayaran yang tersedia:</p>
                                <ul class="mb-2">
                                    <li><strong>Cash/Tunai:</strong> Pembayaran langsung saat pengambilan</li>
                                    <li><strong>Transfer Bank:</strong> BCA 1234567890 a.n. PT Dealer Mobil</li>
                                    <li><strong>Kredit:</strong> Bunga kompetitif mulai 0% (syarat & ketentuan berlaku)</li>
                                </ul>
                                <small class="text-muted">*Detail pembayaran lengkap akan dikonfirmasi oleh tim sales</small>
                            </div>
                            
                            <!-- Tombol Kirim Ulang Struk WhatsApp -->
                            <?php if ($whatsapp_status && (!isset($whatsapp_status['status']) || !$whatsapp_status['status'])): ?>
                                <div class="alert alert-light">
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="nama" value="<?php echo htmlspecialchars($nama); ?>">
                                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                        <input type="hidden" name="telepon" value="<?php echo htmlspecialchars($telepon); ?>">
                                        <input type="hidden" name="alamat" value="<?php echo htmlspecialchars($alamat); ?>">
                                        <input type="hidden" name="metode_pembayaran" value="<?php echo htmlspecialchars($metode_pembayaran); ?>">
                                        <input type="hidden" name="catatan" value="<?php echo htmlspecialchars($catatan); ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fab fa-whatsapp me-1"></i>Kirim Ulang Struk WhatsApp
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row mt-4">
                                <div class="col-md-6 mb-2">
                                    <a href="index.php" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="detail.php?id=<?php echo $mobil['id']; ?>" class="btn btn-secondary btn-lg w-100">
                                        <i class="fas fa-car me-2"></i>Lihat Detail Mobil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            </body>
            </html>
            
        <?php else: ?>
            <!-- Form Pemesanan -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Form Pemesanan</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" id="formPemesanan">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama" name="nama" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="telepon" class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="telepon" name="telepon" required 
                                               placeholder="Contoh: 08123456789">
                                        <small class="form-text text-muted">
                                            <i class="fab fa-whatsapp text-success"></i> Struk pemesanan akan dikirim ke nomor ini
                                        </small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                        <select class="form-select" id="metode_pembayaran" name="metode_pembayaran">
                                            <option value="">Pilih Metode Pembayaran</option>
                                            <option value="cash">💰 Cash/Tunai</option>
                                            <option value="kredit">💳 Kredit/Cicilan</option>
                                            <option value="trade_in">🔄 Trade In</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="catatan" class="form-label">Catatan Tambahan</label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                              placeholder="Contoh: Warna yang diinginkan, waktu yang tepat untuk dihubungi, dll."></textarea>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="setuju" required>
                                    <label class="form-check-label" for="setuju">
                                        Saya setuju dengan syarat dan ketentuan yang berlaku.
                                    </label>
                                </div>
                                
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <i class="fas fa-phone me-2"></i>
                                            <strong>Konfirmasi:</strong> Tim sales akan menghubungi Anda melalui telepon
                                        </div>
                                        <div class="col-md-6">
                                            <i class="fab fa-whatsapp me-2 text-success"></i>
                                            <strong>Struk Digital:</strong> Akan dikirim ke WhatsApp Anda otomatis
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-success btn-lg flex-md-fill">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Pemesanan
                                    </button>
                                    <a href="detail.php?id=<?php echo $mobil['id']; ?>" class="btn btn-secondary btn-lg flex-md-fill">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Ringkasan Pesanan -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Ringkasan Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="<?php echo $mobil['gambar']; ?>" class="img-fluid rounded" alt="<?php echo $mobil['nama']; ?>" 
                                     style="height: 150px; object-fit: cover; width: 100%;">
                            </div>
                            
                            <h6 class="fw-bold"><?php echo $mobil['nama']; ?></h6>
                            <p class="text-muted small"><?php echo $mobil['deskripsi_singkat']; ?></p>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Harga:</span>
                                <strong class="text-primary"><?php echo formatHarga($mobil['harga']); ?></strong>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Kategori:</span>
                                <span class="badge bg-secondary"><?php echo $mobil['kategori']; ?></span>
                            </div>
                            
                            <hr>
                            
                            <div class="alert alert-success small">
                                <i class="fab fa-whatsapp me-1"></i>
                                <strong>Bonus:</strong> Struk digital akan dikirim otomatis ke WhatsApp Anda setelah pemesanan!
                            </div>
                            
                            <div class="alert alert-warning small">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Catatan:</strong> Harga dapat berubah sewaktu-waktu. Tim sales kami akan mengkonfirmasi harga final saat menghubungi Anda.
                            </div>
                            
                            <div class="alert alert-info small">
                                <i class="fas fa-phone me-1"></i>
                                Butuh bantuan? Hubungi kami di <strong>0812-3456-7890</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>     
        <?php endif; ?>
    </div>
</section>

<?php 
// Include footer HANYA untuk halaman form, bukan halaman sukses
if (!$pesan_sukses && file_exists('includes/footer.php')) {
    include 'includes/footer.php';
}
?>