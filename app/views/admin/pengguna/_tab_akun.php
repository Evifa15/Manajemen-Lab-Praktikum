<div class="table-controls-container">
    <form id="searchAkunForm" action="<?= BASEURL; ?>/admin/pengguna/akun" method="GET" class="search-and-filter-form">
        <div class="search-container">
            <input type="text" name="search" id="searchAkunInput" placeholder="Cari nama atau ID pengguna..." value="<?= htmlspecialchars($data['filters']['keyword'] ?? '') ?>">
        </div>
        <div class="filter-container">
            <select name="filter_role" onchange="this.form.submit()">
                <option value="">Semua Peran</option>
                <option value="admin" <?= (($data['filters']['role'] ?? '') == 'admin') ? 'selected' : ''; ?>>Staff</option>
                <option value="guru" <?= (($data['filters']['role'] ?? '') == 'guru') ? 'selected' : ''; ?>>Guru</option>
                <option value="siswa" <?= (($data['filters']['role'] ?? '') == 'siswa') ? 'selected' : ''; ?>>Siswa</option>
            </select>
        </div>
    </form>
</div>

<?php Flasher::flash(); ?>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pengguna</th>
            <th>ID Pengguna</th>
            <th>Kata Sandi (Hashed)</th>
            <th>Peran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody id="akunTableBody">
        <?php if (!empty($data['users'])):
            $no = ($data['halaman_aktif'] - 1) * $data['limit'] + 1;
            foreach ($data['users'] as $user): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($user['username']); ?></td>
                <td><?= htmlspecialchars($user['id_pengguna'] ?? '-'); ?></td>
                <td style="word-break: break-all; max-width: 250px; font-family: monospace; font-size: 12px;"><?= htmlspecialchars($user['password'] ?? 'Data tidak tersedia'); ?></td>
                <td><?= ucfirst(htmlspecialchars($user['role'])); ?></td>
                <td class="action-buttons">
                    <button class="edit-akun-btn" data-id="<?= $user['id']; ?>" data-username="<?= htmlspecialchars($user['username']); ?>" title="Ubah Kata Sandi">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>
                    </button>
                </td>
            </tr>
            <?php endforeach;
        else: ?>
            <tr><td colspan="6" class="text-center" style="text-align: center;">Tidak ada data akun yang ditemukan.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($data['total_halaman']) && $data['total_halaman'] > 1): ?>
<div class="pagination-container">
    <?php
        $queryParams = http_build_query($data['filters'] ?? []);
    ?>
    <a href="<?= BASEURL ?>/admin/pengguna/akun/<?= max(1, $data['halaman_aktif'] - 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] <= 1) ? 'disabled' : '' ?>">Sebelumnya</a>
    <div class="page-numbers">
        <?php for ($i = 1; $i <= $data['total_halaman']; $i++): ?>
            <a href="<?= BASEURL ?>/admin/pengguna/akun/<?= $i ?>?<?= $queryParams ?>" class="page-link <?= ($i == $data['halaman_aktif']) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <a href="<?= BASEURL ?>/admin/pengguna/akun/<?= min($data['total_halaman'], $data['halaman_aktif'] + 1) ?>?<?= $queryParams ?>" class="pagination-btn <?= ($data['halaman_aktif'] >= $data['total_halaman']) ? 'disabled' : '' ?>">Berikutnya</a>
</div>
<?php endif; ?>