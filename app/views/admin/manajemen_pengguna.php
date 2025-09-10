<?php require_once '../app/views/layouts/admin_header.php'; ?>

<div class="content">
    <div class="main-table-container">
        <div class="table-controls-container">
            
            <form action="<?= BASEURL; ?>/admin/pengguna" method="GET" class="search-and-filter-form">
                
                <div class="search-container">
                    <input type="text" name="search" id="searchInput" placeholder="Cari nama atau ID pengguna..." value="<?= htmlspecialchars($data['filters']['keyword'] ?? '') ?>">
                    <button type="submit" class="search-submit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24" width="24px" fill="#4CAF50"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                    </button>
                </div>

                <div class="filter-container">
                    <select name="filter_role" onchange="this.form.submit()">
                        <option value="">Semua Peran</option>
                        <option value="admin" <?= ($data['filters']['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="guru" <?= ($data['filters']['role'] ?? '') == 'guru' ? 'selected' : ''; ?>>Guru</option>
                        <option value="siswa" <?= ($data['filters']['role'] ?? '') == 'siswa' ? 'selected' : ''; ?>>Siswa</option>
                    </select>
                </div>
            </form>

            <div class="actions-container">
                <button class="add-button" id="addUserBtn">+ Tambah Pengguna</button>
            </div>
        </div>
        
        <div class="table-wrapper">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Pengguna</th>
                        <th>Nama Pengguna</th>
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
                            <td colspan="6" class="text-center">Tidak ada data pengguna.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
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
    
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3 class="modal-title">Pengguna</h3>
            <form id="userForm" action="<?= BASEURL; ?>/admin/tambah-pengguna" method="POST">
                <input type="hidden" id="userId" name="id">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" id="username" name="username" placeholder="Nama Pengguna" required>
                    </div>
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
                        </select>>
                    </div>
                </div>
                <button type="submit">Simpan</button>
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
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>