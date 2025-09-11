<div class="table-controls-container">
    <button type="submit" form="bulkDeleteSiswaForm" class="btn btn-danger" id="bulkDeleteSiswaBtn" style="display: none;">Hapus Terpilih</button>
    
    <form id="searchSiswaForm" action="<?= BASEURL; ?>/admin/pengguna/siswa" method="GET" class="search-form-container">
        <input type="text" id="searchSiswaInput" name="search_siswa" placeholder="Cari nama atau ID siswa..." value="<?= htmlspecialchars($data['search_term_siswa'] ?? '') ?>">
    </form>

    <div class="actions-container">
        <button type="button" class="btn btn-secondary" id="importSiswaBtn">Import Siswa</button>
        <button type="button" class="add-button" id="addSiswaBtn">+ Tambah Siswa</button>
    </div>
</div>

<?php Flasher::flash(); ?>

<form action="<?= BASEURL; ?>/admin/hapus-siswa-massal" method="POST" id="bulkDeleteSiswaForm">
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAllSiswa"></th>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>ID Siswa (NIS)</th>
                <th>Jenis Kelamin</th>
                <th>No. HP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="siswaTableBody">
            <?php if (!empty($data['siswa'])):
                $no = ($data['halaman_aktif'] - 1) * $data['limit'] + 1;
                foreach ($data['siswa'] as $siswa): ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?= $siswa['id']; ?>" class="row-checkbox-siswa"></td>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($siswa['nama']); ?></td>
                    <td><?= htmlspecialchars($siswa['id_siswa']); ?></td>
                    <td><?= htmlspecialchars($siswa['jenis_kelamin']); ?></td>
                    <td><?= htmlspecialchars($siswa['no_hp'] ?? '-'); ?></td>
                    <td class="action-buttons">
                        <a href="<?= BASEURL ?>/admin/detailSiswa/<?= $siswa['id'] ?>" class="view-btn" title="Lihat Detail">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg>
                        </a>
                        <button type="button" class="edit-siswa-btn" data-id="<?= $siswa['id']; ?>" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>
                        </button>
                        <button type="button" class="delete-siswa-btn" data-id="<?= $siswa['id']; ?>" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg>
                        </button>
                    </td>
                </tr>
                <?php endforeach;
            else: ?>
                <tr><td colspan="7" style="text-align: center;">Tidak ada data siswa yang ditemukan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</form>

<?php if (isset($data['total_halaman']) && $data['total_halaman'] > 1): ?>
<div class="pagination-container">
    <?php
        $queryParams = http_build_query(['search_siswa' => $data['search_term_siswa'] ?? '']);
    ?>
    <a href="<?= BASEURL ?>/admin/pengguna/siswa/<?= max(1, $data['halaman_aktif'] - 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
    <div class="page-numbers">
        <?php for ($i = 1; $i <= $data['total_halaman']; $i++): ?>
            <a href="<?= BASEURL ?>/admin/pengguna/siswa/<?= $i ?>?<?= $queryParams ?>" class="page-link <?= ($i == $data['halaman_aktif']) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <a href="<?= BASEURL ?>/admin/pengguna/siswa/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] >= $data['total_halaman']) ? 'disabled' : '' ?>">Berikutnya</a>
</div>
<?php endif; ?>

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
                <div class="form-group"><label for="jenis_kelamin_siswa">Jenis Kelamin</label>
                    <select id="jenis_kelamin_siswa" name="jenis_kelamin" required>
                        <option value="Laki laki">Laki laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
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
        <h3 id="importSiswaModalTitle">Import Data Siswa</h3>
        <form id="importSiswaForm" action="<?= BASEURL; ?>/admin/import-siswa" method="POST" enctype="multipart/form-data">
            <div class="import-instructions" style="background-color: #f0f2f5; border-left: 4px solid #4CAF50; padding: 15px; margin-bottom: 1.5rem; border-radius: 5px; font-size: 14px;">
                <strong>Petunjuk:</strong>
                <ul style="padding-left: 20px; margin-top: 10px; line-height: 1.6;">
                    <li>Gunakan file dengan format <strong>.csv</strong>.</li>
                    <li>Pastikan urutan kolom: <strong>Nama, ID Siswa (NIS), Jenis Kelamin, No. HP, Email</strong>.</li>
                    <li>Baris pertama (header) akan diabaikan.</li>
                    <li>Akun login akan dibuat otomatis dengan <strong>Nama</strong> sebagai username dan <strong>ID Siswa</strong> sebagai password awal.</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="file_import_siswa">Pilih File CSV untuk Diimpor</label>
                <input type="file" id="file_import_siswa" name="file_import_siswa" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 100%;">
            </div>
            <button type="submit">Unggah dan Proses</button>
        </form>
    </div>
</div>

<div id="importSiswaModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 id="importSiswaModalTitle">Import Data Siswa</h3>
        <form id="importSiswaForm" action="<?= BASEURL; ?>/admin/import-siswa" method="POST" enctype="multipart/form-data">
            <div class="import-instructions" style="background-color: #f0f2f5; border-left: 4px solid #4CAF50; padding: 15px; margin-bottom: 1.5rem; border-radius: 5px; font-size: 14px;">
                <strong>Petunjuk:</strong>
                <ul style="padding-left: 20px; margin-top: 10px; line-height: 1.6;">
                    <li>Gunakan file dengan format <strong>.csv</strong>.</li>
                    <li>Pastikan urutan kolom: <strong>Nama, ID Siswa (NIS), Jenis Kelamin, No. HP, Email</strong>.</li>
                    <li>Baris pertama (header) akan diabaikan.</li>
                    <li>Akun login akan dibuat otomatis dengan <strong>Nama</strong> sebagai username dan <strong>ID Siswa</strong> sebagai password awal.</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="file_import_siswa">Pilih File CSV untuk Diimpor</label>
                <input type="file" id="file_import_siswa" name="file_import_siswa" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 100%;">
            </div>
            <button type="submit">Unggah dan Proses</button>
        </form>
    </div>
</div>