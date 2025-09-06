<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="main-table-container">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Detail Siswa</h2>
            <a href="<?= BASEURL; ?>/admin/detailKelas/<?= $data['siswa']['kelas_id'] ?? '' ?>" class="btn btn-secondary" style="text-decoration: none; padding: 10px 15px; border-radius: 5px;">Kembali ke Daftar Siswa</a>
        </div>

        <?php if ($data['siswa']): ?>
        <div class="detail-card-wrapper" style="margin-top: 0;">
            <div class="detail-card" style="padding: 2rem; max-width: 900px; margin: auto; display:flex; gap: 2rem; align-items: flex-start;">
                
                <div class="profile-sidebar" style="flex-shrink: 0; width: 200px; text-align: center;">
                    <div style="width: 150px; height: 150px; border-radius: 8px; overflow: hidden; margin: auto; background-color: #f0f2f5; border: 3px solid #4CAF50;">
                        <?php
                            $foto_siswa = $data['siswa']['foto'] ?? 'default.png';
                            $path_ke_foto = 'img/siswa/' . $foto_siswa;
                            if (!empty($data['siswa']['foto']) && file_exists($path_ke_foto)) {
                                $url_foto = BASEURL . '/' . $path_ke_foto;
                            } else {
                                $url_foto = BASEURL . '/img/siswa/default.png';
                            }
                        ?>
                        <img src="<?= $url_foto; ?>" alt="Foto Siswa" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    
                    <h3 style="margin-top: 1rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($data['siswa']['nama']); ?></h3>
                </div>

                <div class="info-section" style="flex-grow: 1;">
                    <h4>Informasi Pribadi</h4>
                    <div class="detail-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="detail-item">
                            <span class="detail-label">ID Siswa (NIS/NISN)</span>
                            <span class="detail-value"><?= htmlspecialchars($data['siswa']['id_siswa']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Jenis Kelamin</span>
                            <span class="detail-value"><?= htmlspecialchars($data['siswa']['jenis_kelamin']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tempat, Tanggal Lahir</span>
                            <span class="detail-value"><?= htmlspecialchars($data['siswa']['ttl'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Agama</span>
                            <span class="detail-value"><?= htmlspecialchars($data['siswa']['agama'] ?? '-'); ?></span>
                        </div>
                    </div>
                    
                    <hr style="margin: 1.5rem 0;">
                    
                    <h4>Informasi Kontak</h4>
                     <div class="detail-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="detail-item">
                            <span class="detail-label">No. HP</span>
                            <span class="detail-value"><?= htmlspecialchars($data['siswa']['no_hp'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?= htmlspecialchars($data['siswa']['email'] ?? '-'); ?></span>
                        </div>
                         <div class="detail-item" style="grid-column: 1 / -1;">
                            <span class="detail-label">Alamat</span>
                            <span class="detail-value" style="white-space: pre-wrap;"><?= htmlspecialchars($data['siswa']['alamat'] ?? '-'); ?></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php else: ?>
        <p style="text-align:center; margin-top: 2rem;">Data siswa tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>