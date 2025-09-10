<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="main-table-container">
        <?php if ($data['kelas']): ?>
        <hr style="margin: 2rem 0;">

        <div class="manajemen-siswa-container">
            <h4>Daftar Siswa di Kelas Ini</h4>
            
            <form action="<?= BASEURL ?>/admin/hapus-siswa-massal" method="POST" id="bulkDeleteSiswaForm">
                <input type="hidden" name="kelas_id" value="<?= $data['kelas']['id']; ?>">

                <div class="table-controls-container" style="margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-danger" id="bulkDeleteSiswaBtn" style="display: none;">Hapus Terpilih</button>
                    
                    <div class="search-form" style="margin-left:auto;">
                        <input type="text" name="search" placeholder="Cari nama atau ID siswa..." value="<?= htmlspecialchars($data['search_term'] ?? '') ?>" onchange="this.form.submit()">
                    </div>

                    <div class="actions-container">
                        <button type="button" class="btn btn-secondary" id="importSiswaBtn">Import Siswa</button>
                        <button type="button" class="add-button" id="addSiswaBtn">+ Tambah Siswa</button>
                    </div>
                </div>
            
                <?php Flasher::flash(); ?>

                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllSiswa"></th>
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
                            <td><input type="checkbox" name="ids[]" value="<?= $siswa['id']; ?>" class="row-checkbox-siswa"></td>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($siswa['nama']); ?></td>
                            <td><?= htmlspecialchars($siswa['id_siswa']); ?></td>
                            <td><?= htmlspecialchars($siswa['jenis_kelamin']); ?></td>
                            <td><?= htmlspecialchars($siswa['status']); ?></td>
                            <td class="action-buttons">
                                </td>
                        </tr>
                        <?php endforeach;
                    else: ?>
                        <tr><td colspan="7" style="text-align:center;">Tidak ada data siswa di kelas ini.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </form>
            </div>

        <?php else: ?>
            <p style="text-align:center; margin-top: 2rem;">Data kelas tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<div id="importSiswaModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 id="importSiswaModalTitle">Import Data Siswa ke Kelas Ini</h3>
        <form id="importSiswaForm" action="<?= BASEURL; ?>/admin/import-siswa" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="kelas_id" value="<?= $data['kelas']['id'] ?? ''; ?>">
            <div class="import-instructions" style="background-color: #f0f2f5; border-left: 4px solid #4CAF50; padding: 15px; margin-bottom: 1.5rem; border-radius: 5px; font-size: 14px;">
                <strong>Petunjuk:</strong>
                <ul style="padding-left: 20px; margin-top: 10px;">
                    <li>Gunakan file dengan format <strong>.csv</strong>.</li>
                    <li>Pastikan file memiliki 3 kolom dengan urutan: <strong>Nama Siswa</strong>, <strong>ID Siswa (NIS/NISN)</strong>, <strong>Jenis Kelamin</strong>.</li>
                    <li>Baris pertama (header) akan dilewati.</li>
                    <li>Akun login untuk setiap siswa akan dibuat secara otomatis dengan <strong>Nama Siswa</strong> sebagai username dan <strong>ID Siswa</strong> sebagai password awal.</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="file_import_siswa">Pilih File untuk Diimpor</label>
                <input type="file" id="file_import_siswa" name="file_import_siswa" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 100%;">
            </div>
            <button type="submit">Unggah dan Proses</button>
        </form>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>