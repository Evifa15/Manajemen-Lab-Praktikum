<div class="content">
    <div class="main-table-container">
        <div class="table-controls-container" style="justify-content: flex-end;">
            <form id="searchForm" action="<?= BASEURL; ?>/guru/verifikasi" method="GET" class="search-form-container">
                <input type="text" id="searchInput" name="search" placeholder="Cari nama siswa, barang, atau ID..." value="<?= htmlspecialchars($data['keyword'] ?? '') ?>">
                <button type="submit" class="search-submit-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#4CAF50"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                </button>
            </form>
        </div>
        <div class="table-wrapper" style="margin-top: 20px;">
            <?php Flasher::flash(); ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Barang Dipinjam</th>
                        <th>Jml</th>
                        <th>Keperluan</th>
                        <th>Tgl. Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['requests'])): ?>
                        <?php $no = 1; foreach ($data['requests'] as $req): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <?= htmlspecialchars($req['nama_siswa']); ?><br>
                                    <small style="color: #666;"><?= htmlspecialchars($req['id_siswa']); ?></small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($req['nama_barang']); ?><br>
                                    <small style="color: #666;"><?= htmlspecialchars($req['kode_barang']); ?> | Stok: <?= htmlspecialchars($req['stok_barang']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($req['jumlah_pinjam']); ?></td>
                                <td><?= htmlspecialchars($req['keperluan']); ?></td>
                                <td><?= date('d/m/Y', strtotime($req['tanggal_pinjam'])); ?></td>
                                <td><?= date('d/m/Y', strtotime($req['tanggal_wajib_kembali'])); ?></td>
                                <td class="action-buttons">
                                    <form action="<?= BASEURL; ?>/guru/proses-verifikasi" method="post" style="display:inline;">
                                        <input type="hidden" name="peminjaman_id" value="<?= $req['id']; ?>">
                                        <button type="submit" name="status" value="Disetujui" class="btn" style="background-color: #16A34A;">Setujui</button>
                                    </form>
                                    <button type="button" class="btn btn-danger open-modal-tolak-btn" data-id="<?= $req['id']; ?>">Tolak</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center;">Tidak ada permintaan verifikasi saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isset($data['total_halaman']) && $data['total_halaman'] > 1): ?>
            <div class="pagination-container">
                <?php
                    $queryParams = http_build_query(['search' => $data['keyword'] ?? '']);
                ?>
                <a href="<?= BASEURL ?>/guru/verifikasi/<?= max(1, $data['halaman_aktif'] - 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
                <div class="page-numbers">
                    <?php for ($i = 1; $i <= $data['total_halaman']; $i++): ?>
                        <a href="<?= BASEURL ?>/guru/verifikasi/<?= $i ?>?<?= $queryParams ?>" class="page-link <?= ($i == $data['halaman_aktif']) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <a href="<?= BASEURL ?>/guru/verifikasi/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] >= $data['total_halaman']) ? 'disabled' : '' ?>">Berikutnya</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="tolakModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close-button">&times;</span>
        <h3 class="modal-title">Berikan Alasan Penolakan</h3>
        <p style="color: #666; margin-bottom: 20px;">Silakan isi alasan penolakan agar siswa dapat melihatnya.</p>
        <form id="tolakForm" method="POST" action="<?= BASEURL; ?>/guru/proses-verifikasi">
            <input type="hidden" id="peminjamanIdTolak" name="peminjaman_id">
            <input type="hidden" name="status" value="Ditolak">
            <div class="form-group">
                <label for="alasan-tolak">Alasan Penolakan</label>
                <textarea id="alasan-tolak" name="keterangan" rows="4" placeholder="Contoh: Barang tidak dapat dipinjam karena stok tidak sesuai atau sedang dalam perbaikan." required></textarea>
            </div>
            <button type="submit">Konfirmasi Penolakan</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tolakModal = document.getElementById('tolakModal');
        const openModalBtns = document.querySelectorAll('.open-modal-tolak-btn');
        const closeBtn = tolakModal.querySelector('.close-button');
        const peminjamanIdInput = document.getElementById('peminjamanIdTolak');
        
        openModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                peminjamanIdInput.value = btn.dataset.id;
                tolakModal.classList.add('active');
            });
        });

        closeBtn.addEventListener('click', () => {
            tolakModal.classList.remove('active');
        });

        window.addEventListener('click', (e) => {
            if (e.target === tolakModal) {
                tolakModal.classList.remove('active');
            }
        });
    });
</script>