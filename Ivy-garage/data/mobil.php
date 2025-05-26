<?php
// Data mobil dalam bentuk array
$mobil_data = [
    [
    'id' => 1,
    'nama' => 'Honda Civic Type R',
    'kategori' => 'Sport',
    'harga' => 1200000000,
    'gambar' => 'assets/img/Honda Civic Type R.png',
    'deskripsi_singkat' => 'Hot hatch bertenaga tinggi dengan performa balap',
    'deskripsi_lengkap' => 'Honda Civic Type R adalah mobil sport berperforma tinggi yang dirancang untuk pengalaman berkendara maksimal. Dilengkapi dengan mesin turbo bertenaga, suspensi sport, dan desain aerodinamis, Civic Type R cocok bagi pecinta kecepatan dan handling yang presisi.',
    'spesifikasi' => [
        'Mesin' => '2.0L VTEC Turbo',
        'Transmisi' => 'Manual 6-Speed',
        'Kapasitas' => '4 Penumpang',
        'Bahan Bakar' => 'Bensin',
        'Konsumsi BBM' => '10-12 km/liter'
        ]
    ],

    [
    'id' => 2,
    'nama' => 'BMW 7 Series',
    'kategori' => 'Luxury Sedan',
    'harga' => 2800000000,
    'gambar' => 'assets/img/BMW 7 Series.png',
    'deskripsi_singkat' => 'Sedan mewah dengan teknologi canggih dan kenyamanan premium',
    'deskripsi_lengkap' => 'BMW 7 Series adalah lambang kemewahan dan kecanggihan teknologi dari BMW. Dilengkapi dengan interior elegan, sistem hiburan mutakhir, dan performa mesin yang luar biasa, mobil ini dirancang untuk eksekutif yang mengutamakan kenyamanan dan prestise.',
    'spesifikasi' => [
        'Mesin' => '3.0L TwinPower Turbo Inline-6 / V8',
        'Transmisi' => 'Automatic 8-Speed',
        'Kapasitas' => '5 Penumpang',
        'Bahan Bakar' => 'Bensin',
        'Konsumsi BBM' => '9-12 km/liter'
        ]
    ],

    [
    'id' => 3,
    'nama' => 'Audi A8',
    'kategori' => 'Luxury Sedan',
    'harga' => 3000000000,
    'gambar' => 'assets/img/Audi A8.png',
    'deskripsi_singkat' => 'Sedan premium dengan kemewahan dan teknologi canggih',
    'deskripsi_lengkap' => 'Audi A8 merupakan sedan flagship dari Audi yang menawarkan kombinasi sempurna antara kenyamanan, kemewahan, dan teknologi mutakhir. Dilengkapi dengan sistem penggerak quattro dan interior berkelas, Audi A8 cocok untuk eksekutif dan kalangan elit yang menginginkan pengalaman berkendara istimewa.',
    'spesifikasi' => [
        'Mesin' => '3.0L V6 TFSI Mild Hybrid',
        'Transmisi' => 'Automatic 8-Speed Tiptronic',
        'Kapasitas' => '5 Penumpang',
        'Bahan Bakar' => 'Bensin',
        'Konsumsi BBM' => '9-11 km/liter'
        ]
    ],

    [
    'id' => 4,
    'nama' => 'Porsche 911 Turbo S',
    'kategori' => 'Sport',
    'harga' => 5900000000,
    'gambar' => 'assets/img/Porsche 911 Turbo S.png',
    'deskripsi_singkat' => 'Mobil sport ikonik dengan performa luar biasa dan teknologi mutakhir',
    'deskripsi_lengkap' => 'Porsche 911 Turbo S adalah mobil sport legendaris dengan perpaduan desain klasik dan performa ekstrem. Ditenagai mesin twin-turbo dan dilengkapi teknologi canggih Porsche Active Suspension Management, mobil ini cocok bagi mereka yang mencari kecepatan dan kemewahan dalam satu paket.',
    'spesifikasi' => [
        'Mesin' => '3.8L Twin-Turbocharged Flat-6',
        'Transmisi' => '8-Speed PDK Dual-Clutch',
        'Kapasitas' => '4 Penumpang',
        'Bahan Bakar' => 'Bensin',
        'Konsumsi BBM' => '8-10 km/liter'
        ]
    ],

    [
    'id' => 5,
    'nama' => 'Bugatti Veyron 16.4',
    'kategori' => 'Hypercar',
    'harga' => 40000000000,
    'gambar' => 'assets/img/Bugatti Veyron 16.4.png',
    'deskripsi_singkat' => 'Hypercar legendaris dengan kecepatan ekstrem dan kemewahan tinggi',
    'deskripsi_lengkap' => 'Bugatti Veyron 16.4 adalah simbol kemewahan dan performa ekstrem. Ditenagai mesin W16 quad-turbo, mobil ini mampu mencapai kecepatan lebih dari 400 km/jam. Interior mewah dan desain aerodinamis menjadikannya ikon hypercar sejati.',
    'spesifikasi' => [
        'Mesin' => '8.0L W16 Quad-Turbocharged',
        'Transmisi' => '7-Speed DSG Dual-Clutch Automatic',
        'Kapasitas' => '2 Penumpang',
        'Bahan Bakar' => 'Bensin',
        'Konsumsi BBM' => '4-6 km/liter'
    ]
]

];

// Fungsi untuk mendapatkan semua data mobil
function getAllMobil() {
    global $mobil_data;
    return $mobil_data;
}

// Fungsi untuk mendapatkan mobil berdasarkan ID
function getMobilById($id) {
    global $mobil_data;
    foreach ($mobil_data as $mobil) {
        if ($mobil['id'] == $id) {
            return $mobil;
        }
    }
    return null;
}

// Fungsi untuk mencari mobil berdasarkan nama atau kategori
function cariMobil($keyword) {
    global $mobil_data;
    $hasil = [];
    
    foreach ($mobil_data as $mobil) {
        if (stripos($mobil['nama'], $keyword) !== false || 
            stripos($mobil['kategori'], $keyword) !== false) {
            $hasil[] = $mobil;
        }
    }
    
    return $hasil;
}

// Fungsi untuk filter mobil berdasarkan kategori
function filterMobilByKategori($kategori) {
    global $mobil_data;
    if ($kategori == 'semua') {
        return $mobil_data;
    }
    
    $hasil = [];
    foreach ($mobil_data as $mobil) {
        if (strtolower($mobil['kategori']) == strtolower($kategori)) {
            $hasil[] = $mobil;
        }
    }
    
    return $hasil;
}

// Fungsi untuk format harga
function formatHarga($harga) {
    return 'Rp ' . number_format($harga, 0, ',', '.');
}
?>