<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Hitung progress CV berbasis skema:
 * - user            : id, name, email, domisili_kota/domilisi_negara, linkedin/instagram
 * - cv              : id, user_id, nama, posisi, perusahaan, domisili_kota/domilisi_negara, dll
 * - pendidikan_formal, pendidikan_nonformal, pengalaman_kerja, sertifikasi_profesi, bahasa, lampiran_cv (cv_id)
 *
 * Bobot (total 100):
 * - Profil Dasar (user+cv)     : 20
 * - Pendidikan Formal          : 15
 * - Pelatihan/Nonformal        : 10
 * - Pengalaman Kerja           : 25
 * - Sertifikasi Profesi        : 10
 * - Bahasa                     : 10
 * - Lampiran (file apa aja)    : 10
 */
if (!function_exists('cv_compute_progress')) {
  function cv_compute_progress(CI_DB_query_builder $db, $user_id) {
    $user_id = (int)$user_id;

    // Ambil user
    $user = $db->select('id,name,email,domisili_kota,domisili_negara,linkedin,instagram')
               ->from('user')->where('id', $user_id)->get()->row_array();

    // Ambil CV by user_id (assume 1:1). Kalau 1 user bisa punya banyak CV, pilih terbaru.
    $cv = $db->from('cv')->where('user_id', $user_id)
             ->order_by('updated_at', 'DESC')->order_by('id', 'DESC')->limit(1)
             ->get()->row_array();
    $cv_id = $cv['id'] ?? 0;

    $score = 0;
    $missing = [];

    // ===== 1) Profil Dasar (20) =====
    // Syarat minimal: ada CV, ada nama/posisi, ada domisili (ambil dari cv atau user), ada kontak (email user)
    $nama_ok    = !empty($cv['nama'] ?? $user['name'] ?? '');
    $posisi_ok  = !empty($cv['posisi'] ?? '');
    $dom_ok     = !empty($cv['domisili_kota'] ?? $user['domisili_kota'] ?? '')
                  || !empty($cv['domisili_negara'] ?? $user['domisili_negara'] ?? '');
    $kontak_ok  = !empty($user['email'] ?? '');
    $profil_ok  = (!empty($cv_id) && $nama_ok && $posisi_ok && $dom_ok && $kontak_ok);

    if ($profil_ok) { $score += 20; } else { $missing[] = 'Profil Dasar'; }

    // Helper kecil hitung count by cv_id
    $count_by = function($table) use ($db, $cv_id) {
      if (!$cv_id) return 0;
      return (int)$db->where('cv_id', $cv_id)->count_all_results($table);
    };

    // ===== 2) Pendidikan Formal (15) =====
    if ($count_by('pendidikan_formal') > 0) { $score += 15; } else { $missing[] = 'Pendidikan Formal'; }

    // ===== 3) Pelatihan / Pendidikan Nonformal (10) =====
    if ($count_by('pendidikan_nonformal') > 0) { $score += 10; } else { $missing[] = 'Pelatihan/Nonformal'; }

    // ===== 4) Pengalaman Kerja (25) =====
    if ($count_by('pengalaman_kerja') > 0) { $score += 25; } else { $missing[] = 'Pengalaman Kerja'; }

    // ===== 5) Sertifikasi Profesi (10) =====
    if ($count_by('sertifikasi_profesi') > 0) { $score += 10; } else { $missing[] = 'Sertifikasi Profesi'; }

    // ===== 6) Bahasa (10) =====
    if ($count_by('bahasa') > 0) { $score += 10; } else { $missing[] = 'Bahasa'; }

    // ===== 7) Lampiran (10) =====
    $lampiran_ok = false;
    if ($cv_id) {
      $lamp = $db->select('ktp_file,npwp_file,bukti_pajak,foto,lainnya')
                 ->from('lampiran_cv')->where('cv_id', $cv_id)->get()->row_array();
      if (!empty($lamp)) {
        foreach (['ktp_file','npwp_file','bukti_pajak','foto','lainnya'] as $k) {
          if (!empty($lamp[$k])) { $lampiran_ok = true; break; }
        }
      }
    }
    if ($lampiran_ok) { $score += 10; } else { $missing[] = 'Lampiran'; }

    // Clamp 0..100
    $score = max(0, min(100, (int)$score));

    return [$score, $missing];
  }
}
