<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistem Informasi Administrasi Pengajuan HKI - Politeknik Negeri Banjarmasin</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            .gradient-bg {
                /* Fallback gradient jika gambar belum tersedia */
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                /* Background image gedung P3M */
                background-image: url('{{ asset("images/gedung-p3m.jpg") }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                position: relative;
            }
            .gradient-bg::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                /* Overlay gelap untuk memastikan teks tetap terbaca */
                background: linear-gradient(135deg, rgba(102, 126, 234, 0.85) 0%, rgba(118, 75, 162, 0.85) 100%);
                z-index: 1;
            }
            .gradient-bg > * {
                position: relative;
                z-index: 2;
            }
            .card-hover {
                transition: all 0.3s ease;
            }
            .card-hover:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
        </style>
    </head>
    <body class="bg-white text-gray-800 overflow-x-hidden">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg fixed w-full z-50">
            <div class="container mx-auto px-6 py-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('img/logo-hki.png') }}" alt="Logo HKI" class="h-10 w-10">
                        <span class="text-xl font-bold text-gray-800">HKI POLIBAN</span>
                    </div>
                    <div class="hidden md:flex space-x-6">
                        <a href="#beranda" class="text-gray-600 hover:text-blue-600 transition duration-300">Beranda</a>
                        <a href="#tentang" class="text-gray-600 hover:text-blue-600 transition duration-300">Tentang</a>
                        <a href="#fitur" class="text-gray-600 hover:text-blue-600 transition duration-300">Fitur</a>
                        <a href="#hki" class="text-gray-600 hover:text-blue-600 transition duration-300">Info HKI</a>
                        <a href="#kontak" class="text-gray-600 hover:text-blue-600 transition duration-300">Kontak</a>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-600 hover:text-white transition duration-300">
                            <i class="fas fa-user-plus mr-2"></i>Daftar
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="beranda" class="gradient-bg text-white py-32 relative overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="container mx-auto px-6 text-center relative z-10">
                <div class="animate-float">
                    <i class="fas fa-certificate text-6xl mb-6 text-yellow-300"></i>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    Sistem Informasi Administrasi<br>
                    <span class="text-yellow-300">Pengajuan Hak Kekayaan Intelektual</span>
                </h1>
                <p class="text-lg md:text-xl mb-8 max-w-3xl mx-auto opacity-90">
                    Mempermudah proses pengajuan Hak Kekayaan Intelektual di lingkungan 
                    Politeknik Negeri Banjarmasin dengan sistem yang terintegrasi dan efisien.
                </p>
                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('login') }}" class="bg-white text-blue-600 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition duration-300 transform hover:scale-105">
                        <i class="fas fa-rocket mr-2"></i>Mulai Pengajuan
                    </a>
                    <a href="#tentang" class="border-2 border-white text-white px-8 py-4 rounded-full font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                        <i class="fas fa-info-circle mr-2"></i>Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
            
            <!-- Floating Elements -->
            <div class="absolute top-20 left-10 opacity-20">
                <i class="fas fa-lightbulb text-4xl text-yellow-300"></i>
            </div>
            <div class="absolute bottom-20 right-10 opacity-20">
                <i class="fas fa-cogs text-4xl text-white"></i>
            </div>
        </section>

        <!-- About Section -->
        <section id="tentang" class="py-20 px-6 md:px-20">
            <div class="container mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4 text-gray-800">Tentang Sistem HKI</h2>
                    <div class="w-24 h-1 bg-blue-600 mx-auto mb-6"></div>
                    <p class="text-lg max-w-3xl mx-auto text-gray-600">
                        Sistem ini dirancang khusus untuk memfasilitasi civitas akademika Politeknik Negeri Banjarmasin 
                        dalam mengelola dan mengajukan permohonan Hak Kekayaan Intelektual dengan efisien dan terdokumentasi dengan baik.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h3 class="text-2xl font-bold mb-6 text-gray-800">Mengapa Memilih Sistem Kami?</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-check-circle text-2xl text-green-600 mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Proses Digital Terintegrasi</h4>
                                    <p class="text-gray-600">Semua proses pengajuan dilakukan secara digital dari awal hingga akhir.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-shield-alt text-2xl text-blue-600 mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Keamanan Data Terjamin</h4>
                                    <p class="text-gray-600">Sistem keamanan berlapis untuk melindungi data dan dokumen Anda.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-clock text-2xl text-purple-600 mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Tracking Real-time</h4>
                                    <p class="text-gray-600">Pantau status pengajuan Anda kapan saja dan dimana saja.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-blue-100 to-purple-100 p-8 rounded-2xl">
                            <i class="fas fa-laptop-code text-6xl text-blue-600 mb-4"></i>
                            <h4 class="text-xl font-bold text-gray-800 mb-2">Interface Modern</h4>
                            <p class="text-gray-600">Desain yang user-friendly dan mudah digunakan</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="fitur" class="bg-gray-50 py-20 px-6 md:px-20">
            <div class="container mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4 text-gray-800">Fitur Unggulan</h2>
                    <div class="w-24 h-1 bg-blue-600 mx-auto mb-6"></div>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Berbagai fitur canggih yang memudahkan proses pengajuan HKI Anda
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-file-upload text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Pengajuan Online</h3>
                        <p class="text-gray-600">Ajukan HKI secara online dengan form yang mudah diisi dan upload dokumen yang aman.</p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-tasks text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Validasi Otomatis</h3>
                        <p class="text-gray-600">Sistem validasi otomatis oleh P3M dan persetujuan direktur melalui dashboard khusus.</p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover text-center">
                        <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-bell text-2xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Notifikasi Real-time</h3>
                        <p class="text-gray-600">Dapatkan notifikasi instant setiap ada perubahan status pengajuan Anda.</p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Dashboard Analytics</h3>
                        <p class="text-gray-600">Monitor statistik dan progress pengajuan melalui dashboard yang informatif.</p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-file-pdf text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Dokumen Digital</h3>
                        <p class="text-gray-600">Kelola semua dokumen dalam format digital dengan sistem backup otomatis.</p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover text-center">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-mobile-alt text-2xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Mobile Responsive</h3>
                        <p class="text-gray-600">Akses sistem dari perangkat apapun dengan tampilan yang optimal.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Process Section -->
        <section class="py-20 px-6 md:px-20">
            <div class="container mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4 text-gray-800">Alur Proses Pengajuan</h2>
                    <div class="w-24 h-1 bg-blue-600 mx-auto mb-6"></div>
                    <p class="text-lg text-gray-600">Proses pengajuan HKI yang mudah dan terstruktur</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">1</div>
                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Daftar & Login</h3>
                        <p class="text-gray-600">Buat akun dan login ke sistem</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">2</div>
                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Isi Form</h3>
                        <p class="text-gray-600">Lengkapi form pengajuan dan upload dokumen</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-yellow-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">3</div>
                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Validasi</h3>
                        <p class="text-gray-600">Proses validasi oleh P3M dan persetujuan direktur</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">4</div>
                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Selesai</h3>
                        <p class="text-gray-600">Pengajuan selesai dan sertifikat diterbitkan</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="gradient-bg text-white py-20">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Siap Mengajukan HKI Anda?</h2>
                <p class="text-lg mb-8 opacity-90">Bergabunglah dengan ratusan dosen dan mahasiswa yang telah mempercayai sistem kami</p>
                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition duration-300 transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-4 rounded-full font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="kontak" class="py-20 px-6 md:px-20 bg-gray-50">
            <div class="container mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4 text-gray-800">Hubungi Kami</h2>
                    <div class="w-24 h-1 bg-blue-600 mx-auto mb-6"></div>
                    <p class="text-lg text-gray-600">Butuh bantuan? Tim kami siap membantu Anda</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-lg text-center card-hover">
                        <i class="fas fa-map-marker-alt text-3xl text-blue-600 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">Alamat</h3>
                        <p class="text-gray-600">Jl. Brigjen H. Hasan Basry<br>Banjarmasin, Kalimantan Selatan</p>
                    </div>
                    <div class="bg-white p-8 rounded-xl shadow-lg text-center card-hover">
                        <i class="fas fa-phone text-3xl text-green-600 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">Telepon</h3>
                        <p class="text-gray-600">(0511) 3305052<br>Senin - Jumat: 08:00 - 16:00</p>
                    </div>
                    <div class="bg-white p-8 rounded-xl shadow-lg text-center card-hover">
                        <i class="fas fa-envelope text-3xl text-purple-600 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">Email</h3>
                        <p class="text-gray-600">hki@poliban.ac.id<br>info@poliban.ac.id</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Informasi HKI & Hak Cipta Section -->
        <section id="hki" class="py-20 px-6 md:px-20 bg-white">
            <div class="container mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4 text-gray-800">Apa itu Hak Kekayaan Intelektual?</h2>
                    <div class="w-24 h-1 bg-blue-600 mx-auto mb-6"></div>
                    <p class="text-lg max-w-3xl mx-auto text-gray-600">
                        Hak Kekayaan Intelektual (HKI) adalah hak yang timbul dari hasil olah pikir yang menghasilkan suatu produk atau proses yang berguna bagi manusia. Menurut Direktorat Jenderal Kekayaan Intelektual (DJKI), HKI meliputi hak cipta, paten, merek, desain industri, rahasia dagang, dan sebagainya.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                    <div>
                        <h3 class="text-2xl font-bold mb-6 text-gray-800">Fokus: Hak Cipta</h3>
                        <ul class="space-y-4 list-disc list-inside text-gray-600">
                            <li><span class="font-semibold text-gray-800">Definisi:</span> Hak Cipta merupakan hak eksklusif bagi Pencipta atas karya ciptaannya di bidang ilmu pengetahuan, seni, dan sastra.</li>
                            <li><span class="font-semibold text-gray-800">Lingkup Perlindungan:</span> Meliputi karya tulis, musik, gambar, program komputer, fotografi, film, dan karya lainnya yang tercantum dalam Undang-Undang Nomor 28 Tahun 2014 tentang Hak Cipta.</li>
                            <li><span class="font-semibold text-gray-800">Masa Berlaku:</span> Selama hidup Pencipta dan 70 tahun setelah Pencipta meninggal dunia (untuk ciptaan perseorangan). Untuk ciptaan yang dimiliki badan hukum, 50 tahun sejak pertama kali diumumkan.</li>
                            <li><span class="font-semibold text-gray-800">Manfaat Pendaftaran:</span> Menjadi bukti kepemilikan resmi, memudahkan penegakan hukum atas pelanggaran, serta meningkatkan nilai ekonomi karya.</li>
                        </ul>
                    </div>

                    <div class="text-center">
                        <img src="{{ asset('img/logo-hki.png') }}" alt="Ilustrasi HKI" class="w-64 mx-auto animate-float" />
                        <p class="text-sm text-gray-500 mt-4">Sumber informasi: <a href="https://www.dgip.go.id/" target="_blank" class="underline">DJKI Kemenkumham RI</a></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-12">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <img src="{{ asset('img/logo-hki.png') }}" alt="Logo HKI" class="h-10 w-10">
                            <span class="text-xl font-bold">HKI POLIBAN</span>
                        </div>
                        <p class="text-gray-400">Sistem Informasi Administrasi Pengajuan HKI Politeknik Negeri Banjarmasin</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Menu</h4>
                        <ul class="space-y-2">
                            <li><a href="#beranda" class="text-gray-400 hover:text-white transition duration-300">Beranda</a></li>
                            <li><a href="#tentang" class="text-gray-400 hover:text-white transition duration-300">Tentang</a></li>
                            <li><a href="#fitur" class="text-gray-400 hover:text-white transition duration-300">Fitur</a></li>
                            <li><a href="#hki" class="text-gray-400 hover:text-white transition duration-300">Info HKI</a></li>
                            <li><a href="#kontak" class="text-gray-400 hover:text-white transition duration-300">Kontak</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Layanan</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition duration-300">Login</a></li>
                            <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-white transition duration-300">Registrasi</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Bantuan</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Ikuti Kami</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                                <i class="fab fa-facebook-f text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                                <i class="fab fa-youtube text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                    <p class="text-gray-400">&copy; 2025 Politeknik Negeri Banjarmasin. Semua hak dilindungi undang-undang.</p>
                </div>
            </div>
        </footer>

        <!-- Scroll to top button -->
        <button id="scrollToTop" class="fixed bottom-6 right-6 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition duration-300 opacity-0 invisible">
            <i class="fas fa-chevron-up"></i>
        </button>

        <script>
            // Smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Scroll to top button
            const scrollToTopBtn = document.getElementById('scrollToTop');
            
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    scrollToTopBtn.classList.remove('opacity-0', 'invisible');
                    scrollToTopBtn.classList.add('opacity-100', 'visible');
                } else {
                    scrollToTopBtn.classList.add('opacity-0', 'invisible');
                    scrollToTopBtn.classList.remove('opacity-100', 'visible');
                }
            });

            scrollToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Mobile menu toggle (if needed)
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        </script>
    </body>
</html>
