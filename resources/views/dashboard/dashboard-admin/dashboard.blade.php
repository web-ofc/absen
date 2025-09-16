<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Navbar - Instagram Style</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #405cf5;
            --secondary-color: #6c757d;
            --hover-color: #0d6efd;
            --bg-color: #ffffff;
            --text-color: #212529;
            --border-color: #dee2e6;
        }

        body {
            padding-bottom: 70px; /* Space for bottom nav on mobile */
        }

        /* Desktop Navbar (Top) */
        .desktop-nav {
            background: var(--bg-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 1px solid var(--border-color);
        }

        .desktop-nav .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }

        .desktop-nav .nav-link {
            color: var(--text-color) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            margin: 0 0.25rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .desktop-nav .nav-link:hover {
            color: var(--hover-color) !important;
            background-color: rgba(13, 110, 253, 0.1);
            transform: translateY(-2px);
        }

        .desktop-nav .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(64, 92, 245, 0.1);
        }

        /* Mobile Bottom Navbar */
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-color);
            border-top: 1px solid var(--border-color);
            z-index: 1000;
            box-shadow: 0 -2px 20px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
        }

        .mobile-nav .nav {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0.5rem 0;
        }

        .mobile-nav .nav-item {
            flex: 1;
            text-align: center;
        }

        .mobile-nav .nav-link {
            color: var(--secondary-color) !important;
            padding: 0.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
        }

        .mobile-nav .nav-link:hover {
            color: var(--hover-color) !important;
            background-color: rgba(13, 110, 253, 0.1);
            transform: translateY(-3px);
        }

        .mobile-nav .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(64, 92, 245, 0.1);
        }

        .mobile-nav .nav-link i {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .mobile-nav .nav-link span {
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Active indicator for mobile */
        .mobile-nav .nav-link.active::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 0 0 3px 3px;
        }

        /* Content styling */
        .content-section {
            min-height: 100vh;
            padding: 2rem 0;
            display: none;
        }

        .content-section.active {
            display: block;
        }

        .content-card {
            background: var(--bg-color);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .content-card h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Responsive breakpoints */
        @media (max-width: 991.98px) {
            .desktop-nav {
                display: none !important;
            }
            body {
                padding-top: 0;
            }
        }

        @media (min-width: 992px) {
            .mobile-nav {
                display: none !important;
            }
            body {
                padding-top: 80px;
                padding-bottom: 0;
            }
        }

        /* Animation for smooth transitions */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <!-- Desktop Navigation (Top) -->
    <nav class="navbar navbar-expand-lg desktop-nav fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <i class="fas fa-cube me-2"></i>MyApp
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="#home" data-section="home">
                    <i class="fas fa-home me-2"></i>Beranda
                </a>
                <a class="nav-link" href="#explore" data-section="explore">
                    <i class="fas fa-search me-2"></i>Jelajah
                </a>
                <a class="nav-link" href="#notifications" data-section="notifications">
                    <i class="fas fa-bell me-2"></i>Notifikasi
                </a>
                <a class="nav-link" href="#messages" data-section="messages">
                    <i class="fas fa-envelope me-2"></i>Pesan
                </a>
                <a class="nav-link" href="#profile" data-section="profile">
                    <i class="fas fa-user me-2"></i>Profil
                </a>
            </div>
        </div>
    </nav>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-nav d-lg-none">
        <div class="nav">
            <div class="nav-item">
                <a class="nav-link active" href="#home" data-section="home">
                    <i class="fas fa-home"></i>
                    <span>Beranda</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="#explore" data-section="explore">
                    <i class="fas fa-search"></i>
                    <span>Jelajah</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="#notifications" data-section="notifications">
                    <i class="fas fa-bell"></i>
                    <span>Notifikasi</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="#messages" data-section="messages">
                    <i class="fas fa-envelope"></i>
                    <span>Pesan</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="#profile" data-section="profile">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid">
        <!-- Home Section -->
        <section id="home" class="content-section active fade-in">
            <div class="container">
                <div class="content-card">
                    <h2><i class="fas fa-home me-2"></i>Selamat Datang di Beranda</h2>
                    <p class="lead">Ini adalah halaman beranda aplikasi Anda. Di desktop, menu berada di atas seperti navbar tradisional. Di tablet dan mobile, menu berada di bawah seperti Instagram untuk kemudahan akses dengan jempol.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Fitur Desktop:</h4>
                            <ul>
                                <li>Menu navbar di bagian atas</li>
                                <li>Hover effects yang menarik</li>
                                <li>Layout horizontal tradisional</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h4>Fitur Mobile:</h4>
                            <ul>
                                <li>Bottom navigation bar</li>
                                <li>Icon dengan label</li>
                                <li>Mudah dijangkau dengan jempol</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Explore Section -->
        <section id="explore" class="content-section">
            <div class="container">
                <div class="content-card">
                    <h2><i class="fas fa-search me-2"></i>Halaman Jelajah</h2>
                    <p>Temukan konten menarik di halaman jelajah. Navbar akan menyesuaikan dengan ukuran layar secara otomatis.</p>
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Konten 1</h5>
                                    <p class="card-text">Deskripsi konten yang menarik.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Konten 2</h5>
                                    <p class="card-text">Deskripsi konten yang menarik.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Konten 3</h5>
                                    <p class="card-text">Deskripsi konten yang menarik.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Notifications Section -->
        <section id="notifications" class="content-section">
            <div class="container">
                <div class="content-card">
                    <h2><i class="fas fa-bell me-2"></i>Notifikasi</h2>
                    <p>Lihat semua notifikasi terbaru Anda di sini.</p>
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Notifikasi Baru</h5>
                                <small>3 hari yang lalu</small>
                            </div>
                            <p class="mb-1">Anda memiliki pesan baru dari sistem.</p>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Update Aplikasi</h5>
                                <small class="text-muted">1 minggu yang lalu</small>
                            </div>
                            <p class="mb-1">Aplikasi telah diperbarui ke versi terbaru.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Messages Section -->
        <section id="messages" class="content-section">
            <div class="container">
                <div class="content-card">
                    <h2><i class="fas fa-envelope me-2"></i>Pesan</h2>
                    <p>Kelola semua percakapan Anda di sini.</p>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">John Doe</h5>
                                        <small>12:30</small>
                                    </div>
                                    <p class="mb-1">Halo, bagaimana kabarmu?</p>
                                    <small>Aktif sekarang</small>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Jane Smith</h5>
                                        <small>11:45</small>
                                    </div>
                                    <p class="mb-1">Terima kasih untuk bantuannya!</p>
                                    <small>Terakhir online 2 jam lalu</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Profile Section -->
        <section id="profile" class="content-section">
            <div class="container">
                <div class="content-card">
                    <h2><i class="fas fa-user me-2"></i>Profil Pengguna</h2>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="profile-avatar mb-3">
                                <i class="fas fa-user-circle fa-5x text-primary"></i>
                            </div>
                            <h4>Nama Pengguna</h4>
                            <p class="text-muted">@username</p>
                        </div>
                        <div class="col-md-8">
                            <h4>Informasi Profil</h4>
                            <form>
                                <div class="mb-3">
                                    <label for="fullName" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="fullName" value="Nama Pengguna">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" value="user@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control" id="bio" rows="3">Deskripsi singkat tentang diri Anda...</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link[data-section]');
            const sections = document.querySelectorAll('.content-section');

            function showSection(sectionId) {
                // Hide all sections
                sections.forEach(section => {
                    section.classList.remove('active');
                });

                // Show target section
                const targetSection = document.getElementById(sectionId);
                if (targetSection) {
                    targetSection.classList.add('active', 'fade-in');
                }

                // Update active nav links
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });

                // Set active nav links
                const activeLinks = document.querySelectorAll(`[data-section="${sectionId}"]`);
                activeLinks.forEach(link => {
                    link.classList.add('active');
                });
            }

            // Add click event listeners
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionId = this.getAttribute('data-section');
                    showSection(sectionId);
                    
                    // Smooth scroll to top on mobile
                    if (window.innerWidth < 992) {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                // Force recalculation of layouts on resize
                const activeSection = document.querySelector('.content-section.active');
                if (activeSection) {
                    activeSection.classList.remove('fade-in');
                    setTimeout(() => {
                        activeSection.classList.add('fade-in');
                    }, 10);
                }
            });
        });

        // Add some interactive elements
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('nav-link')) {
                // Add ripple effect
                const ripple = document.createElement('span');
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255,255,255,0.6);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;
                
                const rect = e.target.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
                
                e.target.style.position = 'relative';
                e.target.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            }
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>