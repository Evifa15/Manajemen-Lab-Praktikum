<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="main-table-container">
            <div class="table-controls-container" style="margin-top: 1.5rem;">
            <button type="submit" form="bulkDeleteKelasForm" class="btn btn-danger" id="bulkDeleteKelasBtn" style="display: none;">Hapus Terpilih</button>
            
            <form id="searchKelasForm" action="<?= BASEURL; ?>/admin/kelas" method="GET" class="search-form-container" style="justify-content: flex-end;">
                <input type="text" id="searchKelasInput" name="search_kelas" placeholder="Cari nama kelas atau wali..." value="<?= htmlspecialchars($data['search_term_kelas'] ?? '') ?>" style="width: 300px;">
            </form>

            <div class="actions-container">
                <button type="button" class="btn btn-secondary" id="importKelasBtn">Import Kelas</button>
                <button type="button" class="add-button" id="addKelasBtn">+ Tambah Kelas</button>
            </div>
        </div>
    
        <?php Flasher::flash(); ?>

        <form action="<?= BASEURL; ?>/admin/hapus-kelas-massal" method="POST" id="bulkDeleteKelasForm">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllKelas"></th>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Wali Kelas</th>
                        <th>ID Wali Kelas (NIP)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="kelasTableBody">
                    <?php if (!empty($data['kelas'])): 
                        $no = ($data['halaman_aktif'] - 1) * 10 + 1;
                        foreach ($data['kelas'] as $kelas): ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?= $kelas['id']; ?>" class="row-checkbox-kelas"></td>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($kelas['nama_kelas']); ?></td>
                            <td><?= htmlspecialchars($kelas['nama_wali_kelas'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($kelas['nip'] ?? '-'); ?></td>
                            <td class="action-buttons">
                                <a href="<?= BASEURL ?>/admin/detailKelas/<?= $kelas['id'] ?>" class="view-btn" title="Lihat Detail"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg></a>
                                <button type="button" class="edit-kelas-btn" data-id="<?= $kelas['id']; ?>" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg></button>
                                <button type="button" class="delete-kelas-btn" data-id="<?= $kelas['id']; ?>" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg></button>
                            </td>
                        </tr>
                        <?php endforeach; 
                    else: ?>
                        <tr><td colspan="6" style="text-align: center;">Tidak ada data kelas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>

<div id="kelasModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 id="kelasModalTitle">Tambah Kelas</h3>
        <form id="kelasForm" method="POST">
            <input type="hidden" id="kelasId" name="id">
            <div class="form-group">
                <label for="nama_kelas">Nama Kelas</label>
                <input type="text" id="nama_kelas" name="nama_kelas" required>
            </div>
            <div class="form-group">
                <label for="wali_kelas_id">Wali Kelas</label>
                <select id="wali_kelas_id" name="wali_kelas_id" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php foreach ($data['all_guru'] as $guru): ?>
                        <option value="<?= $guru['id'] ?>"><?= htmlspecialchars($guru['nama']) ?> (NIP: <?= $guru['nip'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Simpan</button>
        </form>
    </div>
</div>

<div id="importKelasModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 id="importKelasModalTitle">Import Data Kelas</h3>
        <form id="importKelasForm" action="<?= BASEURL; ?>/admin/import-kelas" method="POST" enctype="multipart/form-data">
            <div class="import-instructions" style="background-color: #f0f2f5; border-left: 4px solid #4CAF50; padding: 15px; margin-bottom: 1.5rem; border-radius: 5px; font-size: 14px;">
                <strong>Petunjuk:</strong>
                <ul style="padding-left: 20px; margin-top: 10px; line-height: 1.6;">
                    <li>Gunakan file dengan format <strong>.csv</strong>.</li>
                    <li>Pastikan urutan kolom: <strong>Nama Kelas</strong>, <strong>NIP Wali Kelas</strong>.</li>
                    <li>Baris pertama (header) akan diabaikan.</li>
                    <li>Pastikan **NIP Wali Kelas** sudah terdaftar di tab Daftar Guru.</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="file_import_kelas">Pilih File CSV untuk Diimpor</label>
                <input type="file" id="file_import_kelas" name="file_import_kelas" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 100%;">
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