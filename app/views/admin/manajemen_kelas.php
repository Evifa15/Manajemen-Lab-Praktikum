<?php require_once '../app/views/layouts/admin_header.php'; ?>

<style>
    /* Style untuk tab */
    .tab-container { display: flex; border-bottom: 2px solid #ddd; margin-bottom: 20px; }
    .tab-link { padding: 10px 20px; cursor: pointer; border: none; background: none; font-size: 16px; font-weight: 500; color: #888; border-bottom: 3px solid transparent; }
    .tab-link.active { color: #4CAF50; border-bottom: 3px solid #4CAF50; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .search-form { display: flex; gap: 10px; flex-grow: 1; }
    .search-form input { width: 100%; }
</style>

<div class="content">
    <div class="main-table-container">

        <div class="tab-container">
            <button class="tab-link <?= ($data['active_tab'] == 'kelas') ? 'active' : '' ?>" data-tab="kelas">Daftar Kelas</button>
            <button class="tab-link <?= ($data['active_tab'] == 'guru') ? 'active' : '' ?>" data-tab="guru">Daftar Guru</button>
        </div>

        <!-- Konten Tab Daftar Kelas -->
        <div id="kelas" class="tab-content <?= ($data['active_tab'] == 'kelas') ? 'active' : '' ?>">
            <div class="table-controls-container">
                <form action="<?= BASEURL ?>/admin/kelas/kelas" method="get" class="search-form">
                    <input type="text" name="search" placeholder="Cari nama kelas atau wali..." value="<?= htmlspecialchars($data['search_term'] ?? '') ?>">
                    <button type="submit" class="add-button">Cari</button>
                </form>
                <button class="add-button" id="addKelasBtn">+ Tambah Kelas</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Wali Kelas</th>
                        <th>ID Wali Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['kelas'])): 
                        $no = ($data['halaman_aktif_kelas'] - 1) * 10 + 1;
                        foreach ($data['kelas'] as $kelas): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($kelas['nama_kelas']); ?></td>
                            <td><?= htmlspecialchars($kelas['nama_wali_kelas'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($kelas['wali_kelas_id'] ?? '-'); ?></td>
                            <td class="action-buttons">
                                <a href="<?= BASEURL ?>/admin/detailKelas/<?= $kelas['id'] ?>" class="view-btn" title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg>
                                </a>
                                <button class="edit-kelas-btn" data-id="<?= $kelas['id']; ?>" title="Edit">
                                     <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>
                                </button>
                                <button class="delete-kelas-btn" data-id="<?= $kelas['id']; ?>" title="Hapus">
                                     <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; 
                    else: ?>
                        <tr><td colspan="5" style="text-align: center;">Tidak ada data kelas yang cocok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php 
                $searchQuery = isset($data['search_term']) ? '?search=' . urlencode($data['search_term']) : '';
            ?>
            <div class="pagination-container">
                 <a href="<?= BASEURL ?>/admin/kelas/kelas/<?= max(1, $data['halaman_aktif_kelas'] - 1) . $searchQuery ?>" class="pagination-btn <?= ($data['halaman_aktif_kelas'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
                <div class="page-numbers">
                    <?php for ($i = 1; $i <= $data['total_halaman_kelas']; $i++): ?>
                        <a href="<?= BASEURL ?>/admin/kelas/kelas/<?= $i . $searchQuery ?>" class="page-link <?= ($i == $data['halaman_aktif_kelas']) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <a href="<?= BASEURL ?>/admin/kelas/kelas/<?= min($data['total_halaman_kelas'], $data['halaman_aktif_kelas'] + 1) . $searchQuery ?>" class="pagination-btn <?= ($data['halaman_aktif_kelas'] >= $data['total_halaman_kelas']) ? 'disabled' : '' ?>">Berikutnya</a>
            </div>
        </div>

        <!-- Konten Tab Daftar Guru -->
        <div id="guru" class="tab-content <?= ($data['active_tab'] == 'guru') ? 'active' : '' ?>">
             <div class="table-controls-container">
                 <form action="<?= BASEURL ?>/admin/kelas/guru" method="get" class="search-form">
                    <input type="text" name="search" placeholder="Cari nama atau NIP guru..." value="<?= htmlspecialchars($data['search_term'] ?? '') ?>">
                    <button type="submit" class="add-button">Cari</button>
                </form>
                <button class="add-button" id="addGuruBtn">+ Tambah Guru</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Guru</th>
                        <th>ID Guru (NIP)</th>
                        <th>Jenis Kelamin</th>
                        <th>No HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                     <?php if (!empty($data['guru'])):
                        $no = ($data['halaman_aktif_guru'] - 1) * 10 + 1;
                        foreach ($data['guru'] as $guru): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($guru['nama']); ?></td>
                            <td><?= htmlspecialchars($guru['nip']); ?></td>
                            <td><?= htmlspecialchars($guru['jenis_kelamin']); ?></td>
                            <td><?= htmlspecialchars($guru['no_hp']); ?></td>
                            <td class="action-buttons">
                                <!-- ✅ PERBAIKAN: Mengganti teks dengan ikon SVG -->
                                <a href="<?= BASEURL ?>/admin/detailGuru/<?= $guru['id'] ?>" class="view-btn" title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg>
                                </a>
                                <button class="edit-guru-btn" data-id="<?= $guru['id']; ?>" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>
                                </button>
                                <button class="delete-guru-btn" data-id="<?= $guru['id']; ?>" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach;
                    else: ?>
                        <tr><td colspan="6" style="text-align: center;">Tidak ada data guru yang cocok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
             <?php 
                $searchQuery = isset($data['search_term']) ? '?search=' . urlencode($data['search_term']) : '';
            ?>
            <div class="pagination-container">
                <a href="<?= BASEURL ?>/admin/kelas/guru/<?= max(1, $data['halaman_aktif_guru'] - 1) . $searchQuery ?>" class="pagination-btn <?= ($data['halaman_aktif_guru'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
                <div class="page-numbers">
                    <?php for ($i = 1; $i <= $data['total_halaman_guru']; $i++): ?>
                        <a href="<?= BASEURL ?>/admin/kelas/guru/<?= $i . $searchQuery ?>" class="page-link <?= ($i == $data['halaman_aktif_guru']) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <a href="<?= BASEURL ?>/admin/kelas/guru/<?= min($data['total_halaman_guru'], $data['halaman_aktif_guru'] + 1) . $searchQuery ?>" class="pagination-btn <?= ($data['halaman_aktif_guru'] >= $data['total_halaman_guru']) ? 'disabled' : '' ?>">Berikutnya</a>
            </div>
        </div>

    </div>
</div>

<!-- Semua Modal (Kelas, Guru, Hapus) tidak berubah -->
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
                        <option value="Laki-laki">Laki-laki</option>
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