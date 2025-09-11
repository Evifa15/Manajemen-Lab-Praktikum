<div class="content">
    <div class="main-table-container">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Detail Staff</h2>
            <a href="<?= BASEURL; ?>/admin/pengguna/staff" class="btn btn-secondary" style="text-decoration: none; padding: 10px 15px; border-radius: 5px;">Kembali ke Daftar Staff</a>
        </div>

        <?php if ($data['staff']): ?>
        <div class="detail-card-wrapper" style="margin-top: 0;">
            <div class="detail-card" style="padding: 2rem; max-width: 900px; margin: auto; display:flex; gap: 2rem; align-items: flex-start;">
                
                <div class="profile-sidebar" style="flex-shrink: 0; width: 200px; text-align: center;">
                    <div style="width: 150px; height: 150px; border-radius: 8px; overflow: hidden; margin: auto; background-color: #f0f2f5; border: 3px solid #4CAF50;">
                        <img src="<?= BASEURL . '/img/siswa/' . htmlspecialchars($data['staff']['foto'] ?? 'default.png'); ?>" alt="Foto Staff" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h3 style="margin-top: 1rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($data['staff']['nama']); ?></h3>
                </div>

                <div class="info-section" style="flex-grow: 1;">
                    <h4>Informasi Pribadi</h4>
                    <div class="detail-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="detail-item">
                            <span class="detail-label">ID Staff</span>
                            <span class="detail-value"><?= htmlspecialchars($data['staff']['id_staff']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Jenis Kelamin</span>
                            <span class="detail-value"><?= htmlspecialchars($data['staff']['jenis_kelamin']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tempat, Tanggal Lahir</span>
                            <span class="detail-value"><?= htmlspecialchars($data['staff']['ttl'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Agama</span>
                            <span class="detail-value"><?= htmlspecialchars($data['staff']['agama'] ?? '-'); ?></span>
                        </div>
                    </div>
                    <hr style="margin: 1.5rem 0;">
                    <h4>Informasi Kontak</h4>
                     <div class="detail-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="detail-item">
                            <span class="detail-label">No. HP</span>
                            <span class="detail-value"><?= htmlspecialchars($data['staff']['no_hp'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?= htmlspecialchars($data['staff']['email'] ?? '-'); ?></span>
                        </div>
                         <div class="detail-item" style="grid-column: 1 / -1;">
                            <span class="detail-label">Alamat</span>
                            <span class="detail-value" style="white-space: pre-wrap;"><?= htmlspecialchars($data['staff']['alamat'] ?? '-'); ?></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php else: ?>
        <p style="text-align:center; margin-top: 2rem;">Data staff tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>