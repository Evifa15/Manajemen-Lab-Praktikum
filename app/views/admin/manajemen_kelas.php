<?php require_once '../app/views/layouts/admin_header.php'; ?>

<style>
    /* Style untuk tab */
    .tab-links-wrapper {
        display: flex;
        border-bottom: 2px solid #ddd;
    }
    .tab-link {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
        font-weight: 500;
        color: #888;
        border-bottom: 3px solid transparent;
        text-decoration: none;
    }
    .tab-link.active {
        color: #4CAF50;
        border-bottom: 3px solid #4CAF50;
    }
    .tab-content {
        display: none;
        padding-top: 20px;
    }
    .tab-content.active {
        display: block;
    }
    /* Style untuk tata letak kontrol di setiap tab */
    .table-controls-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 10px;
    }
    .actions-container {
        display: flex;
        gap: 10px;
    }
    .search-form-container {
        display: flex;
        gap: 10px;
        flex-grow: 1;
        justify-content: flex-end; /* Membuat form pencarian ke kanan */
    }
    .search-form-container input {
        width: 100%;
        max-width: 300px; /* Batasi lebar input agar tidak terlalu besar */
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    /* Style untuk modal import */
    .import-instructions {
        font-size: 14px;
        color: #555;
        background-color: #f0f2f5;
        border-left: 4px solid #4CAF50;
        padding: 15px;
        margin-bottom: 1.5rem;
        border-radius: 5px;
    }
    .import-instructions ul {
        padding-left: 20px;
        margin: 10px 0 0 0;
    }
    .import-instructions li {
        margin-bottom: 5px;
    }
</style>

<div class="content">
    <div class="main-table-container">

        <div class="tab-links-wrapper">
            <a href="<?= BASEURL; ?>/admin/kelas/guru" class="tab-link <?= ($data['active_tab'] == 'guru') ? 'active' : '' ?>">Daftar Guru</a>
            <a href="<?= BASEURL; ?>/admin/kelas/kelas" class="tab-link <?= ($data['active_tab'] == 'kelas') ? 'active' : '' ?>">Daftar Kelas</a>
        </div>

        <!-- ======================= TAB DAFTAR KELAS ======================= -->
        <div id="kelas" class="tab-content <?= ($data['active_tab'] == 'kelas') ? 'active' : '' ?>">
            <div class="table-controls-container">
                <button type="submit" form="bulkDeleteKelasForm" class="btn btn-danger" id="bulkDeleteKelasBtn" style="display: none;">Hapus Terpilih</button>
                
                <!-- PERUBAHAN: Tombol 'Cari' dihapus & ID ditambahkan -->
                <form id="searchKelasForm" action="<?= BASEURL; ?>/admin/kelas/kelas" method="GET" class="search-form-container">
                    <input type="text" id="searchKelasInput" name="search_kelas" placeholder="Cari nama kelas atau wali..." value="<?= htmlspecialchars($data['search_term_kelas'] ?? '') ?>">
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
                    <tbody>
                        <?php if (!empty($data['kelas'])): 
                            $no = 1;
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

        <!-- ======================= TAB DAFTAR GURU ======================= -->
        <div id="guru" class="tab-content <?= ($data['active_tab'] == 'guru') ? 'active' : '' ?>">
            <div class="table-controls-container">
                <button type="submit" form="bulkDeleteGuruForm" class="btn btn-danger" id="bulkDeleteGuruBtn" style="display: none;">Hapus Terpilih</button>
                
                 <!-- PERUBAHAN: Tombol 'Cari' dihapus & ID ditambahkan -->
                <form id="searchGuruForm" action="<?= BASEURL; ?>/admin/kelas/guru" method="GET" class="search-form-container">
                    <input type="text" id="searchGuruInput" name="search_guru" placeholder="Cari nama atau NIP guru..." value="<?= htmlspecialchars($data['search_term_guru'] ?? '') ?>">
                </form>

                <div class="actions-container">
                    <button type="button" class="btn btn-secondary" id="importGuruBtn">Import Guru</button>
                    <button type="button" class="add-button" id="addGuruBtn">+ Tambah Guru</button>
                </div>
            </div>
            
            <form action="<?= BASEURL; ?>/admin/hapus-guru-massal" method="POST" id="bulkDeleteGuruForm">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllGuru"></th>
                            <th>No</th>
                            <th>Nama Guru</th>
                            <th>ID Guru (NIP)</th>
                            <th>No HP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['guru'])):
                            $no = 1;
                            foreach ($data['guru'] as $guru): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?= $guru['id']; ?>" class="row-checkbox-guru"></td>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($guru['nama']); ?></td>
                                <td><?= htmlspecialchars($guru['nip']); ?></td>
                                <td><?= htmlspecialchars($guru['no_hp']); ?></td>
                                <td class="action-buttons">
                                    <a href="<?= BASEURL ?>/admin/detailGuru/<?= $guru['id'] ?>" class="view-btn" title="Lihat Detail"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg></a>
                                    <button type="button" class="edit-guru-btn" data-id="<?= $guru['id']; ?>" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg></button>
                                    <button type="button" class="delete-guru-btn" data-id="<?= $guru['id']; ?>" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg></button>
                                </td>
                            </tr>
                            <?php endforeach;
                        else: ?>
                            <tr><td colspan="6" style="text-align: center;">Tidak ada data guru.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>
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

<div id="guruModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <span class="close-button">&times;</span>
        <h3 id="guruModalTitle">Tambah Guru</h3>
        <form id="guruForm" method="POST">
            <input type="hidden" id="guruId" name="id">
            <div class="form-row">
                <div class="form-group">
                    <label for="nama">Nama Guru</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="nip">ID Guru (NIP)</label>
                    <input type="text" id="nip" name="nip" required>
                </div>
            </div>
            <div class="form-row">
                 <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="Laki laki">Laki laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ttl">Tempat, Tanggal Lahir</label>
                    <input type="text" id="ttl" name="ttl">
                </div>
            </div>
             <div class="form-row">
                <div class="form-group">
                    <label for="agama">Agama</label>
                    <input type="text" id="agama" name="agama">
                </div>
                 <div class="form-group">
                    <label for="no_hp">No. HP</label>
                    <input type="text" id="no_hp" name="no_hp">
                </div>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>
            <button type="submit">Simpan</button>
        </form>
    </div>
</div>

<div id="importModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 id="importModalTitle">Import Data Kelas dari File</h3>
        <form id="importForm" action="<?= BASEURL; ?>/admin/import-kelas" method="POST" enctype="multipart/form-data">
            <div class="import-instructions">
                <strong>Petunjuk:</strong>
                <ul>
                    <li>Gunakan file dengan format <strong>.csv</strong>.</li>
                    <li>Pastikan file memiliki 3 kolom dengan urutan: <strong>Nama Kelas</strong>, <strong>Nama Wali Kelas</strong>, <strong>ID Wali Kelas (NIP)</strong>.</li>
                    <li>Baris pertama (header) akan dilewati.</li>
                    <li>Pastikan NIP Wali Kelas sudah terdaftar di sistem pada tab "Daftar Guru".</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="file_import">Pilih File untuk Diimpor</label>
                <input type="file" id="file_import" name="file_import" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 100%;">
            </div>
            <button type="submit">Unggah dan Proses</button>
        </form>
    </div>
</div>

<div id="importGuruModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 id="importGuruModalTitle">Import Data Guru dari File</h3>
        <form id="importGuruForm" action="<?= BASEURL; ?>/admin/import-guru" method="POST" enctype="multipart/form-data">
            <div class="import-instructions">
                <strong>Petunjuk:</strong>
                <ul>
                    <li>Gunakan file dengan format <strong>.csv</strong>.</li>
                    <li>Pastikan file memiliki 6 kolom: <strong>Nama Guru, NIP, Jenis Kelamin, No. HP, Email, Alamat</strong>.</li>
                    <li>Baris pertama (header) akan dilewati.</li>
                    <li>Pastikan NIP tidak duplikat dengan data guru yang sudah ada.</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="file_import_guru">Pilih File untuk Diimpor</label>
                <input type="file" id="file_import_guru" name="file_import_guru" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 100%;">
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
            <a href="#" class="btn btn-danger" id="confirmDelete">Ya, Hapus</a>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>

