/* ===================================================
 * Profile Toggle (Database Version)
 * Menggunakan fetch() untuk menyimpan state ke DB
 * =================================================== */

document.addEventListener('DOMContentLoaded', () => {

  const allToggleButtons = document.querySelectorAll('button.toggle-section');

  /**
   * Mengubah teks & ikon tombol
   * @param {HTMLElement} button Tombol yang diklik
   * @param {boolean} isVisible Status baru (true = terlihat)
   */
  const updateButtonUI = (button, isVisible) => {
    if (isVisible) {
      button.innerHTML = '<i class="bi bi-eye-slash me-1"></i> Sembunyikan';
    } else {
      button.innerHTML = '<i class="bi bi-eye me-1"></i> Tampilkan';
    }
    // Simpan state baru ke atribut data
    button.dataset.visibleState = isVisible ? '1' : '0';
  };

/**
   * Mengirim state baru ke database via fetch()
   * @param {string} sectionKey Nama section (mis. 'sertifikasi')
   * @param {number} newState 1 untuk visible, 0 untuk hidden
   */
  const saveStateToDB = async (sectionKey, newState) => {
    
    const formData = new FormData();
    formData.append('section', sectionKey);
    formData.append('is_visible', newState);

    // !! PENTING: Penanganan CSRF !!
    // Kita ambil data dari 'jembatan' (variabel global) yang ada di profile.php
    if (window.CSRF_NAME && window.CSRF_HASH) {
      formData.append(window.CSRF_NAME, window.CSRF_HASH);
    } else {
      console.warn('Variabel CSRF global (window.CSRF_NAME) tidak ditemukan.');
    }

    try {
      // Kita gunakan URL dari 'jembatan' (variabel global)
      const response = await fetch(window.API_URL, { 
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      if (!response.ok) {
        throw new Error(`Server response was not OK: ${response.statusText}`);
      }

      const result = await response.json();

      // (Opsional tapi direkomendasikan) Perbarui hash CSRF untuk request berikutnya
      if (result.new_csrf_hash && window.CSRF_HASH_INPUT) {
         window.CSRF_HASH_INPUT.value = result.new_csrf_hash;
         // Atau jika Anda menyimpannya di var global
         window.CSRF_HASH = result.new_csrf_hash;
      }

      if (!result.success) {
        console.warn('Gagal menyimpan state ke DB:', result.message);
      }
      
    } catch (error) {
      console.error('Error saat menyimpan visibility state:', error);
    }
  };


  // --- Event Listener Utama ---
  allToggleButtons.forEach(button => {
    
    // Inisialisasi saat load (pastikan UI sesuai data-visible-state dari PHP)
    const targetId = button.dataset.target;
    const content = document.querySelector(targetId);
    const isVisible = (button.dataset.visibleState || '1') === '1';

    if (content) {
      content.classList.toggle('d-none', !isVisible);
    }
    updateButtonUI(button, isVisible);


    // Tambahkan event klik
    button.addEventListener('click', (e) => {
      e.preventDefault();
      
      const currentTargetId = button.dataset.target;
      const currentContent = document.querySelector(currentTargetId);
      const sectionKey = button.dataset.storageKey;

      if (!currentContent || !sectionKey) {
        console.warn('Toggle button missing data-target or data-storage-key');
        return;
      }

      // 1. Tentukan state baru
      // Cek apakah content SEKARANG memiliki class d-none
      const isCurrentlyHidden = currentContent.classList.contains('d-none');
      const newIsVisible = isCurrentlyHidden; // Jika tersembunyi, state BARU-nya adalah terlihat
      const newStateForDB = newIsVisible ? 1 : 0; // 1 atau 0

      // 2. Update UI secara instan (Optimistic Update)
      currentContent.classList.toggle('d-none', !newIsVisible);
      updateButtonUI(button, newIsVisible);

      // 3. Kirim ke database
      saveStateToDB(sectionKey, newStateForDB);
    });
  });

});