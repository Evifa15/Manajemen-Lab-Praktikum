<div class="content main-table-container">
    <div class="container-detail-barang">
        <div class="header-detail-barang">
            <h2 class="title-detail-barang">Detail Barang</h2>
            <a href="<?= BASEURL ?>/siswa/katalog" class="back-btn-detail-barang">&laquo; Kembali ke Katalog</a>
        </div>
        
        <?php if ($data['barang']): ?>
            <div class="card-detail-barang">
                <div class="image-wrapper-detail-barang">
                    <?php 
                        $gambar_url = (!empty($data['barang']['gambar']) && file_exists('img/barang/' . $data['barang']['gambar']))
                            ? BASEURL . '/img/barang/' . htmlspecialchars($data['barang']['gambar'])
                            : BASEURL . '/img/siswa/default.png';
                    ?>
                    <img src="<?= $gambar_url; ?>" alt="<?= htmlspecialchars($data['barang']['nama_barang']); ?>">
                </div>
                <div class="info-wrapper-detail-barang">
                    <h3 class="info-title-detail-barang"><?= htmlspecialchars($data['barang']['nama_barang']); ?></h3>
                    <p class="info-item-detail-barang">
                        <span class="label-detail-barang">Kode Barang:</span>
                        <span class="value-detail-barang"><?= htmlspecialchars($data['barang']['kode_barang']); ?></span>
                    </p>
                    <p class="info-item-detail-barang">
                        <span class="label-detail-barang">Kategori:</span>
                        <span class="value-detail-barang"><?= htmlspecialchars($data['barang']['kategori']); ?></span>
                    </p>
                    <p class="info-item-detail-barang">
                        <span class="label-detail-barang">Jumlah Tersedia:</span>
                        <span class="value-detail-barang"><?= htmlspecialchars($data['barang']['jumlah']); ?></span>
                    </p>
                    <p class="info-item-detail-barang">
                        <span class="label-detail-barang">Deskripsi:</span>
                        <span class="value-detail-barang"><?= htmlspecialchars($data['barang']['deskripsi']); ?></span>
                    </p>
                    <p class="info-item-detail-barang">
                        <span class="label-detail-barang">Lokasi:</span>
                        <span class="value-detail-barang"><?= htmlspecialchars($data['barang']['lokasi_penyimpanan']); ?></span>
                    </p>
                    
                    <button class="btn btn-primary add-to-cart-btn-detail" data-id="<?= $data['barang']['id']; ?>">
                        Tambah ke Keranjang
                    </button>
                </div>
            </div>
        <?php else: ?>
            <p class="not-found-detail-barang">Barang tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>