<div class="content">
    <h2 class="dashboard-title">Profil Admin</h2>
    <div class="profile-card">
        <div class="profile-avatar">
            <!-- Anda bisa menggunakan Font Awesome atau ikon lain di sini -->
            <p style="font-size: 5rem; margin: 0;">&#128100;</p>
        </div>
        <div class="profile-details">
            <!-- Menampilkan username dari session -->
            <h3><?= htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></h3>
            <p>Role: Admin</p>
        </div>
        <div class="profile-actions">
            <button class="edit-profile-button" disabled>Edit Profil</button>
            <button class="change-password-button" disabled>Ubah Password</button>
        </div>
    </div>
</div>
