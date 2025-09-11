<?php require_once '../app/views/layouts/admin_header.php'; ?>

<style>
    .tab-links-wrapper {
        display: flex;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 1.5rem;
    }
    .tab-link {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
        font-weight: 500;
        color: #6c757d;
        border-bottom: 3px solid transparent;
        text-decoration: none;
        margin-bottom: -2px;
    }
    .tab-link.active {
        color: #4CAF50;
        border-bottom-color: #4CAF50;
    }
    .tab-content.active {
        display: block;
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<div class="content">
    <div class="main-table-container">
        <div class="tab-links-wrapper">
            <a href="<?= BASEURL; ?>/admin/pengguna/staff" class="tab-link <?= (isset($data['active_tab']) && $data['active_tab'] == 'staff') ? 'active' : '' ?>">Daftar Staff</a>
            <a href="<?= BASEURL; ?>/admin/pengguna/guru" class="tab-link <?= (isset($data['active_tab']) && $data['active_tab'] == 'guru') ? 'active' : '' ?>">Daftar Guru</a>
            <a href="<?= BASEURL; ?>/admin/pengguna/siswa" class="tab-link <?= (isset($data['active_tab']) && $data['active_tab'] == 'siswa') ? 'active' : '' ?>">Daftar Siswa</a>
            <a href="<?= BASEURL; ?>/admin/pengguna/akun" class="tab-link <?= (isset($data['active_tab']) && $data['active_tab'] == 'akun') ? 'active' : '' ?>">Daftar Akun</a>
        </div>

        <div class="tab-content active">
    <?php
    if (isset($data['active_tab']) && !empty($data['active_tab'])) {
        $tabView = '../app/views/admin/pengguna/_tab_' . $data['active_tab'] . '.php';
        if (file_exists($tabView)) {
            require_once $tabView;
        } else {
            echo "<p>Tampilan untuk tab '{$data['active_tab']}' tidak ditemukan.</p>";
        }
    }
    ?>
</div>
    </div>
</div>

<div id="staffModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <span class="close-button">&times;</span>
        <h3 id="staffModalTitle">Tambah Staff</h3>
        <form id="staffForm" method="POST" action="<?= BASEURL; ?>/admin/tambah-staff" enctype="multipart/form-data">
            <input type="hidden" id="staffId" name="id">
            <div class="form-row">
                <div class="form-group"><label for="nama">Nama Staff</label><input type="text" id="nama" name="nama" required></div>
                <div class="form-group"><label for="id_staff">ID Staff</label><input type="text" id="id_staff" name="id_staff" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="jenis_kelamin_staff">Jenis Kelamin</label><select id="jenis_kelamin_staff" name="jenis_kelamin" required><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                <div class="form-group"><label for="ttl_staff">Tempat, Tanggal Lahir</label><input type="text" id="ttl_staff" name="ttl"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="agama_staff">Agama</label><input type="text" id="agama_staff" name="agama"></div>
                <div class="form-group"><label for="no_hp_staff">No. HP</label><input type="text" id="no_hp_staff" name="no_hp"></div>
            </div>
            <div class="form-group"><label for="alamat_staff">Alamat</label><textarea id="alamat_staff" name="alamat" rows="2"></textarea></div>
            <div class="form-group"><label for="email_staff">Email</label><input type="email" id="email_staff" name="email"></div>
            <button type="submit">Simpan</button>
        </form>
    </div>
</div>

<div id="importStaffModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 id="importStaffModalTitle">Import Data Staff</h3>
        <form id="importStaffForm" action="<?= BASEURL; ?>/admin/import-staff" method="POST" enctype="multipart/form-data">
            <div class="import-instructions" style="background-color: #f0f2f5; border-left: 4px solid #4CAF50; padding: 15px; margin-bottom: 1.5rem; border-radius: 5px; font-size: 14px;">
                <strong>Petunjuk:</strong>
                <ul style="padding-left: 20px; margin-top: 10px; line-height: 1.6;">
                    <li>Gunakan file dengan format <strong>.csv</strong> (Comma Separated Values).</li>
                    <li>Pastikan urutan kolom di file CSV Anda adalah: <br><strong>Nama, ID Staff, Jenis Kelamin, No. HP, Email</strong>.</li>
                    <li>Baris pertama (header) akan diabaikan secara otomatis.</li>
                    <li>Akun login akan dibuat otomatis dengan <strong>Nama</strong> sebagai username dan <strong>ID Staff</strong> sebagai password awal.</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="file_import_staff">Pilih File CSV untuk Diimpor</label>
                <input type="file" id="file_import_staff" name="file_import_staff" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 100%;">
            </div>
            <button type="submit">Unggah dan Proses</button>
        </form>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content delete-modal">
        <span class="close-button">&times;</span>
        <div class="delete-icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 0 24 24" width="48px" fill="#ef4444"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        </div>
        <p class="modal-message">Apakah Anda yakin ingin menghapus data ini?</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="cancelDelete">Batal</button>
            <a href="#" class="btn btn-danger" id="confirmDelete">Ya, Hapus</a>
        </div>
    </div>
</div>

<div id="guruModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <span class="close-button">&times;</span>
        <h3 id="guruModalTitle">Tambah Guru</h3>
        <form id="guruForm" method="POST" action="<?= BASEURL; ?>/admin/tambah-guru" enctype="multipart/form-data">
            <input type="hidden" id="guruId" name="id">
            <div class="form-row">
                <div class="form-group"><label for="nama_guru">Nama Guru</label><input type="text" id="nama_guru" name="nama" required></div>
                <div class="form-group"><label for="nip_guru">ID Guru (NIP)</label><input type="text" id="nip_guru" name="nip" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="jenis_kelamin_guru">Jenis Kelamin</label><select id="jenis_kelamin_guru" name="jenis_kelamin" required><option value="Laki laki">Laki laki</option> <option value="Perempuan">Perempuan</option></select></div>
                <div class="form-group"><label for="ttl_guru">Tempat, Tanggal Lahir</label><input type="text" id="ttl_guru" name="ttl"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="agama_guru">Agama</label><input type="text" id="agama_guru" name="agama"></div>
                <div class="form-group"><label for="no_hp_guru">No. HP</label><input type="text" id="no_hp_guru" name="no_hp"></div>
            </div>
            <div class="form-group"><label for="alamat_guru">Alamat</label><textarea id="alamat_guru" name="alamat" rows="2"></textarea></div>
            <div class="form-group"><label for="email_guru">Email</label><input type="email" id="email_guru" name="email"></div>
            <button type="submit">Simpan</button>
        </form>
    </div>
</div>

<div id="importGuruModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 id="importGuruModalTitle">Import Data Guru</h3>
        <form id="importGuruForm" action="<?= BASEURL; ?>/admin/import-guru" method="POST" enctype="multipart/form-data">
            <div class="import-instructions" style="background-color: #f0f2f5; border-left: 4px solid #4CAF50; padding: 15px; margin-bottom: 1.5rem; border-radius: 5px; font-size: 14px;">
                <strong>Petunjuk:</strong>
                <ul style="padding-left: 20px; margin-top: 10px; line-height: 1.6;">
                    <li>Gunakan file dengan format <strong>.csv</strong>.</li>
                    <li>Pastikan urutan kolom: <strong>Nama, NIP, Jenis Kelamin, No. HP, Email</strong>.</li>
                    <li>Baris pertama (header) akan diabaikan.</li>
                    <li>Akun login akan dibuat otomatis dengan <strong>Nama</strong> sebagai username dan <strong>NIP</strong> sebagai password awal.</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="file_import_guru">Pilih File CSV untuk Diimpor</label>
                <input type="file" id="file_import_guru" name="file_import_guru" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 100%;">
            </div>
            <button type="submit">Unggah dan Proses</button>
        </form>
    </div>
</div>

<div id="ubahPasswordModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close-button">&times;</span>
        <h3 id="ubahPasswordModalTitle">Ubah Kata Sandi</h3>
        <p>Mengubah kata sandi untuk akun <strong id="username-akun"></strong>.</p>
        <form id="ubahPasswordForm" method="POST" action="<?= BASEURL; ?>/admin/ubah-password-akun">
            <input type="hidden" id="akunId" name="id">
            <div class="form-group">
                <label for="password-baru">Kata Sandi Baru</label>
                <input type="password" id="password-baru" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="konfirmasi-password">Konfirmasi Kata Sandi Baru</label>
                <input type="password" id="konfirmasi-password" name="confirm_password" required>
            </div>
            <button type="submit">Simpan Kata Sandi Baru</button>
        </form>
    </div>
</div>
<?php require_once '../app/views/layouts/admin_footer.php'; ?>