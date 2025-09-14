<div class="content">
    <div class="main-table-container">
        <div class="table-controls-container" style="justify-content: flex-end;">
            <form id="searchForm" action="<?= BASEURL; ?>/siswa/riwayat" method="GET" class="search-form-container">
                <input type="text" id="searchInput" name="search" placeholder="Cari barang atau keperluan..." value="<?= htmlspecialchars($data['keyword'] ?? '') ?>">
                <button type="submit" class="search-submit-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#4CAF50"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                </button>
            </form>
        </div>

        <h3>Riwayat Peminjaman</h3>
        <p>Berikut adalah riwayat pengajuan dan peminjaman barang Anda.</p>

        <div class="table-wrapper" style="margin-top: 20px;">
            <?php Flasher::flash(); ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Barang yang Dipinjam</th>
                        <th>Jumlah</th>
                        <th>Keperluan</th>
                        <th>Tanggal Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['history'])): ?>
                        <?php 
                            $no = ($data['halaman_aktif'] - 1) * 10 + 1;
                            foreach ($data['history'] as $item): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <?= htmlspecialchars($item['nama_barang']); ?><br>
                                    <small style="color: #666;"><?= htmlspecialchars($item['kode_barang']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($item['jumlah_pinjam']); ?></td>
                                <td><?= htmlspecialchars($item['keperluan']); ?></td>
                                <td><?= date('d/m/Y', strtotime($item['tanggal_pinjam'])); ?></td>
                                <td><?= date('d/m/Y', strtotime($item['tanggal_wajib_kembali'])); ?></td>
                                <td>
                                    <?php
                                        $status_class = strtolower(str_replace(' ', '-', $item['status']));
                                        $status_display = htmlspecialchars($item['status']);
                                    ?>
                                    <span class="status-badge status-<?= $status_class; ?>">
                                        <?= $status_display; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">Tidak ada riwayat peminjaman saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($data['total_halaman']) && $data['total_halaman'] > 1): ?>
        <div class="pagination-container">
            <?php $queryParams = http_build_query(['search' => $data['keyword'] ?? '']); ?>
            <a href="<?= BASEURL ?>/siswa/riwayat/<?= max(1, $data['halaman_aktif'] - 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
            <div class="page-numbers">
                <?php for ($i = 1; $i <= $data['total_halaman']; $i++): ?>
                    <a href="<?= BASEURL ?>/siswa/riwayat/<?= $i ?>?<?= $queryParams ?>" class="page-link <?= ($i == $data['halaman_aktif']) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <a href="<?= BASEURL ?>/siswa/riwayat/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] >= $data['total_halaman']) ? 'disabled' : '' ?>">Berikutnya</a>
        </div>
        <?php endif; ?>
    </div>
</div>
<style>
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        color: #fff;
        white-space: nowrap;
    }
    .status-menunggu-verifikasi { background-color: #F59E0B; }
    .status-disetujui { background-color: #16A34A; }
    .status-ditolak { background-color: #DC2626; }
</style>