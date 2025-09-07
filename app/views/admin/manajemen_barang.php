<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="main-table-container">
        <div class="table-controls-container">
            <form action="<?= BASEURL ?>/admin/barang" method="GET" class="search-and-filter-form">
                
                <div class="search-container">
                    <input type="text" name="search" id="searchInput" placeholder="Cari nama atau kode barang..." value="<?= htmlspecialchars($data['filters']['keyword'] ?? '') ?>">
                    <button type="submit" class="search-submit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#4CAF50"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                    </button>
                </div>

                <div class="filter-container">
                    <select name="filter_kondisi" onchange="this.form.submit()">
                        <option value="">Semua Kondisi</option>
                        <option value="baik" <?= ($data['filters']['kondisi'] ?? '') == 'baik' ? 'selected' : ''; ?>>Baik</option>
                        <option value="rusak ringan" <?= ($data['filters']['kondisi'] ?? '') == 'rusak ringan' ? 'selected' : ''; ?>>Rusak Ringan</option>
                        <option value="rusak berat" <?= ($data['filters']['kondisi'] ?? '') == 'rusak berat' ? 'selected' : ''; ?>>Rusak Berat</option>
                    </select>
                </div>
            </form>

            <div class="actions-container">
                <button class="add-button" id="addItemBtn">+ Tambah Barang</button>
            </div>
        </div>
        
        <div class="table-wrapper">
            <table id="itemTable">
                <thead>
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Stok</th>
                        <th>Kondisi</th>
                        <th>Status</th> <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($data['items']) && !empty($data['items'])):
                        foreach ($data['items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['kode_barang']); ?></td>
                            <td><?= htmlspecialchars($item['nama_barang']); ?></td>
                            <td><?= htmlspecialchars($item['jumlah']); ?></td>
                            <td><?= ucfirst(htmlspecialchars($item['kondisi'])); ?></td>
                            <td><?= htmlspecialchars($item['status']); ?></td>
                            <td class="action-buttons">
                                <button class="view-btn" data-id="<?= $item['id']; ?>" title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg>
                                </button>
                                <button class="edit-btn" data-id="<?= $item['id']; ?>" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>
                                </button>
                                <button class="delete-btn" data-id="<?= $item['id']; ?>" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach;
                    else: ?>
                        <tr><td colspan="6" style="text-align:center;">Tidak ada data barang yang cocok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isset($data['total_halaman']) && $data['total_halaman'] > 1): ?>
        <div class="pagination-container">
            <?php
                $queryParams = http_build_query([
                    'search' => $data['filters']['keyword'] ?? '',
                    'filter_kondisi' => $data['filters']['kondisi'] ?? ''
                ]);
            ?>
            <a href="<?= BASEURL ?>/admin/barang/<?= max(1, $data['halaman_aktif'] - 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
            <div class="page-numbers">
                <?php for ($i = 1; $i <= $data['total_halaman']; $i++): ?>
                    <a href="<?= BASEURL ?>/admin/barang/<?= $i ?>?<?= $queryParams ?>" class="page-link <?= ($i == $data['halaman_aktif']) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <a href="<?= BASEURL ?>/admin/barang/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] >= $data['total_halaman']) ? 'disabled' : '' ?>">Berikutnya</a>
        </div>
        <?php endif; ?>
    </div>
    
    <div id="itemModal" class="modal">
        ...
    </div>

    <div id="deleteModal" class="modal">
        ...
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>