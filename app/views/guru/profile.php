<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <img src="https://img.icons8.com/ios-filled/100/4caf50/teacher.png" alt="profile-picture" class="profile-picture"/>
            <h2><?= htmlspecialchars($username); ?></h2>
            <span class="profile-role"><?= ucfirst(htmlspecialchars($role)); ?></span>
        </div>
        <div class="profile-body">
            <div class="profile-info-item">
                <span class="info-label">Email</span>
                <span class="info-value"><?= htmlspecialchars($email); ?></span>
            </div>
             <div class="profile-info-item">
                <span class="info-label">Status</span>
                <span class="info-value active-status">Aktif</span>
            </div>
        </div>
        <div class="profile-footer">
            <a href="<?= BASEURL; ?>/guru/dashboard" class="back-to-dashboard-btn">Kembali ke Dashboard</a>
        </div>
    </div>
</div>