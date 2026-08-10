<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio - Junior Web Developer</title>
    <!-- CSS Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        /* styling  */
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        .sidebar {
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
            min-height: 100vh;
        }

        .profile-img {
            width: 100%;
            max-width: 200px;
            height: auto;
            object-fit: cover;
            filter: grayscale(20%);
        }

        .article-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .article-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- SIDEBAR KIRI (Info Profil & Link Ke Website Usaha) -->
            <aside class="col-lg-3 col-md-4 sidebar p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Brand / Nama Utama -->
                    <h2 class="fw-bold text-uppercase tracking-wide mb-1">FAIRUUZ</h2>
                    <p class="text-muted fw-semibold mb-4">JUNIOR WEB DEVELOPER</p>

                    <!-- Foto Profil -->
                    <div class="text-center mb-4">
                        <img src="assets/images/profile.jpg" alt="Foto Profil" class="profile-img shadow-sm mb-3">
                    </div>

                    <!-- Bio Singkat -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase text-secondary small">Tentang Saya</h6>
                        <p class="small text-muted">
                            Seorang Web Developer berdedikasi tinggi yang berfokus pada pengembangan antarmuka web yang
                            bersih, responsif, dan ramah pengguna.
                        </p>
                    </div>

                    <!-- Skills / Keahlian -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase text-secondary small mb-2">Keahlian</h6>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-dark">HTML5</span>
                            <span class="badge bg-dark">CSS3</span>
                            <span class="badge bg-dark">PHP</span>
                            <span class="badge bg-dark">JavaScript</span>
                            <span class="badge bg-dark">TypeScript</span>
                            <span class="badge bg-dark">Bootstrap 5</span>
                            <span class="badge bg-dark">MySQL</span>
                            <span class="badge bg-dark">Git</span>
                        </div>
                    </div>
                </div>

                <!-- Tautan Ke Website Usaha -->
                <div class="pt-3 border-top">
                    <p class="small text-muted mb-2">Lihat Mini Project Usaha:</p>
                    <a href="company.php" class="btn btn-danger w-100 fw-bold">
                        Kunjungi Toko Usaha &rarr;
                    </a>
                </div>
            </aside>

            <!-- KONTEN UTAMA KANAN (Portofolio & Pengalaman) -->
            <main class="col-lg-9 col-md-8 p-4 p-md-5">

                <!-- Header Konten Utama -->
                <header class="mb-5 border-bottom pb-3">
                    <h1 class="fw-bold text-dark mb-2">Portofolio & Proyek Terbaru</h1>
                    <p class="text-muted fs-5">Kumpulan tugas praktik dan eksplorasi pengembangan web.</p>
                </header>

                <!-- Grid Konten / Project Showcase -->
                <section class="row g-4">

                    <!-- Card 1 -->
                    <div class="col-md-6">
                        <article class="article-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="text-muted small fw-bold">PROYEK 01</span>
                                <h4 class="fw-bold mt-1 text-dark">Sistem Informasi Profil Web</h4>
                                <p class="text-muted small">
                                    Mengimplementasikan desain antarmuka responsif menggunakan skema Bootstrap 5 lokal,
                                    terstruktur, dan bersih.
                                </p>
                            </div>
                            <a href="#" class="text-danger fw-bold text-decoration-none small">Lihat Detail &rarr;</a>
                        </article>
                    </div>

                    <!-- Card 2 -->
                    <div class="col-md-6">
                        <article class="article-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="text-muted small fw-bold">PROYEK 02</span>
                                <h4 class="fw-bold mt-1 text-dark">E-Commerce</h4>
                                <p class="text-muted small">
                                    Aplikasi web dinamis berbasis PHP dan database untuk menampilkan katalog produk UMKM
                                    secara interaktif.
                                </p>
                            </div>
                            <a href="company.php" class="text-danger fw-bold text-decoration-none small">Lihat Detail
                                &rarr;</a>
                        </article>
                    </div>

                    <!-- Card 3 -->
                    <div class="col-md-6">
                        <article class="article-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="text-muted small fw-bold">PEDOMAN KODE</span>
                                <h4 class="fw-bold mt-1 text-dark">Clean Code & Best Practices</h4>
                                <p class="text-muted small">
                                    Penerapan prinsip penulisan kode terstruktur, modular, serta efisien sesuai standar
                                    unit kompetensi SKKNI.
                                </p>
                            </div>
                            <a href="#" class="text-danger fw-bold text-decoration-none small">Baca Selengkapnya
                                &rarr;</a>
                        </article>
                    </div>

                    <!-- Card 4 -->
                    <div class="col-md-6">
                        <article class="article-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="text-muted small fw-bold">INTEGRASI</span>
                                <h4 class="fw-bold mt-1 text-dark">Penggunaan Library & Component</h4>
                                <p class="text-muted small">
                                    Menggunakan komponen pre-existing seperti Bootstrap grid, card, dan modal untuk
                                    mempercepat waktu pengembangan.
                                </p>
                            </div>
                            <a href="#" class="text-danger fw-bold text-decoration-none small">Baca Selengkapnya
                                &rarr;</a>
                        </article>
                    </div>

                </section>

            </main>

        </div>
    </div>

    <!-- JS Bootstrap 5 -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>