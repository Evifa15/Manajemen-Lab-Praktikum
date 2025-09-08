document.addEventListener('DOMContentLoaded', () => {
    
    console.log('DEBUG: siswa-script.js dimuat.');

    // Logika untuk Modal Keranjang
    const cartButton = document.querySelector('.cart-button');
    const keranjangModal = document.getElementById('keranjangModal');

    console.log('DEBUG: Mencari tombol keranjang:', cartButton);
    console.log('DEBUG: Mencari modal keranjang:', keranjangModal);

    if (cartButton && keranjangModal) {
        console.log('DEBUG: Tombol dan modal ditemukan, event listener ditambahkan.');
        const closeBtn = keranjangModal.querySelector('.close-button');

        cartButton.addEventListener('click', (e) => {
            e.preventDefault(); // Mencegah aksi default
            console.log('DEBUG: Tombol keranjang diklik! Menambahkan class active.');
            // PERBAIKAN: Gunakan classList.add('active') untuk menampilkan modal
            keranjangModal.classList.add('active'); 
        });

        const closeModal = () => {
            // PERBAIKAN: Gunakan classList.remove('active') untuk menyembunyikan modal
            keranjangModal.classList.remove('active');
        };

        closeBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => {
            if (e.target === keranjangModal) {
                closeModal();
            }
        });
    } else {
        console.error('DEBUG: Tombol keranjang atau modal tidak ditemukan. Periksa kembali class/id di HTML.');
    }
});