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
                 <button type="submit" form="bulkDeleteSiswaForm" class="btn btn-danger" id="bulkDeleteSiswaBtn" style="display: none;">Hapus Terpilih</button>
                
                <form id="searchSiswaForm" action="<?= BASEURL ?>/admin/detailKelas/<?= $data['kelas']['id'] ?>" method="get" style="margin-left:auto; flex-grow: 1;">
                    <input type="text" id="searchSiswaInput" name="search" placeholder="Cari nama atau ID siswa..." value="<?= htmlspecialchars($data['search_term'] ?? '') ?>" style="width: 100%; max-width: 300px; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                </form>

                <div class="actions-container">
                    <button type="button" class="btn btn-secondary" id="importSiswaBtn">Import Siswa</button>
                    <button type="button" class="add-button" id="addSiswaBtn">+ Tambah Siswa</button>
                </div>
            </div>
        
            <?php Flasher::flash(); ?>

           <form action="<?= BASEURL ?>/admin/remove-siswa-massal" method="POST" id="bulkDeleteSiswaForm">
                <input type="hidden" name="kelas_id" value="<?= $data['kelas']['id']; ?>">

                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllSiswa"></th>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>ID Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>No. HP</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($data['siswa'])):
                        $no = ($data['halaman_aktif'] - 1) * 10 + 1;
                        foreach ($data['siswa'] as $siswa): ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?= $siswa['id']; ?>" class="row-checkbox-siswa"></td>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($siswa['nama']); ?></td>
                            <td><?= htmlspecialchars($siswa['id_siswa']); ?></td>
                            <td><?= htmlspecialchars($siswa['jenis_kelamin']); ?></td>
                            <td><?= htmlspecialchars($siswa['no_hp'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach;
                    else: ?>
                        <tr><td colspan="6" style="text-align:center;">Tidak ada data siswa di kelas ini.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </form>
            
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

<div id="assignSiswaModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 class="modal-title">Tambahkan Siswa ke Kelas Ini</h3>
        <form id="assignSiswaForm" method="POST" action="<?= BASEURL; ?>/admin/assignSiswaToKelas">
            <input type="hidden" name="kelas_id" value="<?= htmlspecialchars($data['kelas']['id']); ?>">
            
            <div class="form-group">
                <label for="siswa_id_select">Pilih Siswa</label>
                <select id="siswa_id_select" name="siswa_id" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php if (!empty($data['unassigned_siswa'])): ?>
                        <?php foreach ($data['unassigned_siswa'] as $siswa): ?>
                            <option value="<?= $siswa['id']; ?>"><?= htmlspecialchars($siswa['nama']); ?> (NIS: <?= htmlspecialchars($siswa['id_siswa']); ?>)</option>
                        <?php endforeach; ?>
                    <?php else: ?>
                         <option value="" disabled>Tidak ada siswa yang belum memiliki kelas.</option>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit">Tambahkan</button>
        </form>
    </div>
</div>

<div id="siswaModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <span class="close-button">&times;</span>
        <h3 class="modal-title" id="siswaModalTitle">Tambah Siswa</h3>
        <form id="siswaForm" method="POST" action="<?= BASEURL; ?>/admin/tambah-siswa" enctype="multipart/form-data">
            <input type="hidden" id="siswaId" name="id">
            <input type="hidden" id="fotoLama" name="foto_lama">
            <div class="form-row">
                <div class="form-group"><label for="nama_siswa">Nama Siswa</label><input type="text" id="nama_siswa" name="nama" required></div>
                <div class="form-group"><label for="id_siswa">ID Siswa (NIS)</label><input type="text" id="id_siswa" name="id_siswa" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="jenis_kelamin_siswa">Jenis Kelamin</label><select id="jenis_kelamin_siswa" name="jenis_kelamin" required><option value="Laki laki">Laki laki</option><option value="Perempuan">Perempuan</option></select></div>
                <div class="form-group"><label for="ttl_siswa">Tempat, Tanggal Lahir</label><input type="text" id="ttl_siswa" name="ttl"></div>
            </div>
            <div class="form-row">
                 <div class="form-group"><label for="agama_siswa">Agama</label><input type="text" id="agama_siswa" name="agama"></div>
                <div class="form-group"><label for="no_hp_siswa">No. HP</label><input type="text" id="no_hp_siswa" name="no_hp"></div>
            </div>
            <div class="form-group"><label for="alamat_siswa">Alamat</label><textarea id="alamat_siswa" name="alamat" rows="2"></textarea></div>
            <div class="form-group"><label for="email_siswa">Email</label><input type="email" id="email_siswa" name="email"></div>
            <div class="form-group"><label for="foto_siswa">Foto (Opsional)</label><input type="file" id="foto_siswa" name="foto" accept="image/*"></div>
            <button type="submit">Simpan</button>
        </form>
    </div>
</div>

<div id="importSiswaModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 id="importSiswaModalTitle">Import Data Siswa ke Kelas Ini</h3>
        <form id="importSiswaForm" action="<?= BASEURL; ?>/admin/importSiswaKeKelas" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="kelas_id" value="<?= $data['kelas']['id'] ?? ''; ?>">
            <div class="import-instructions" style="background-color: #f0f2f5; border-left: 4px solid #4CAF50; padding: 15px; margin-bottom: 1.5rem; border-radius: 5px; font-size: 14px;">
                <strong>Petunjuk:</strong>
                <ul style="padding-left: 20px; margin-top: 10px;">
                    <li>Gunakan file dengan format <strong>.csv</strong>.</li>
                    <li>File hanya perlu berisi kolom <strong>NIS</strong> (ID Siswa) yang sudah terdaftar di sistem.</li>
                    <li>Baris pertama (header) akan dilewati.</li>
                    <li>Siswa yang berhasil diimpor akan otomatis ditugaskan ke kelas ini.</li>
                </ul>
                <div style="margin-top: 15px;">
                    Contoh format file CSV:<br>
                    `NIS`<br>
                    `2024001`<br>
                    `2024003`<br>
                    `2024005`<br>
                </div>
            </div>
            <div class="form-group">
                <label for="file_import_siswa">Pilih File untuk Diimpor</label>
                <input type="file" id="file_import_siswa" name="file_import_siswa" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 100%;">
            </div>
            <button type="submit">Unggah dan Proses</button>
        </form>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content delete-modal">
        <span class="close-button">&times;</span>
        <div class="delete-icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 0 24 24" width="48px" fill="#ef4444"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        </div>
        <p class="modal-message">Apakah Anda yakin ingin menghapus data ini?</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="cancelDelete">Batal</button>
            <a href="#" class="btn btn-danger" id="confirmDeleteLink">Ya, Hapus</a>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>