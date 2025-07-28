<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan HKI | Politeknik Negeri Banjarmasin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
        html {
            scroll-behavior: smooth;
            }
        .mobile-menu-transition {
            transition: all 0.3s ease-in-out;
            }
        @media (max-width: 768px) {
            .hero-text {
                font-size: 1.75rem;
                line-height: 1.2;
            }
            }
        </style>
    </head>
<body class="bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 bg-white shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('img/Logo-poliban.png') }}" alt="Logo Poliban" class="h-8 w-8">
                    <span class="font-bold text-lg text-blue-800">HKI POLIBAN</span>
                    </div>
                
                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#beranda" class="text-gray-700 hover:text-blue-600 transition duration-300">Beranda</a>
                    <a href="#panduan" class="text-gray-700 hover:text-blue-600 transition duration-300">Panduan</a>
                        <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                        </a>
                    </div>
                
                <!-- Tombol Menu Mobile -->
                <div class="md:hidden">
                    <button id="menu-button" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Menu Mobile -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t mobile-menu-transition">
            <div class="px-4 py-3 space-y-3">
                <a href="#beranda" class="block text-gray-700 hover:text-blue-600 py-2 mobile-menu-link">
                    <i class="fas fa-home mr-3"></i>Beranda
                </a>
                <a href="#panduan" class="block text-gray-700 hover:text-blue-600 py-2 mobile-menu-link">
                    <i class="fas fa-book mr-3"></i>Panduan
                </a>
                <a href="{{ route('login') }}" class="block bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition duration-300 text-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Sistem
                </a>
                </div>
            </div>
        </nav>

    <!-- Bagian Utama -->
    <section id="beranda" class="pt-16 pb-20 bg-gradient-to-br from-blue-50 via-white to-blue-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center py-12 lg:py-20">
                <h1 class="hero-text text-2xl sm:text-3xl lg:text-5xl font-bold mb-6 text-gray-800">
                    Administrasi Pengajuan <br>
                    <span class="text-yellow-500">Hak Kekayaan Intelektual (HKI)</span>
                </h1>
                <p class="max-w-3xl mx-auto text-gray-600 mb-8 text-sm sm:text-base lg:text-lg leading-relaxed px-4">
                    Sistem ini diperuntukkan bagi civitas akademik Politeknik Negeri Banjarmasin untuk mengajukan, memantau, dan mengelola administrasi permohonan HKI secara terpusat dan terintegrasi.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4 px-4">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto bg-blue-600 text-white px-8 py-4 rounded-full hover:bg-blue-700 transition duration-300 shadow-lg">
                        <i class="fas fa-rocket mr-2"></i>Masuk ke Sistem
                    </a>
                    <a href="#panduan" class="w-full sm:w-auto border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-full hover:bg-blue-50 transition duration-300">
                        <i class="fas fa-book-open mr-2"></i>Panduan Pengajuan
                    </a>
                </div>
                <p class="mt-8 text-xs sm:text-sm text-gray-500 italic">
                    Aplikasi internal milik P3M Politeknik Negeri Banjarmasin
                </p>
            </div>
            </div>
        </section>

    <!-- Bagian Tentang -->
    <section id="tentang" class="py-16 lg:py-20 bg-white hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-4">Tentang Sistem HKI</h2>
                <div class="w-20 h-1 bg-blue-600 mx-auto"></div>
            </div>
            <div class="grid md:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="space-y-6">
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                        Sistem Pengajuan HKI Politeknik Negeri Banjarmasin adalah platform digital yang dirancang khusus untuk memfasilitasi proses pengajuan Hak Kekayaan Intelektual bagi seluruh civitas akademik.
                    </p>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                        Melalui sistem ini, dosen, mahasiswa, dan peneliti dapat mengajukan berbagai jenis HKI seperti hak cipta, paten, dan merek dagang dengan proses yang lebih efisien dan terintegrasi.
                    </p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-blue-600 text-2xl font-bold">50+</div>
                            <div class="text-gray-600 text-sm">Pengajuan Diproses</div>
                </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-green-600 text-2xl font-bold">95%</div>
                            <div class="text-gray-600 text-sm">Tingkat Kepuasan</div>
                        </div>
                    </div>
                        </div>
                <div class="order-first md:order-last">
                    <img src="{{ asset('images/gedung-p3m.jpg') }}" alt="Gedung P3M" class="w-full rounded-lg shadow-lg">
                    </div>
                </div>
            </div>
        </section>

    <!-- Bagian Fitur -->
    <section id="fitur" class="py-16 lg:py-20 bg-gray-50 hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-4">Fitur Unggulan</h2>
                <div class="w-20 h-1 bg-blue-600 mx-auto mb-6"></div>
                <p class="max-w-2xl mx-auto text-gray-600 text-sm sm:text-base">
                    Sistem yang dilengkapi dengan berbagai fitur canggih untuk memudahkan proses pengajuan HKI
                    </p>
                </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <div class="text-blue-600 text-3xl mb-4">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-3">Pengajuan Daring</h3>
                    <p class="text-gray-600 text-sm">Ajukan permohonan HKI secara daring dengan formulir yang mudah digunakan</p>
                        </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <div class="text-green-600 text-3xl mb-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-3">Pelacakan Status</h3>
                    <p class="text-gray-600 text-sm">Pantau perkembangan pengajuan HKI secara waktu nyata dari dasbor</p>
                        </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <div class="text-purple-600 text-3xl mb-4">
                        <i class="fas fa-signature"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-3">Tanda Tangan Digital</h3>
                    <p class="text-gray-600 text-sm">Sistem tanda tangan digital terintegrasi untuk validasi dokumen</p>
                        </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <div class="text-yellow-600 text-3xl mb-4">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-3">Pemberitahuan</h3>
                    <p class="text-gray-600 text-sm">Dapatkan pemberitahuan otomatis untuk setiap pembaruan status pengajuan</p>
                        </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <div class="text-red-600 text-3xl mb-4">
                        <i class="fas fa-download"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-3">Unduh Dokumen</h3>
                    <p class="text-gray-600 text-sm">Unduh sertifikat dan dokumen HKI yang telah disetujui</p>
                        </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <div class="text-indigo-600 text-3xl mb-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-3">Multi Pengguna</h3>
                    <p class="text-gray-600 text-sm">Mendukung pengajuan dengan beberapa pencipta dan kolaborasi tim</p>
                    </div>
                </div>
            </div>
        </section>

    <!-- Bagian Panduan -->
    <section id="panduan" class="py-16 lg:py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-4">Panduan Pengajuan</h2>
                <div class="w-20 h-1 bg-blue-600 mx-auto mb-6"></div>
                <p class="max-w-2xl mx-auto text-gray-600 text-sm sm:text-base">
                    Ikuti langkah-langkah berikut untuk mengajukan HKI dengan mudah
                </p>
                </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                    <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-blue-600 font-bold text-xl">1</span>
                    </div>
                    <h3 class="font-semibold mb-2">Daftar/Masuk</h3>
                    <p class="text-gray-600 text-sm">Masuk ke sistem menggunakan akun yang telah terdaftar</p>
                    </div>
                    <div class="text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-green-600 font-bold text-xl">2</span>
                    </div>
                    <h3 class="font-semibold mb-2">Isi Formulir</h3>
                    <p class="text-gray-600 text-sm">Lengkapi formulir pengajuan dengan data yang akurat</p>
                    </div>
                    <div class="text-center">
                    <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-yellow-600 font-bold text-xl">3</span>
                    </div>
                    <h3 class="font-semibold mb-2">Unggah Dokumen</h3>
                    <p class="text-gray-600 text-sm">Unggah semua dokumen pendukung yang diperlukan</p>
                    </div>
                    <div class="text-center">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-purple-600 font-bold text-xl">4</span>
                    </div>
                    <h3 class="font-semibold mb-2">Kirim & Pantau</h3>
                    <p class="text-gray-600 text-sm">Kirim pengajuan dan pantau statusnya secara berkala</p>
                </div>
            </div>
            <div class="mt-12 bg-blue-50 p-6 lg:p-8 rounded-lg">
                <h3 class="text-lg font-semibold mb-4 text-center">Dokumen yang Diperlukan</h3>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>KTP Pencipta</span>
                </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Surat Pernyataan Keaslian</span>
            </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Berkas Karya/Ciptaan</span>
                </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Surat Pengalihan Hak</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Dokumen Pendukung Lainnya</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Bukti Pembayaran</span>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('img/Logo-poliban.png') }}" alt="Logo Poliban" class="h-8 w-8">
                        <span class="font-bold text-lg">HKI POLIBAN</span>
                    </div>
                    <p class="text-gray-300 text-sm">
                        Sistem Pengajuan HKI Politeknik Negeri Banjarmasin untuk mendukung inovasi dan kreativitas civitas akademik.
                    </p>
                </div>
                    <div>
                    <h4 class="font-semibold mb-4">Kontak</h4>
                    <div class="space-y-2 text-sm text-gray-300">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3"></i>
                            <span>Jl. Brigjen H. Hasan Basri, Banjarmasin</span>
                    </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone mr-3"></i>
                            <span>(0511) 3305052</span>
                    </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>p3m@poliban.ac.id</span>
                        </div>
                    </div>
                    </div>
                    <div>
                    <h4 class="font-semibold mb-4">Tautan Penting</h4>
                    <div class="space-y-2 text-sm">
                        <a href="https://poliban.ac.id" class="text-gray-300 hover:text-white block transition duration-300">
                            <i class="fas fa-external-link-alt mr-2"></i>Situs Web Poliban
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white block transition duration-300">
                            <i class="fas fa-book mr-2"></i>Panduan HKI
                            </a>
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white block transition duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>Portal Masuk
                            </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-400">
                <p>&copy; 2025 P3M Politeknik Negeri Banjarmasin. Hak cipta dilindungi undang-undang.</p>
                </div>
            </div>
        </footer>

    <!-- Skrip -->
        <script>
        // Toggle menu mobile
        const menuButton = document.getElementById('menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuLinks = document.querySelectorAll('.mobile-menu-link');

        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Tutup menu mobile saat mengklik tautan
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });

        // Smooth scrolling untuk tautan anchor
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                    const navHeight = document.querySelector('nav').offsetHeight;
                    const targetPosition = target.offsetTop - navHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                        });
                    }
                });
            });

        // Tutup menu mobile saat mengklik di luar
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !menuButton.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                }
            });

        // Highlight bagian aktif
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('nav a[href^="#"]');
            
            sections.forEach(section => {
                const rect = section.getBoundingClientRect();
                const navHeight = document.querySelector('nav').offsetHeight;
            
                if (rect.top <= navHeight + 50 && rect.bottom >= navHeight + 50) {
                    const activeId = section.getAttribute('id');
                    
                    navLinks.forEach(link => {
                        link.classList.remove('text-blue-600', 'font-semibold');
                        if (link.getAttribute('href') === `#${activeId}`) {
                            link.classList.add('text-blue-600', 'font-semibold');
                        }
                });
            }
            });
        });
        </script>
    </body>
</html>
