document.addEventListener('DOMContentLoaded', () => {
    const BASEURL = "<?= BASEURL; ?>";

    // --- Fungsionalitas Tombol Keranjang ---
    const cartButton = document.querySelector('.cart-button');
    if (cartButton) {
        cartButton.addEventListener('click', () => {
            alert('Fungsionalitas keranjang akan datang!');
        });
    }

    // --- Fungsionalitas Tombol Tambah ke Keranjang ---
    const addToCartButtons = document.querySelectorAll('.btn-keranjang');
    if (addToCartButtons) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const itemId = button.dataset.id;
                
                try {
                    const response = await fetch(`${BASEURL}/siswa/ajukan-peminjaman`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `barang_id=${itemId}`
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        alert(result.message);
                        // Opsional: perbarui tampilan setelah berhasil
                        window.location.reload(); 
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengajukan peminjaman.');
                }
            });
        });
    }
});