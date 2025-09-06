<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="main-table-container">
        <div class="table-controls-container">
            <div class="search-and-filter">
                <input type="text" id="searchInput" placeholder="Cari pengguna...">
            </div>
            <button class="add-button" id="addUserBtn">+ Tambah Pengguna</button>
        </div>
        
        <table id="userTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pengguna</th>
                    <th>Nama Pengguna</th>
                    <!-- ✅ KOLOM EMAIL DIKEMBALIKAN -->
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($data['users']) && is_array($data['users']) && !empty($data['users'])): ?>
                    <?php 
                    $no = 1;
                    if (isset($data['halaman_aktif']) && $data['halaman_aktif'] > 1) {
                        $no = ($data['halaman_aktif'] - 1) * 10 + 1;
                    }
                    ?>
                    <?php foreach ($data['users'] as $user): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($user['id_pengguna']); ?></td>
                        <td><?= htmlspecialchars($user['username']); ?></td>
                        <!-- ✅ DATA EMAIL DIKEMBALIKAN -->
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= ucfirst(htmlspecialchars($user['role'])); ?></td>
                        <td class="action-buttons">
                            <button class="edit-btn" data-id="<?= $user['id']; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>
                            </button>
                            <button class="delete-btn" data-id="<?= $user['id']; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data pengguna.</td> <!-- Colspan diubah kembali menjadi 6 -->
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if (isset($data['total_halaman'])): ?>
        <div class="pagination-container">
            <a href="<?= BASEURL; ?>/admin/pengguna/<?= max(1, $data['halaman_aktif'] - 1); ?>" class="pagination-btn <?= $data['halaman_aktif'] <= 1 ? 'disabled' : ''; ?>">Sebelumnya</a>
            <div class="page-numbers">
                <?php for($i = 1; $i <= $data['total_halaman']; $i++): ?>
                    <a href="<?= BASEURL; ?>/admin/pengguna/<?= $i; ?>" class="page-link <?= $data['halaman_aktif'] == $i ? 'active' : ''; ?>"><?= $i; ?></a>
                <?php endfor; ?>
            </div>
            <a href="<?= BASEURL; ?>/admin/pengguna/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1); ?>" class="pagination-btn <?= $data['halaman_aktif'] >= $data['total_halaman'] ? 'disabled' : ''; ?>">Berikutnya</a>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Modal untuk Tambah dan Edit Pengguna -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3 class="modal-title">Pengguna</h3>
            <form id="userForm" action="<?= BASEURL; ?>/admin/tambahPengguna" method="POST">
                <input type="hidden" id="userId" name="id">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" id="username" name="username" placeholder="Nama Pengguna" required>
                    </div>
                    <!-- ✅ FORM EMAIL DIKEMBALIKAN -->
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" id="id_pengguna" name="id_pengguna" placeholder="ID Pengguna (NIS/NIP)" required>
                    </div>
                    <div class="form-group">
                         <input type="password" id="password" name="password" placeholder="Kata Sandi">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <select id="role" name="role" required>
                            <option value="" disabled selected>Pilih Peran</option>
                            <option value="admin">Admin</option>
                            <option value="guru">Guru</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                </div>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Modal untuk Konfirmasi Hapus Pengguna -->
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
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>
