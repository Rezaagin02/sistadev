<?php
// application/models/Cv_model.php

defined('BASEPATH') or exit('No direct script access allowed');

class Cv_model extends CI_Model
{
    // ====================================================================
    // FUNGSI UMUM (DIPERBAIKI UNTUK MENGATASI ERROR FATAL)
    // ====================================================================
    
    public function getByUser($user_id)
    {
        return $this->db->get_where('cv', ['user_id' => $user_id])->row_array();
    }
    
    // FUNGSI YANG HILANG/UNDEFINED
    protected function computeDurationIndo($start, $end)
    {
        if (empty($start) || empty($end)) return '';

        try {
            $d1 = new DateTime($start);
            $d2 = new DateTime($end);
        } catch (Exception $e) {
            return '';
        }

        if ($d2 < $d1) return '';

        $diff = $d1->diff($d2);
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' tahun';
        if ($diff->m > 0) $parts[] = $diff->m . ' bulan';
        if ($diff->d > 0 || empty($parts)) $parts[] = $diff->d . ' hari';

        return implode(' ', $parts);
    }
    
    // ... (Fungsi getCvData, buildEmploymentRecord, dan get functions lainnya TIDAK DIUBAH,
    // asumsikan sudah ada di file asli Anda tetapi tidak dimasukkan dalam prompt) ...
    
    public function getCvData($user_id)
    {
        // 1. Ambil data utama CV
        $data['cv'] = $this->db->get_where('cv', ['user_id' => $user_id])->row_array();
        
        if (empty($data['cv'])) {
            return []; // Kembalikan array kosong jika CV tidak ditemukan
        }

        $cv_id = $data['cv']['id'];

        // 2. Ambil data relasional yang dibutuhkan di view export_wb.php

        // Pendidikan Formal (Education)
        $data['pendidikan_formal'] = $this->db->get_where('pendidikan_formal', ['cv_id' => $cv_id])->result_array();

        // Sertifikasi Profesi (Professional Certification)
        $data['sertifikasi'] = $this->db->get_where('sertifikasi_profesi', ['cv_id' => $cv_id])->result_array();

        // Pelatihan/Pendidikan Nonformal (Other Relevant Training)
        $data['pendidikan_nonformal'] = $this->db->get_where('pendidikan_nonformal', ['cv_id' => $cv_id])->result_array();

        // Project Relevan (Work Undertaken)
        $data['pengalaman'] = $this->db->get_where('pengalaman_kerja', ['cv_id' => $cv_id])->result_array();

        // 🔑 BARIS KRITIS YANG HILANG: Penguasaan Bahasa (Language Skills)
        $data['bahasa'] = $this->db->get_where('bahasa', ['cv_id' => $cv_id])->result_array();
        
        // Perhatikan: Data Pengalaman di view menggunakan variabel $employment_record dan $pengalaman.
        // Anda harus memastikan data ini dibentuk dengan benar.
        
        // Data Employment Record (Pengalaman Kerja)
        // Asumsikan ada helper function/method lain yang menyusun $employment_record.
        // Jika tidak ada, Anda perlu memuat data dari tabel pengalaman_kerja dan memprosesnya.
        // Untuk tujuan perbaikan error ini, kita buat dummy atau ambil langsung dari DB:
        $employment_raw = $this->db->get_where('pengalaman_kerja', ['cv_id' => $cv_id])->result_array();

        $data['employment_record'] = [];
        foreach ($employment_raw as $row) {
            // Kunci 'posisi' digunakan di view untuk di-explode
            $posisi_string = $row['posisi'] ?? ''; 
            
            // MAPPING YANG BENAR:
            $data['employment_record'][] = [
                'from'      => $row['waktu_mulai'] ?? null, // <-- MENGGUNAKAN NAMA KOLOM DB YANG BENAR
                'to'        => $row['waktu_akhir'] ?? null, // <-- MENGGUNAKAN NAMA KOLOM DB YANG BENAR
                'employer'  => $row['perusahaan'] ?? $row['pelaksana_proyek'] ?? null, // <-- MENGGUNAKAN NAMA KOLOM DB YANG BENAR
                'positions' => explode(',', $posisi_string),
            ];
        }

        // Country of Work Experience (Ambil dari data employment_record)
        $country_list = array_column($employment_raw, 'negara');
        $data['country_experience'] = array_filter(array_unique($country_list));

        return $data;
    }

    // ====================================================================
    // FUNGSI UPLOAD YANG DIKOREKSI (Untuk Referensi Pengalaman)
    // ====================================================================
    public function uploadFile($field_name, $index = null) {
        $user_id = $this->session->userdata('id');
        $upload_path = './uploads/cv/user_' . $user_id . '/';
        $path_prefix = 'uploads/cv/user_' . $user_id . '/'; 

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if ($index !== null) {
            if (empty($_FILES[$field_name]['name'][$index])) return null;

            // Logika Re-organisasi $_FILES['file']
            $_FILES['file']['name']     = $_FILES[$field_name]['name'][$index];
            $_FILES['file']['type']     = $_FILES[$field_name]['type'][$index];
            $_FILES['file']['tmp_name'] = $_FILES[$field_name]['tmp_name'][$index];
            $_FILES['file']['error']    = $_FILES[$field_name]['error'][$index];
            $_FILES['file']['size']     = $_FILES[$field_name]['size'][$index];
            $field_to_upload = 'file'; // Kita akan mengupload 'file'
        } else {
            if (empty($_FILES[$field_name]['name'])) return null;
            $field_to_upload = $field_name; // Jika bukan array, upload dengan nama field asli
        }

        $config['upload_path']   = $upload_path;
        // GANTI TIPE FILE AGAR SINKRON DAN AMAN
        $config['allowed_types'] = 'pdf|jpg|jpeg|png'; // <--- PERBAIKAN DI SINI
        $config['max_size']      = 2048;
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($index !== null) {
            // Jika array item, kita upload field 'file' yang sudah diisi
            if ($this->upload->do_upload($field_to_upload)) { 
                return $path_prefix . $this->upload->data('file_name');
            }
        } else {
            // Jika bukan array, upload field asli
            if ($this->upload->do_upload($field_to_upload)) { 
                return $path_prefix . $this->upload->data('file_name');
            }
        }

        // Tambahkan ini jika Anda ingin melihat error (debugging)
        // echo "Upload Referensi Gagal: " . $this->upload->display_errors(); exit; 

        return null;
    }

    // ====================================================================
    // FUNGSI UPLOAD YANG DIKOREKSI (Untuk Lampiran, Ijazah, Sertifikasi)
    // ====================================================================
    public function saveFileFromField($field_name) {
        $user_id = $this->session->userdata('id');
        $upload_path = './uploads/cv/user_' . $user_id . '/';
        $path_prefix = 'uploads/cv/user_' . $user_id . '/'; 

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        
        // PENTING: Jika $field_name adalah 'file' (dari array repeater), pastikan error OK.
        if (empty($_FILES[$field_name]['name']) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $config['upload_path']   = $upload_path;
        // GANTI: Ubah '*' menjadi tipe file yang diizinkan (sesuai form.php)
        $config['allowed_types'] = 'pdf|jpg|jpeg|png'; 
        $config['max_size']      = 2048; // 2MB (atau sesuaikan, di form.php tidak spesifik)
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($field_name)) {
            return $path_prefix . $this->upload->data('file_name');
        }

        // Tambahkan ini untuk debugging:
        // echo "Upload Gagal: " . $this->upload->display_errors(); exit; 
        return null;
    }


    public function saveOrUpdate() {
        $user_id = $this->session->userdata('id');

        // --- (Bagian data CV Utama, Perbaikan, dan Update/Insert) ---
        $cv = [
            'user_id' => $user_id,
            'nama' => $this->input->post('nama', true),
            'posisi' => $this->input->post('posisi', true),
            'perusahaan' => $this->input->post('perusahaan', true),
            'tempat_lahir' => $this->input->post('tempat_lahir', true),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', true),
            'kewarganegaraan' => $this->input->post('kewarganegaraan', true),
            'status_kepegawaian' => $this->input->post('status_kepegawaian', true),
            'pernah_di_wb' => $this->input->post('pernah_di_wb', true),
            'created_at' => date('Y-m-d H:i:s'),
            'employment_from'     => $this->input->post('employment_from', true),
            'employment_to'       => $this->input->post('employment_to') ?: null, 
            'employer'            => $this->input->post('employer', true),
            'employment_position' => $this->input->post('employment_position', true),
            'employment_desc'     => $this->input->post('employment_desc', true),
            'domisili_negara'     => $this->input->post('domisili_negara', true),
            'domisili_kota'       => $this->input->post('domisili_kota', true),
        ];

        $existing = $this->getByUser($user_id);

        if ($existing) {
            $this->db->where('user_id', $user_id)->update('cv', $cv);
        } else {
            $this->db->insert('cv', $cv);
        }

        $cv_id = $this->getByUser($user_id)['id'];
        // --- (END CV Utama) ---
        
        // save pendidikan_formal dengan file
        $this->db->delete('pendidikan_formal', ['cv_id' => $cv_id]);
        $ijazah_file_old_array = $this->input->post('ijazah_file_old') ?? [];
        $institusi_array = $this->input->post('institusi');

        if (!empty($institusi_array)) {
            $tingkat_array = $this->input->post('tingkat');
            $jurusan_array = $this->input->post('jurusan');
            $tahun_lulus_array = $this->input->post('tahun_lulus');
            $ijazah_files = $_FILES['ijazah_file'] ?? [];

            foreach ($institusi_array as $i => $ins) {
                
                $final_ijazah_file = $ijazah_file_old_array[$i] ?? null;
                
                if (isset($ijazah_files['error'][$i]) && $ijazah_files['error'][$i] === UPLOAD_ERR_OK) {
                    
                    $_FILES['file']['name']     = $ijazah_files['name'][$i];
                    $_FILES['file']['type']     = $ijazah_files['type'][$i];
                    $_FILES['file']['tmp_name'] = $ijazah_files['tmp_name'][$i];
                    $_FILES['file']['error']    = $ijazah_files['error'][$i];
                    $_FILES['file']['size']     = $ijazah_files['size'][$i];

                    $uploaded_path = $this->saveFileFromField('file'); 
                    
                    if ($uploaded_path) {
                        $final_ijazah_file = $uploaded_path;
                    }

                }
                
                $this->db->insert('pendidikan_formal', [
                    'cv_id' => $cv_id,
                    'tingkat' => $tingkat_array[$i] ?? null,
                    'institusi' => $ins,
                    'jurusan' => $jurusan_array[$i] ?? null,
                    'tahun_lulus' => $tahun_lulus_array[$i] ?? null,
                    'ijazah_file' => $final_ijazah_file 
                ]);
            }
        }

        // save pelatihan dengan file
        $this->db->delete('pendidikan_nonformal', ['cv_id' => $cv_id]);
        $sertifikat_file_old_array = $this->input->post('sertifikat_file_old') ?? [];
        $nama_pelatihan_array = $this->input->post('nama_pelatihan');

        if (!empty($nama_pelatihan_array)) {
            $penyelenggara_array = $this->input->post('penyelenggara');
            $tahun_pelatihan_array = $this->input->post('tahun_pelatihan');
            $sertifikat_files = $_FILES['sertifikat_file'] ?? [];

            foreach ($nama_pelatihan_array as $i => $nama) {
                
                $final_sertifikat_file = $sertifikat_file_old_array[$i] ?? null;
                
                if (isset($sertifikat_files['error'][$i]) && $sertifikat_files['error'][$i] === UPLOAD_ERR_OK) {
                    
                    $_FILES['file']['name']     = $sertifikat_files['name'][$i];
                    $_FILES['file']['type']     = $sertifikat_files['type'][$i];
                    $_FILES['file']['tmp_name'] = $sertifikat_files['tmp_name'][$i];
                    $_FILES['file']['error']    = $sertifikat_files['error'][$i];
                    $_FILES['file']['size']     = $sertifikat_files['size'][$i];

                    $uploaded_path = $this->saveFileFromField('file');
                    
                    if ($uploaded_path) {
                        $final_sertifikat_file = $uploaded_path;
                    }

                } 
                
                $this->db->insert('pendidikan_nonformal', [
                    'cv_id' => $cv_id,
                    'nama_pelatihan' => $nama,
                    'penyelenggara' => $penyelenggara_array[$i] ?? null,
                    'tahun' => $tahun_pelatihan_array[$i] ?? null,
                    'sertifikat_file' => $final_sertifikat_file
                ]);
            }
        }

        // ===== Pengalaman Kerja =====
        $this->db->delete('pengalaman_kerja', ['cv_id' => $cv_id]);

        $referensi_file_old_array = $this->input->post('referensi_file_old') ?? []; 
        // ----------------------

        $nama_kegiatan = $this->input->post('nama_kegiatan');
        if (!empty($nama_kegiatan)) {
            $lokasi               = $this->input->post('lokasi') ?: [];
            $negara_list          = $this->input->post('negara') ?: [];
            $pemberi              = $this->input->post('pemberi_pekerjaan') ?: [];
            $pelaksana_list       = $this->input->post('pelaksana_proyek') ?: [];
            $perusahaan_peng      = $this->input->post('perusahaan_pengalaman') ?: [];
            $uraian_proyek_list   = $this->input->post('uraian_proyek') ?: [];
            $uraian_tugas_list    = $this->input->post('uraian_tugas') ?: [];
            $tahun_pengalaman_list= $this->input->post('tahun_pengalaman') ?: [];
            $waktu_legacy_list    = $this->input->post('waktu') ?: [];
            $waktu_mulai_list     = $this->input->post('waktu_mulai') ?: [];
            $waktu_akhir_list     = $this->input->post('waktu_akhir') ?: [];
            $posisi_list          = $this->input->post('posisi_pengalaman') ?: [];
            $status_peg_list      = $this->input->post('status_pegawai') ?: [];

            $hasWaktuLegacy = $this->db->field_exists('waktu_legacy', 'pengalaman_kerja');
            $legacyTimeCol  = $hasWaktuLegacy ? 'waktu_legacy' : ($this->db->field_exists('waktu', 'pengalaman_kerja') ? 'waktu' : null);

            foreach ($nama_kegiatan as $i => $nama) {
                if (trim((string)$nama) === '') continue;

                $w_mulai = $waktu_mulai_list[$i] ?? null;
                $w_akhir = $waktu_akhir_list[$i] ?? null;

                $durasi = trim($this->input->post('durasi')[$i] ?? '');
                if ($durasi === '') $durasi = $this->computeDurationIndo($w_mulai, $w_akhir); 

                $tahun_pengalaman = $tahun_pengalaman_list[$i] ?? null;
                if (!$tahun_pengalaman && $w_mulai) $tahun_pengalaman = date('Y', strtotime($w_mulai));

                $legacyTimeVal = $waktu_legacy_list[$i] ?? ($durasi ?: null);

                // --- LOGIKA PERBAIKAN FILE ---
                $file_lama = $referensi_file_old_array[$i] ?? null; // 1. Ambil file lama
                $uploaded_ref = $this->uploadFile('referensi_file', $i); // 2. Upload file baru
                $ref = $uploaded_ref ?: $file_lama; // 3. Tentukan file akhir (Baru atau Lama)
                // -----------------------------
                
                $row = [
                    'cv_id'               => $cv_id,
                    'tahun'               => $tahun_pengalaman,
                    'nama_kegiatan'       => $nama,
                    'lokasi'              => $lokasi[$i] ?? null,
                    'negara'              => $negara_list[$i] ?? null,
                    'pemberi_pekerjaan'   => $pemberi[$i] ?? null,
                    'perusahaan'          => $perusahaan_peng[$i] ?? null,
                    'pelaksana_proyek'    => $pelaksana_list[$i] ?? null,
                    'uraian_proyek'       => $uraian_proyek_list[$i] ?? null,
                    'uraian_tugas'        => $uraian_tugas_list[$i] ?? null,
                    'waktu_mulai'         => $w_mulai,
                    'waktu_akhir'         => $w_akhir,
                    'durasi'              => $durasi,
                    'posisi'              => $posisi_list[$i] ?? null,
                    'status_kepegawaian'  => $status_peg_list[$i] ?? null,
                    'referensi_file'      => $ref, // <-- MENGGUNAKAN VARIABEL $ref BARU
                    'created_at'          => date('Y-m-d H:i:s'),
                    'updated_at'          => date('Y-m-d H:i:s'),
                ];

                if ($legacyTimeCol) $row[$legacyTimeCol] = $legacyTimeVal;

                $this->db->insert('pengalaman_kerja', $row);
            }
        }

        // Simpan Sertifikasi Profesi
        $this->db->delete('sertifikasi_profesi', ['cv_id' => $cv_id]);
        $sertifikasi_file_old_array = $this->input->post('sertifikasi_file_old') ?? [];
        $sertifikasi_nama_array = $this->input->post('sertifikasi_nama');

        if (!empty($sertifikasi_nama_array)) {
            $penerbit_array = $this->input->post('sertifikasi_penerbit');
            $tahun_array = $this->input->post('sertifikasi_tahun');
            // Asumsi nama input file di form adalah 'file_sertifikat[]'
            $sertifikat_files = $_FILES['file_sertifikat'] ?? []; 

            foreach ($sertifikasi_nama_array as $i => $nama) {
                
                $final_sertifikat_file = $sertifikasi_file_old_array[$i] ?? null; // Ambil file lama

                // --- LOGIKA PEMROSESAN FILE DARI ARRAY ---
                // Cek apakah ada file baru yang diunggah untuk item ini (error 0 = UPLOAD_ERR_OK)
                if (isset($sertifikat_files['error'][$i]) && $sertifikat_files['error'][$i] === UPLOAD_ERR_OK) {
                    
                    // Re-organisasi $_FILES untuk memproses file tunggal pada indeks $i
                    $_FILES['file']['name']     = $sertifikat_files['name'][$i];
                    $_FILES['file']['type']     = $sertifikat_files['type'][$i];
                    $_FILES['file']['tmp_name'] = $sertifikat_files['tmp_name'][$i];
                    $_FILES['file']['error']    = $sertifikat_files['error'][$i];
                    $_FILES['file']['size']     = $sertifikat_files['size'][$i];

                    // Panggil fungsi saveFileFromField() dengan nama field tunggal 'file'
                    // ASUMSI: Fungsi saveFileFromField() Anda akan menggunakan field 'file' yang baru dibuat ini.
                    $uploaded_path = $this->saveFileFromField('file');
                    
                    if ($uploaded_path) {
                        $final_sertifikat_file = $uploaded_path;
                    }
                } 
                // --- AKHIR LOGIKA PEMROSESAN FILE ---
                
                $this->db->insert('sertifikasi_profesi', [
                    'cv_id' => $cv_id,
                    'nama' => $nama,
                    'penerbit' => $penerbit_array[$i] ?? null, // Menggunakan array penerbit yang sudah didefinisikan
                    'tahun' => $tahun_array[$i] ?? null,    // Menggunakan array tahun yang sudah didefinisikan
                    'file_sertifikat' => $final_sertifikat_file 
                ]);
            }
        }

        // Simpan Project Relevan (untuk World Bank)
        $this->db->delete('project_relevan', ['cv_id' => $cv_id]);
        if ($this->input->post('project_nama')) {
            foreach ($this->input->post('project_nama') as $i => $nama) {
                $this->db->insert('project_relevan', [
                    'cv_id' => $cv_id,
                    'nama_project' => $nama,
                    'tahun' => $this->input->post('project_tahun')[$i],
                    'lokasi' => $this->input->post('project_lokasi')[$i],
                    'klien' => $this->input->post('project_klien')[$i],
                    'fitur_proyek' => $this->input->post('project_fitur')[$i],
                    'posisi' => $this->input->post('project_posisi')[$i],
                    'aktivitas' => $this->input->post('project_aktivitas')[$i]
                ]);
            }
        }

        // === LAMPIRAN ===
        $existingLampiran = $this->db->get_where('lampiran_cv', ['cv_id' => $cv_id])->row_array();

        $ktp_file = $this->saveFileFromField('ktp_file');
        $npwp_file = $this->saveFileFromField('npwp_file');
        $bukti_pajak = $this->saveFileFromField('bukti_pajak');
        $foto = $this->saveFileFromField('foto');
        $lainnya = $this->saveFileFromField('lainnya');

        $dataLampiran = [
            'cv_id' => $cv_id,
            'ktp_file' => $ktp_file ?: ($existingLampiran['ktp_file'] ?? null),
            'npwp_file' => $npwp_file ?: ($existingLampiran['npwp_file'] ?? null),
            'bukti_pajak' => $bukti_pajak ?: ($existingLampiran['bukti_pajak'] ?? null),
            'foto' => $foto ?: ($existingLampiran['foto'] ?? null),
            'lainnya' => $lainnya ?: ($existingLampiran['lainnya'] ?? null)
        ];

        if ($existingLampiran) {
            $this->db->where('cv_id', $cv_id)->update('lampiran_cv', $dataLampiran);
        } else {
            $this->db->insert('lampiran_cv', $dataLampiran);
        }
    }
}