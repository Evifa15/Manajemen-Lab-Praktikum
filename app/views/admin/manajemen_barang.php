<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="main-table-container">
        <div class="table-controls-container">
            <div class="search-and-filter">
                <input type="text" id="searchInput" placeholder="Cari barang...">
            </div>
            <button class="add-button" id="addItemBtn">+ Tambah Barang</button>
        </div>
        
        <table id="itemTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Stok</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($data['items']) && is_array($data['items']) && !empty($data['items'])): ?>
                    <?php 
                    // ✅ PERBAIKAN: Logika penomoran agar berlanjut
                    $limit = 10; // Sesuaikan dengan limit di controller
                    $no = ($data['halaman_aktif'] - 1) * $limit + 1; 
                    ?>
                    <?php foreach ($data['items'] as $item): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($item['kode_barang']); ?></td>
                        <td><?= htmlspecialchars($item['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($item['jumlah']); ?></td>
                        <td><?= ucfirst(htmlspecialchars($item['kondisi'])); ?></td>
                        <td class="action-buttons">
                            <button class="view-btn" data-id="<?= $item['id']; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg>
                            </button>
                            <button class="edit-btn" data-id="<?= $item['id']; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>
                            </button>
                            <button class="delete-btn" data-id="<?= $item['id']; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data barang.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="pagination-container">
            <a href="<?= BASEURL ?>/admin/barang/<?= max(1, $data['halaman_aktif'] - 1) ?>" 
               class="pagination-btn <?= ($data['halaman_aktif'] <= 1) ? 'disabled' : '' ?>">
               Sebelumnya
            </a>

            <div class="page-numbers">
                <?php for ($i = 1; $i <= $data['total_halaman']; $i++): ?>
                    <a href="<?= BASEURL ?>/admin/barang/<?= $i ?>" 
                       class="page-link <?= ($i == $data['halaman_aktif']) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>

            <a href="<?= BASEURL ?>/admin/barang/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1) ?>" 
               class="pagination-btn <?= ($data['halaman_aktif'] >= $data['total_halaman']) ? 'disabled' : '' ?>">
               Berikutnya
            </a>
        </div>
    </div>
    
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3 class="modal-title">Tambah Barang</h3>
            <form id="itemForm" action="<?= BASEURL ?>/admin/tambahBarang" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="itemId" name="id">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" id="kode_barang" name="kode_barang" placeholder="Kode Barang" required>
                    </div>
                    <div class="form-group">
                        <input type="text" id="nama_barang" name="nama_barang" placeholder="Nama Barang" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <input type="number" id="jumlah" name="jumlah" placeholder="Jumlah Stok" required>
                    </div>
                    <div class="form-group">
                        <select id="kondisi" name="kondisi" required>
                            <option value="" disabled selected>Kondisi</option>
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                            <option value="perbaikan">Perbaikan</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="gambar" class="file-label">Pilih Gambar</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*">
                    </div>
                    <div class="form-group">
                        <input type="date" id="tanggal_pembelian" name="tanggal_pembelian" placeholder="Tanggal Pembelian">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full-width">
                        <input type="text" id="lokasi_penyimpanan" name="lokasi_penyimpanan" placeholder="Lokasi Penyimpanan">
                    </div>
                </div>
                <button type="submit">Simpan</button>
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
                <button class="btn btn-secondary" id="cancelDeleteBtn">Batal</button>
                <a href="#" class="btn btn-danger" id="confirmDeleteLink">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>