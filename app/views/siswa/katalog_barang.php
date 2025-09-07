<div class="content main-table-container">
    
    <!-- Area Search & Filter -->
    <div class="katalog-header">
        <div class="search-container">
            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            <input type="text" placeholder="Cari nama barang...">
        </div>
        <div class="filter-container">
            <select name="kategori">
                <option value="">Semua Kategori</option>
            </select>
            <select name="ketersediaan">
                <option value="">Semua Status</option>
                <option value="tersedia">Tersedia</option>
                <option value="habis">Stok Habis</option>
            </select>
        </div>
    </div>
    
    <!-- Tombol Keranjang -->
    <div class="cart-button" title="Lihat Keranjang">
        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24" width="24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
        <div class="cart-count">0</div>
    </div>

    <!-- Daftar Card Barang Dinamis -->
    <div class="katalog-grid">
        <?php if (isset($data['items']) && !empty($data['items'])): ?>
            <?php foreach ($data['items'] as $item): ?>
                <div class="katalog-card">
                    <div class="katalog-img-container">
                        <?php 
                            $gambar_url = (!empty($item['gambar']) && file_exists('img/barang/' . $item['gambar']))
                                ? BASEURL . '/img/barang/' . htmlspecialchars($item['gambar'])
                                : BASEURL . '/img/siswa/default.png';
                        ?>
                        <img src="<?= $gambar_url; ?>" alt="<?= htmlspecialchars($item['nama_barang']); ?>">
                    </div>
                    <div class="katalog-card-body">
                        <h4 class="katalog-title"><?= htmlspecialchars($item['nama_barang']); ?></h4>
                        
                        <?php
                            $stok = (int)$item['jumlah'];
                            if ($stok > 5) {
                                $status_text = 'Tersedia';
                                $status_class = 'status-tersedia';
                            } elseif ($stok > 0) {
                                $status_text = 'Stok Terbatas';
                                $status_class = 'status-terbatas';
                            } else {
                                $status_text = 'Stok Habis';
                                $status_class = 'status-habis';
                            }
                        ?>
                        <div class="katalog-stock-status <?= $status_class; ?>"><?= $status_text; ?></div>
                        
                        <!-- ✅ PERUBAHAN: Info Stok & Kode dibungkus dalam div baru -->
                        <div class="katalog-info-wrapper">
                            <p class="katalog-info">Stok: <strong><?= $stok; ?></strong></p>
                            <p class="katalog-info">Kode: <strong><?= htmlspecialchars($item['kode_barang']); ?></strong></p>
                        </div>
                        
                        <button class="btn-keranjang" <?= ($stok <= 0) ? 'disabled' : ''; ?>>
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1 / -1;">Tidak ada barang yang tersedia saat ini.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if(isset($data['total_halaman']) && $data['total_halaman'] > 1): ?>
    <div class="pagination-container" style="margin-top: 2rem;">
        <a href="<?= BASEURL ?>/siswa/katalog/<?= max(1, $data['halaman_aktif'] - 1) ?>" class="pagination-btn <?= ($data['halaman_aktif'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
        <div class="page-numbers">
            <?php for ($i = 1; $i <= $data['total_halaman']; $i++): ?>
                <a href="<?= BASEURL ?>/siswa/katalog/<?= $i ?>" class="page-link <?= ($i == $data['halaman_aktif']) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <a href="<?= BASEURL ?>/siswa/katalog/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1) ?>" class="pagination-btn <?= ($data['halaman_aktif'] >= $data['total_halaman']) ? 'disabled' : '' ?>">Berikutnya</a>
    </div>
    <?php endif; ?>

</div>