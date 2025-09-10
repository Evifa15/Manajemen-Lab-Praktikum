<div class="content">
    <div class="main-table-container">
        <h3>Verifikasi Peminjaman</h3>
        <p>Berikut adalah daftar pengajuan peminjaman dari siswa perwalian Anda yang membutuhkan persetujuan.</p>

        <div class="table-wrapper" style="margin-top: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Barang yang Dipinjam</th>
                        <th>Tanggal Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Keperluan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['requests'])): ?>
                        <?php $no = 1; foreach ($data['requests'] as $req): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($req['nama_siswa']); ?></td>
                                <td><?= htmlspecialchars($req['nama_barang']); ?></td>
                                <td><?= date('d/m/Y', strtotime($req['tanggal_pinjam'])); ?></td>
                                <td><?= date('d/m/Y', strtotime($req['tanggal_kembali_diajukan'])); ?></td>
                                <td><?= htmlspecialchars($req['keperluan']); ?></td>
                                <td class="action-buttons">
                                    <form action="<?= BASEURL; ?>/guru/proses-verifikasi" method="post" style="display:inline;">
                                        <input type="hidden" name="peminjaman_id" value="<?= $req['id']; ?>">
                                        <button type="submit" name="status" value="Disetujui" class="btn" style="background-color: #16A34A;">Setujui</button>
                                    </form>
                                    <form action="<?= BASEURL; ?>/guru/proses-verifikasi" method="post" style="display:inline;">
                                        <input type="hidden" name="peminjaman_id" value="<?= $req['id']; ?>">
                                        <button type="submit" name="status" value="Ditolak" class="btn btn-danger">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">Tidak ada permintaan verifikasi saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>