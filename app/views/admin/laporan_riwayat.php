<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <h2 class="dashboard-title">Laporan Riwayat Peminjaman</h2>
    <div class="report-controls">
        <label for="startDate">Dari Tanggal:</label>
        <input type="date" id="startDate">
        <label for="endDate">Sampai Tanggal:</label>
        <input type="date" id="endDate">
        <button class="search-button">Filter</button>
    </div>
    <div class="table-container">
        <table id="reportTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Peminjam</th>
                    <th>Nama Barang</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Tanggal Pengembalian</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>