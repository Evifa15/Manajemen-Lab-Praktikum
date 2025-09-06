<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="main-table-container">
        <div class="header">
            <h2>Detail Barang</h2>
            <a href="<?= BASEURL; ?>/admin/barang" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="detail-card-wrapper">
            <div class="detail-card">
                <div class="image-section">
                    <?php if (isset($data['item']['gambar']) && !empty($data['item']['gambar'])): ?>
                        <img src="<?= BASEURL; ?>/img/barang/<?= htmlspecialchars($data['item']['gambar']); ?>" alt="Gambar Barang" class="item-image"/>
                    <?php else: ?>
                        <div class="no-image-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <p>Tidak ada gambar</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="info-section">
                    <h3><?= htmlspecialchars($data['item']['nama_barang']); ?></h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Kode Barang</span>
                            <span class="detail-value"><?= htmlspecialchars($data['item']['kode_barang']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Jumlah Stok</span>
                            <span class="detail-value"><?= htmlspecialchars($data['item']['jumlah']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Kondisi</span>
                            <span class="detail-value"><?= ucfirst(htmlspecialchars($data['item']['kondisi'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tanggal Pembelian</span>
                            <span class="detail-value"><?= htmlspecialchars($data['item']['tanggal_pembelian'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lokasi Penyimpanan</span>
                            <span class="detail-value"><?= htmlspecialchars($data['item']['lokasi_penyimpanan'] ?? '-'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>