<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <h2 class="dashboard-title">Dashboard Admin</h2>
    <div class="card-container">
        <div class="card">
            <div class="card-icon"><i class="fas fa-users"></i></div>
            <div class="card-content">
                <p>Total Pengguna</p>
                <h3><?= $data['totalPengguna'] ?? 0; ?></h3>
            </div>
        </div>

        <div class="card">
            <div class="card-icon"><i class="fas fa-box-open"></i></div>
            <div class="card-content">
                <p>Total Barang</p>
                <h3><?= $data['totalBarang'] ?? 0; ?></h3>
            </div>
        </div>

        <div class="card">
            <div class="card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="card-content">
                <p>Total Kelas</p>
                <h3><?= $data['totalKelas'] ?? 0; ?></h3>
            </div>
        </div>
    </div>

    <div class="section peminjaman-terbaru">
        <h3>Peminjaman Terbaru</h3>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peminjam</th>
                        <th>Nama Barang</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($data['peminjamanTerbaru']) && count($data['peminjamanTerbaru']) > 0): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($data['peminjamanTerbaru'] as $peminjaman): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($peminjaman['nama_peminjam']); ?></td>
                                <td><?= htmlspecialchars($peminjaman['nama_barang']); ?></td>
                                <td><?= htmlspecialchars($peminjaman['tanggal_pinjam']); ?></td>
                                <td><span class="status-badge <?= strtolower($peminjaman['status']); ?>"><?= htmlspecialchars($peminjaman['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data peminjaman terbaru.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section statistik-peminjaman">
        <h3>Statistik Aktivitas Peminjaman</h3>
        <p>Grafik aktivitas peminjaman bulanan akan ditampilkan di sini.</p>
        <div class="chart-container">
            <canvas id="peminjamanChart"></canvas>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>