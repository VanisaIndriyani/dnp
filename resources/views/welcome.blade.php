<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Center Part Production</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
        }
        .hero-section {
            background-color: #C62828;
            color: #fff;
            padding: 80px 0;
            text-align: center;
            border-bottom-left-radius: 50% 20px;
            border-bottom-right-radius: 50% 20px;
        }
        .hero-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.3);
            object-fit: cover;
            margin-bottom: 20px;
            background-color: #fff;
        }
        .btn-login-hero {
            background-color: #fff;
            color: #C62828;
            font-weight: bold;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .btn-login-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            color: #b71c1c;
        }
        .visi-misi-section {
            padding: 60px 0;
        }
        .section-card {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
            height: 100%;
            transition: transform 0.3s;
        }
        .section-card:hover {
            transform: translateY(-5px);
        }
        .section-title {
            color: #C62828;
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
            display: inline-block;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50%;
            height: 3px;
            background-color: #C62828;
        }
        .list-custom ul {
            list-style: none;
            padding-left: 0;
        }
        .list-custom li {
            position: relative;
            padding-left: 25px;
            margin-bottom: 15px;
            color: #555;
            line-height: 1.6;
        }
        .list-custom li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            color: #C62828;
        }
        .footer {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            margin-top: 50px;
        }
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
                border-bottom-left-radius: 20px;
                border-bottom-right-radius: 20px;
            }
            .display-4 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger text-wrap" style="max-width: 200px; line-height: 1.2; font-size: 1rem;" href="#">
                <i class="fas fa-industry me-2"></i>TRAINING CENTER<br>PART PRODUCTION
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-danger rounded-pill px-4" href="{{ route('quick-attendance.index') }}">
                                <i class="fas fa-clock me-1"></i> Absen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-danger rounded-pill px-4" href="{{ route('login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section mt-5">
        <div class="container">
            <img src="{{ asset('img/logo.jpeg') }}" alt="DNP Logo" class="hero-logo">
            <h1 class="display-4 fw-bold">Selamat Datang di Training Center<br>Part Production</h1>
            <p class="lead mb-4">Sistem Manajemen Absensi dan Evaluasi Karyawan</p>
            @guest
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="{{ route('quick-attendance.index') }}" class="btn btn-outline-light rounded-pill px-4 py-3 fw-bold shadow-sm" style="border-width: 2px;">
                        <i class="fas fa-fingerprint me-2"></i> Absensi Cepat
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-light text-danger rounded-pill px-4 py-3 fw-bold shadow-sm">
                        <i class="fas fa-sign-in-alt me-2"></i> Masuk Sekarang
                    </a>
                </div>
            @endguest
        </div>
    </header>

    <!-- Visi Misi Section -->
    <section class="visi-misi-section">
        <div class="container">
            <div class="row g-4">
                <!-- Visi -->
                <div class="col-md-5">
                    <div class="section-card">
                        <h2 class="section-title">
                            <i class="fas fa-eye me-2"></i>Visi
                        </h2>
                        <div class="list-custom">
                            <ul>
                                <li>Menjadi pemimpin pasar dalam industri filter otomotif.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Misi -->
                <div class="col-md-7">
                    <div class="section-card">
                        <h2 class="section-title">
                            <i class="fas fa-bullseye me-2"></i>Misi
                        </h2>
                        <div class="list-custom">
                            <ul>
                                <li>Meningkatkan kuantitas produksi.</li>
                                <li>Meningkatkan kualitas produk.</li>
                                <li>Melakukan respons dan aksi cepat terhadap lingkungan.</li>
                                <li>Menjadi produsen produk filter berkualitas tinggi yang memenuhi kebutuhan pasar domestik dan ekspor.</li>
                                <li>Mencapai tingkat kepuasan pelanggan tertinggi dalam hal kualitas, ketepatan waktu, pengiriman, dan harga yang kompetitif.</li>
                                <li>Memberikan layanan yang memuaskan.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} Training Center Part Production. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
