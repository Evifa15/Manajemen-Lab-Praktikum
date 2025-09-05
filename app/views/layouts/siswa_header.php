<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - Lab Praktikum</title>
    <link rel="stylesheet" href="<?= BASEURL; ?>/css/main-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-content-wrapper">
                <h1>Lab Praktikum</h1>
                <nav>
                    <a href="<?= BASEURL; ?>/siswa/dashboard" class="sidebar-item" id="nav-dashboard-siswa">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= BASEURL; ?>/siswa/katalog" class="sidebar-item" id="nav-katalog-siswa">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        <span>Katalog Barang</span>
                    </a>
                    <a href="<?= BASEURL; ?>/siswa/pengembalian" class="sidebar-item" id="nav-pengembalian-siswa">
                         <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8a4 4 0 00-4 4v1m12-8l-4-4m4 4l4 4m-4-4v12"></path></svg>
                        <span>Pengembalian Barang</span>
                    </a>
                    <a href="<?= BASEURL; ?>/siswa/riwayat" class="sidebar-item" id="nav-riwayat-siswa">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Riwayat Peminjaman</span>
                    </a>
                </nav>
            </div>
            <div class="logout-button-wrapper">
                <a href="<?= BASEURL; ?>/logout" class="logout-button">
                    <svg fill="currentColor" viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <div class="main-content">
            <header class="header">
                <h2><?= $title; ?></h2>
                <div class="user-profile">
                    <span class="user-greeting">Selamat datang, <strong><?= $_SESSION['username'] ?? 'Siswa'; ?></strong>!</span>
                </div>
            </header>
            <div class="content-area">