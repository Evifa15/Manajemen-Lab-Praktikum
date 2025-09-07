<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - Lab Praktikum</title>
    <link rel="stylesheet" href="<?= BASEURL; ?>/css/main-style.css"> <link rel="stylesheet" href="<?= BASEURL; ?>/css/admin-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-content-wrapper">
                <nav>
                    <a href="<?= BASEURL; ?>/admin/dashboard" class="sidebar-item" id="nav-dashboard">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= BASEURL; ?>/admin/pengguna" class="sidebar-item" id="nav-pengguna">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Manajemen Pengguna</span>
                    </a>
                    <a href="<?= BASEURL; ?>/admin/barang" class="sidebar-item" id="nav-barang">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span>Manajemen Barang</span>
                    </a>
                    <a href="<?= BASEURL; ?>/admin/kelas" class="sidebar-item" id="nav-kelas">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Manajemen Kelas</span>
                    </a>
                    <a href="<?= BASEURL; ?>/admin/laporan" class="sidebar-item" id="nav-laporan">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2m-6 4h6m-6-4H9m-6-4h.01M9 12h.01M15 12h.01M21 12c0 4.418-4.03 8-9 8s-9-3.582-9-8 4.03-8 9-8 9 3.582 9 8z"></path></svg>
                        <span>Laporan & Riwayat</span>
                    </a>
                </nav>
            </div>
            <div class="logout-button-wrapper">
                <a href="<?= BASEURL; ?>/logout" class="logout-button">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="20" viewBox="0 0 24 24" width="20"><path d="M0 0h24v24H0z" fill="none"/><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <div class="main-content">
            <header class="header">
                <div class="user-profile">
                    <a href="<?= BASEURL; ?>/admin/profile" class="profile-link" title="Lihat Profil">
                         <div class="profile-picture-container">
                            <?php if (isset($data['profile_picture']) && !empty($data['profile_picture'])): ?>
                                <img src="<?= BASEURL; ?>/img/profiles/<?= $data['profile_picture']; ?>" alt="Foto Profil" class="profile-picture">
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" class="profile-default-icon"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/></svg>
                            <?php endif; ?>
                         </div>
                    </a>
                    <span class="user-greeting">Selamat datang, <strong><?= $_SESSION['username'] ?? 'Admin'; ?></strong>!</span>
                </div>
            </header>
            <div class="content-area">