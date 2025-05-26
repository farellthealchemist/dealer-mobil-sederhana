// Custom JavaScript untuk AutoDealer

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scrolling untuk anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Auto-hide success modal after 10 seconds
    const successModal = document.getElementById('successModal');
    if (successModal && successModal.classList.contains('show')) {
        setTimeout(function() {
            successModal.style.display = 'none';
        }, 10000);
        
        // Close modal when clicking outside
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                successModal.style.display = 'none';
            }
        });
    }
    
    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                
                // Focus pada field pertama yang error
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            form.classList.add('was-validated');
        });
    });
    
    // Phone number formatting (Indonesian format)
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Auto-format Indonesian phone numbers
            if (value.startsWith('62')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = value;
            } else if (value.length > 0) {
                value = '0' + value;
            }
            
            e.target.value = value;
        });
        
        // Validation feedback
        input.addEventListener('blur', function(e) {
            const value = e.target.value;
            const isValid = /^(\+62|0)[0-9]{9,12}$/.test(value);
            
            if (value && !isValid) {
                e.target.setCustomValidity('Format nomor telepon tidak valid');
            } else {
                e.target.setCustomValidity('');
            }
        });
    });
    
    // PERBAIKAN: Loading animation untuk buttons - Hanya untuk form yang menggunakan AJAX atau membutuhkan loading
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        // Simpan teks asli tombol
        const originalText = button.innerHTML;
        
        button.addEventListener('click', function(e) {
            const form = button.closest('form');
            
            // PENTING: Skip loading untuk form pencarian dan filter biasa
            if (form) {
                const formAction = form.getAttribute('action');
                const formMethod = form.getAttribute('method') || 'GET';
                
                // Jangan tampilkan loading untuk form GET (pencarian/filter) ke index.php
                if (formMethod.toLowerCase() === 'get' && 
                    (formAction === 'index.php' || formAction === '' || !formAction)) {
                    return; // Biarkan form submit normal tanpa loading
                }
            }
            
            // Tampilkan loading hanya untuk form POST atau form khusus
            if (form && form.checkValidity()) {
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                button.disabled = true;
                
                // Restore button setelah form submit
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 1500); // Dikurangi waktu timeout
            }
        });
        
        // Reset button saat halaman dimuat ulang
        window.addEventListener('beforeunload', function() {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    });
    
    // Image lazy loading fallback
    const images = document.querySelectorAll('img[src]');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGVlMmU2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkdhbWJhciBUaWRhayBEaXRlbXVrYW48L3RleHQ+PC9zdmc+';
            this.alt = 'Gambar tidak ditemukan';
        });
    });
    
    // Search functionality enhancement
    const searchForm = document.querySelector('form[action="index.php"]');
    const searchInput = document.querySelector('input[name="cari"]');
    
    if (searchInput) {
        // Auto-suggest (simple implementation)
        const suggestions = ['Avanza', 'Xenia', 'Brio', 'Jazz', 'Innova', 'MPV', 'Hatchback'];
        
        searchInput.addEventListener('input', function(e) {
            const value = e.target.value.toLowerCase();
            
            // Simple suggestion logic (can be enhanced)
            if (value.length > 2) {
                const matches = suggestions.filter(item => 
                    item.toLowerCase().includes(value)
                );
                
                // You can implement dropdown suggestions here
                console.log('Suggestions:', matches);
            }
        });
        
        // Clear search - PERBAIKAN: Buat tombol clear yang lebih baik
        let clearSearch = searchForm.querySelector('.clear-search-btn');
        if (!clearSearch) {
            clearSearch = document.createElement('button');
            clearSearch.type = 'button';
            clearSearch.className = 'btn btn-sm btn-outline-secondary ms-1 clear-search-btn';
            clearSearch.innerHTML = '<i class="fas fa-times"></i>';
            clearSearch.title = 'Hapus pencarian';
            clearSearch.style.display = searchInput.value ? 'inline-block' : 'none';
            
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                window.location.href = 'index.php';
            });
            
            // Insert setelah search input
            searchInput.parentNode.insertBefore(clearSearch, searchInput.nextSibling);
        }
        
        searchInput.addEventListener('input', function() {
            clearSearch.style.display = this.value ? 'inline-block' : 'none';
        });
    }
    
    // PERBAIKAN: Hapus auto-submit yang bermasalah pada dropdown kategori
    const categorySelect = document.querySelector('select[name="kategori"]');
    if (categorySelect) {
        // Hapus onchange="this.form.submit()" dan ganti dengan event listener yang lebih controlled
        categorySelect.removeAttribute('onchange');
        
        categorySelect.addEventListener('change', function() {
            // Tambahkan delay kecil untuk memastikan user sudah selesai memilih
            setTimeout(() => {
                this.form.submit();
            }, 100);
        });
    }
    
    // Price formatting
    const priceElements = document.querySelectorAll('.price, [data-price]');
    priceElements.forEach(element => {
        const price = element.dataset.price || element.textContent;
        if (price && !isNaN(price.replace(/[^\d]/g, ''))) {
            const numericPrice = parseInt(price.replace(/[^\d]/g, ''));
            element.textContent = 'Rp ' + numericPrice.toLocaleString('id-ID');
        }
    });
    
    // Scroll to top button
    const scrollToTopBtn = document.createElement('button');
    scrollToTopBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
    scrollToTopBtn.className = 'btn btn-primary position-fixed';
    scrollToTopBtn.style.cssText = `
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    `;
    scrollToTopBtn.title = 'Kembali ke atas';
    
    document.body.appendChild(scrollToTopBtn);
    
    // Show/hide scroll to top button
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.style.display = 'block';
        } else {
            scrollToTopBtn.style.display = 'none';
        }
    });
    
    // Scroll to top functionality
    scrollToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Car card hover effects
    const carCards = document.querySelectorAll('.card');
    carCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        });
    });
    
    // WhatsApp contact enhancement
    const whatsappLinks = document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"]');
    whatsappLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add tracking or analytics here if needed
            console.log('WhatsApp contact clicked');
        });
    });
    
    // Performance optimization: Debounce scroll events
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Apply debounce to scroll events if there are performance issues
    const debouncedScroll = debounce(function() {
        // Additional scroll-based functions can go here
    }, 100);
    
    window.addEventListener('scroll', debouncedScroll);
    
    // Console log for debugging
    console.log('AutoDealer JavaScript loaded successfully');
});

// Global utility functions
window.AutoDealer = {
    // Format currency
    formatCurrency: function(amount) {
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    },
    
    // Format phone number
    formatPhone: function(phone) {
        let cleaned = phone.replace(/\D/g, '');
        if (cleaned.startsWith('62')) {
            return '+' + cleaned;
        } else if (cleaned.startsWith('0')) {
            return cleaned;
        } else {
            return '0' + cleaned;
        }
    },
    
    // Show loading state
    showLoading: function(element) {
        if (element) {
            const originalText = element.innerHTML;
            element.setAttribute('data-original-text', originalText);
            element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            element.disabled = true;
        }
    },
    
    // Hide loading state
    hideLoading: function(element) {
        if (element) {
            const originalText = element.getAttribute('data-original-text');
            element.innerHTML = originalText || 'Submit';
            element.disabled = false;
            element.removeAttribute('data-original-text');
        }
    }
};