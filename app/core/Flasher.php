<?php

class Flasher {
    public static function setFlash($pesan, $aksi, $tipe) {
        $_SESSION['flash'] = [
            'pesan' => $pesan,
            'aksi'  => $aksi,
            'tipe'  => $tipe // 'success' atau 'danger'
        ];
    }

    public static function flash() {
        if (isset($_SESSION['flash'])) {
            // Menggunakan kelas CSS dari desain baru Anda ('notification-box' dan 'danger'/'success')
            echo '<div class="notification-box ' . $_SESSION['flash']['tipe'] . '">
                    ' . $_SESSION['flash']['pesan'] . ' ' . $_SESSION['flash']['aksi'] . '
                  </div>';
            unset($_SESSION['flash']);
        }
    }
}