<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="header">
        <h2 class="dashboard-title">Manajemen Kelas</h2>
        <button class="add-button" onclick="showAddForm()">+ Tambah Kelas</button>
    </div>
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Cari kelas...">
    </div>
    <div class="table-container">
        <table id="classTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Wali Kelas</th>
                    <th>Jumlah Siswa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>

    <div id="classModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideModal()">&times;</span>
            <h3>Tambah/Edit Kelas</h3>
            <form id="classForm">
                <label for="nama_kelas">Nama Kelas</label>
                <input type="text" id="nama_kelas" name="nama_kelas" required>
                <label for="wali_kelas">Wali Kelas</label>
                <input type="text" id="wali_kelas" name="wali_kelas" required>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>