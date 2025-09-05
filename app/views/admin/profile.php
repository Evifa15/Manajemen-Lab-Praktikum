<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <h2 class="dashboard-title">Profil Admin</h2>
    <div class="profile-card">
        <div class="profile-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="profile-details">
            <h3>Nama Admin</h3>
            <p>Email: admin@example.com</p>
            <p>Role: Admin</p>
        </div>
        <div class="profile-actions">
            <button class="edit-profile-button">Edit Profil</button>
            <button class="change-password-button">Ubah Password</button>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>