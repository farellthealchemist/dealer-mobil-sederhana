<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-car me-2"></i>Ivy Garage
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                       href="index.php">
                        <i class="fas fa-home me-1"></i>Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#katalog">
                        <i class="fas fa-list me-1"></i>Katalog Mobil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tentang">
                        <i class="fas fa-info-circle me-1"></i>Tentang Kami
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#kontak">
                        <i class="fas fa-phone me-1"></i>Kontak
                    </a>
                </li>
            </ul>
            
            <!-- Form pencarian di navbar -->
            <form class="d-flex" method="GET" action="index.php">
                <input class="form-control me-2" type="search" name="cari" placeholder="Cari mobil..." 
                       value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                <button class="btn btn-outline-light" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</nav>