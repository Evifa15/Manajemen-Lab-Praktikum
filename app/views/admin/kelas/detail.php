<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="main-table-container">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Detail Kelas</h2>
            <a href="<?= BASEURL; ?>/admin/kelas" class="btn btn-secondary" style="text-decoration: none; padding: 10px 15px; border-radius: 5px;">Kembali ke Daftar Kelas</a>
        </div>

        <?php if ($data['kelas']): ?>
        <div class="detail-card-wrapper" style="margin-top: 0;">
            <div class="detail-card" style="padding: 1.5rem; max-width: none; margin: 0;">
                <div class="info-section">
                    <h3><?= htmlspecialchars($data['kelas']['nama_kelas']); ?></h3>
                    <div class="detail-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                        <div class="detail-item">
                            <span class="detail-label">ID Kelas</span>
                            <span class="detail-value"><?= htmlspecialchars($data['kelas']['id']); ?></span>
                        </div>
                         <div class="detail-item">
                            <span class="detail-label">Wali Kelas</span>
                            <span class="detail-value"><?= htmlspecialchars($data['kelas']['nama_wali_kelas'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">NIP Wali Kelas</span>
                            <span class="detail-value"><?= htmlspecialchars($data['kelas']['nip'] ?? '-'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr style="margin: 2rem 0;">

        <div class="manajemen-siswa-container">
            <h4>Daftar Siswa di Kelas Ini</h4>
            <div class="table-controls-container" style="margin-top: 1.5rem;">
                <form action="<?= BASEURL ?>/admin/detailKelas/<?= $data['kelas']['id'] ?>" method="get" class="search-form">
                    <input type="text" name="search" placeholder="Cari nama atau ID siswa..." value="<?= htmlspecialchars($data['search_term'] ?? '') ?>">
                    <button type="submit" class="add-button">Cari</button>
                </form>
                <button class="add-button" id="addSiswaBtn">+ Tambah Siswa</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>ID Siswa</th>
                        <th>Jenis Kelamin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($data['siswa'])):
                    $no = ($data['halaman_aktif'] - 1) * 10 + 1;
                    foreach ($data['siswa'] as $siswa): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($siswa['nama']); ?></td>
                        <td><?= htmlspecialchars($siswa['id_siswa']); ?></td>
                        <td><?= htmlspecialchars($siswa['jenis_kelamin']); ?></td>
                        <td><?= htmlspecialchars($siswa['status']); ?></td>
                        <td class="action-buttons">
                            <!-- ✅ PERBAIKAN: Menambahkan ikon ke semua tombol aksi -->
                            <a href="<?= BASEURL ?>/admin/detailSiswa/<?= $siswa['id'] ?>" class="view-btn" title="Lihat Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg>
                            </a>
                            <button class="edit-siswa-btn" data-id="<?= $siswa['id']; ?>" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>
                            </button>
                            <button class="delete-siswa-btn" data-id="<?= $siswa['id']; ?>" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach;
                else: ?>
                    <tr><td colspan="6" style="text-align:center;">Tidak ada data siswa di kelas ini.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <?php 
                $searchQuery = isset($data['search_term']) ? '?search=' . urlencode($data['search_term']) : '';
            ?>
            <div class="pagination-container">
                <a href="<?= BASEURL ?>/admin/detailKelas/<?= $data['kelas']['id'] ?>/<?= max(1, $data['halaman_aktif'] - 1) . $searchQuery ?>" class="pagination-btn <?= ($data['halaman_aktif'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
                <div class="page-numbers">
                <?php for ($i = 1; $i <= $data['total_halaman']; $i++): ?>
                    <a href="<?= BASEURL ?>/admin/detailKelas/<?= $data['kelas']['id'] ?>/<?= $i . $searchQuery ?>" class="page-link <?= ($i == $data['halaman_aktif']) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                </div>
                <a href="<?= BASEURL ?>/admin/detailKelas/<?= $data['kelas']['id'] ?>/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1) . $searchQuery ?>" class="pagination-btn <?= ($data['halaman_aktif'] >= $data['total_halaman']) ? 'disabled' : '' ?>">Berikutnya</a>
            </div>
        </div>

        <?php else: ?>
            <p style="text-align:center; margin-top: 2rem;">Data kelas tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal untuk Siswa (Konten di-generate oleh JS) -->
<div id="siswaModal" class="modal"></div>

<!-- Modal Hapus Universal (Struktur tetap sama) -->
<div id="deleteModal" class="modal">
    <div class="modal-content delete-modal" style="max-width: 400px; text-align: center;">
        <span class="close-button">&times;</span>
        <p style="font-size: 18px; margin-top: 20px;">Apakah Anda yakin ingin menghapus data ini?</p>
        <div class="modal-actions" style="margin-top: 20px;">
            <button id="cancelDelete" class="btn btn-secondary">Batal</button>
            <a href="#" id="confirmDelete" class="btn btn-danger">Ya, Hapus</a>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>
