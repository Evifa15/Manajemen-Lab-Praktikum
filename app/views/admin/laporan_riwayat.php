<?php require_once '../app/views/layouts/admin_header.php'; ?>

<style>
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        color: #fff;
    }
    .status-dipinjam { background-color: #3B82F6; }
    .status-dikembalikan { background-color: #16A34A; }
    .status-terlambat { background-color: #DC2626; }
</style>

<div class="content">
    <div class="main-table-container">
        <div class="table-controls-container" style="display: block;">
            <h4>Laporan Riwayat Peminjaman</h4>
            
            <form action="<?= BASEURL ?>/admin/laporan" method="get" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; margin-top: 1rem;">
                <div style="flex-grow: 1;">
                    <label for="search" style="font-size: 14px; margin-bottom: 5px; display:block;">Cari Peminjam / Barang</label>
                    <input type="text" id="search" name="search" placeholder="Masukkan nama peminjam, ID, atau barang..." value="<?= htmlspecialchars($data['filters']['keyword'] ?? '') ?>" style="width: 100%; padding: 8px;">
                </div>
                <div>
                    <label for="start_date" style="font-size: 14px; margin-bottom: 5px; display:block;">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($data['filters']['start_date'] ?? '') ?>" style="padding: 7px;">
                </div>
                <div>
                    <label for="end_date" style="font-size: 14px; margin-bottom: 5px; display:block;">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($data['filters']['end_date'] ?? '') ?>" style="padding: 7px;">
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="add-button" style="padding: 8px 15px;">Filter</button>
                    <a href="<?= BASEURL ?>/admin/laporan" class="btn btn-secondary" style="text-decoration: none; padding: 8px 15px; border-radius: 5px;">Reset</a>
                    
                    <!-- ✅ TOMBOL BARU: Untuk Unduh Laporan -->
                    <?php 
                        $queryParams = http_build_query($data['filters']);
                    ?>
                    <a href="<?= BASEURL ?>/admin/unduhLaporan?<?= $queryParams ?>" class="add-button" style="background-color: #166534; text-decoration: none; padding: 8px 15px;">
                        Unduh Laporan
                    </a>
                </div>
            </form>
        </div>

        <table id="laporan-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Peminjam</th>
                    <th>No. ID</th>
                    <th>Nama Barang</th>
                    <th>Tgl. Pinjam</th>
                    <th>Tgl. Kembali</th>
                    <th>Status</th>
                    <th>Bukti Kembali</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['history'])):
                    $no = ($data['halaman_aktif'] - 1) * 10 + 1;
                    foreach($data['history'] as $item): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($item['nama_peminjam']); ?></td>
                        <td><?= htmlspecialchars($item['no_id_peminjam']); ?></td>
                        <td><?= htmlspecialchars($item['nama_barang']); ?></td>
                        <td><?= date('d/m/Y', strtotime($item['tanggal_pinjam'])); ?></td>
                        <td><?= $item['tanggal_kembali'] ? date('d/m/Y', strtotime($item['tanggal_kembali'])) : '-'; ?></td>
                        <td>
                            <?php
                                $status_class = 'status-' . strtolower(str_replace(' ', '', $item['status']));
                                $status_text = ($item['status'] == 'Dikembalikan') ? 'Tepat Waktu' : $item['status'];
                                echo "<span class='status-badge {$status_class}'>" . htmlspecialchars($status_text) . "</span>";
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if (!empty($item['bukti_kembali'])): ?>
                                <button class="view-bukti-btn" data-img-url="<?= BASEURL . '/img/bukti_kembali/' . htmlspecialchars($item['bukti_kembali']); ?>" title="Lihat Bukti">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg>
                                </button>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach;
                else: ?>
                    <tr><td colspan="8" style="text-align: center;">Tidak ada data riwayat yang cocok.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="pagination-container">
            <a href="<?= BASEURL ?>/admin/laporan/<?= max(1, $data['halaman_aktif'] - 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
            <div class="page-numbers">
                <?php for ($i = 1; $i <= $data['total_halaman']; $i++): ?>
                    <a href="<?= BASEURL ?>/admin/laporan/<?= $i ?>?<?= $queryParams ?>" class="page-link <?= ($i == $data['halaman_aktif']) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <a href="<?= BASEURL ?>/admin/laporan/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] >= $data['total_halaman']) ? 'disabled' : '' ?>">Berikutnya</a>
        </div>
    </div>
</div>

<div id="buktiModal" class="modal">
    <div class="modal-content" style="max-width: 600px; padding: 10px;">
        <span class="close-button" style="top: 10px; right: 15px;">&times;</span>
        <img id="buktiModalImg" src="" alt="Bukti Pengembalian" style="width: 100%; height: auto; display: block;">
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>
