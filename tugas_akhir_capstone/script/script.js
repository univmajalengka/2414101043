if (typeof AOS !== 'undefined') {
    AOS.init({
        duration: 1000,
        once: true
    });
}

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        if (window.scrollY > 100) {
            navbar.style.background = 'linear-gradient(135deg, rgba(13, 110, 253, 1) 0%, rgba(32, 201, 151, 1) 100%)';
        } else {
            navbar.style.background = 'linear-gradient(135deg, rgba(13, 110, 253, 0.95) 0%, rgba(32, 201, 151, 0.95) 100%)';
        }
    }

    const scrollTop = document.getElementById('scrollTop');
    if (scrollTop) {
        if (window.scrollY > 300) {
            scrollTop.classList.add('show');
        } else {
            scrollTop.classList.remove('show');
        }
    }
});

const btnScrollTop = document.getElementById('scrollTop');
if (btnScrollTop) {
    btnScrollTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

const sections = document.querySelectorAll('section');
const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (scrollY >= (sectionTop - 200)) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href').startsWith('#') && link.getAttribute('href').slice(1) === current) {
            link.classList.add('active');
        }
    });
});

document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', function() {
        const img = this.querySelector('img');
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9); display: flex; align-items: center;
            justify-content: center; z-index: 9999; cursor: pointer;
        `;
        
        const modalImg = document.createElement('img');
        modalImg.src = img.src;
        modalImg.style.cssText = `max-width: 90%; max-height: 90%; border-radius: 10px;`;
        
        modal.appendChild(modalImg);
        document.body.appendChild(modal);
        
        modal.addEventListener('click', function() {
            document.body.removeChild(modal);
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const formPesanan = document.getElementById('formPesanan');

    if (formPesanan) {

        const tglInput = document.getElementById('tanggal_pesan');
        if (tglInput) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        
        const today = `${year}-${month}-${day}`;
        
        tglInput.setAttribute('min', today);
        }

        console.log("Form Pesanan Ditemukan!");

        const waktuIn = document.getElementById('waktu_pelaksanaan');
        const pesertaIn = document.getElementById('jumlah_peserta');
        const paketIn = document.getElementById('id_paket');
        const hargaDisplay = document.getElementById('harga_paket_display');
        const totalIn = document.getElementById('total_tagihan');
        const btnReset = document.getElementById('btnReset');

        function hitungTotal() {
            let waktu = parseInt(waktuIn.value) || 0;
            let peserta = parseInt(pesertaIn.value) || 0;
            
            let hargaPaket = 0;
            if (paketIn.selectedIndex > 0) {
                let selectedOption = paketIn.options[paketIn.selectedIndex];
                hargaPaket = parseInt(selectedOption.getAttribute('data-harga'));
            }

            console.log("Menghitung: ", waktu, "hari x", peserta, "orang x", hargaPaket, "rupiah"); // Debugging

            if(hargaDisplay) {
                hargaDisplay.value = "Rp " + new Intl.NumberFormat('id-ID').format(hargaPaket);
            }

            let total = waktu * peserta * hargaPaket;

            if(totalIn) {
                totalIn.value = total;
            }
        }

        if(waktuIn) waktuIn.addEventListener('input', hitungTotal);
        if(pesertaIn) pesertaIn.addEventListener('input', hitungTotal);
        if(paketIn) paketIn.addEventListener('change', hitungTotal);

        hitungTotal();

        if (btnReset) {
            btnReset.addEventListener('click', function() {
                const idInput = document.getElementById('id');
                if(idInput) idInput.value = '';
                
                setTimeout(hitungTotal, 100); 
            });
        }
    } else {
        console.log("Form pesanan tidak ditemukan di halaman ini (Mungkin halaman index/login).");
    }
});

window.editData = function(id, nama, hp, tgl, waktu, peserta, paket) {
    console.log("Edit Data Triggered: ", id);
    
    if (document.getElementById('id')) {
        document.getElementById('id').value = id;
        document.getElementById('nama_pemesan').value = nama;
        document.getElementById('no_hp').value = hp;
        document.getElementById('tanggal_pesan').value = tgl;
        document.getElementById('waktu_pelaksanaan').value = waktu;
        document.getElementById('jumlah_peserta').value = peserta;
        document.getElementById('id_paket').value = paket;
        
        const event = new Event('change');
        document.getElementById('id_paket').dispatchEvent(event);

        const formHeader = document.querySelector('.card-custom');
        if (formHeader) {
            formHeader.scrollIntoView({ behavior: 'smooth' });
        }
    } else {
        console.error("Elemen ID tidak ditemukan");
    }
};