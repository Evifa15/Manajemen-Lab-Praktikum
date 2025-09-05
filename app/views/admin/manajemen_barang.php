<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="header">
        <h2 class="dashboard-title">Manajemen Barang</h2>
        <button class="add-button" onclick="showAddForm()">+ Tambah Barang</button>
    </div>
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Cari barang...">
    </div>
    <div class="table-container">
        <table id="itemTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kode Barang</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>

    <div id="itemModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideModal()">&times;</span>
            <h3>Tambah/Edit Barang</h3>
            <form id="itemForm">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" id="nama_barang" name="nama_barang" required>
                <label for="kode_barang">Kode Barang</label>
                <input type="text" id="kode_barang" name="kode_barang" required>
                <label for="jumlah">Jumlah</label>
                <input type="number" id="jumlah" name="jumlah" required>
                <label for="kondisi">Kondisi</label>
                <select id="kondisi" name="kondisi">
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                    <option value="perbaikan">Perbaikan</option>
                </select>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>