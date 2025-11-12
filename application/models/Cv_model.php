<?php
// application/models/Cv_model.php

defined('BASEPATH') or exit('No direct script access allowed');

class Cv_model extends CI_Model
{
     public function getByUser($user_id)
    {
        return $this->db->get_where('cv', ['user_id' => $user_id])->row_array();
    }
    public function getCvData($user_id)
    {
        $data = [
            'cv' => [], 'pendidikan_formal' => [], 'pendidikan_nonformal' => [],
            'pengalaman' => [], 'bahasa' => [], 'sertifikasi' => [],
            'project' => [], 'lampiran' => [], 'country_experience' => [], // <-- add
        ];

        // CV utama
        $this->db->where('user_id', $user_id);
        $data['cv'] = $this->db->get('cv')->row_array();
        if (empty($data['cv']['id'])) return $data;

        $cv_id = (int)$data['cv']['id'];

        // lainnya (unchanged)
        $this->db->where('cv_id', $cv_id);
        $data['pendidikan_formal'] = $this->db->get('pendidikan_formal')->result_array();

        $this->db->where('cv_id', $cv_id);
        $data['pendidikan_nonformal'] = $this->db->get('pendidikan_nonformal')->result_array();

        $this->db->where('cv_id', $cv_id);
        $data['pengalaman'] = $this->db->get('pengalaman_kerja')->result_array();

        
        $this->db->where('cv_id', $cv_id);
        $data['bahasa'] = $this->db->get('bahasa')->result_array();

        $this->db->where('cv_id', $cv_id);
        $data['sertifikasi'] = $this->db->get('sertifikasi_profesi')->result_array();

        $this->db->where('cv_id', $cv_id);
        $data['project'] = $this->db->get('project_relevan')->result_array();

        $this->db->where('cv_id', $cv_id);
        $data['lampiran'] = $this->db->get('lampiran_cv')->row_array();

        // 🔥 NEGARA UNIK (tanpa helper): DISTINCT + filter NULL/kosong
        $rows = $this->db->select("DISTINCT TRIM(negara) AS negara", false)
                        ->from('pengalaman_kerja')
                        ->where('cv_id', $cv_id)
                        ->where('negara IS NOT NULL', null, false)
                        ->where("negara <> ''", null, false)
                        ->order_by('negara', 'ASC')
                        ->get()->result_array();
        $data['country_experience'] = array_column($rows, 'negara');
        $data['employment_record'] = $this->buildEmploymentRecord($data['pengalaman']);
        return $data;
    }
    /**
     * Grup per employer (perusahaan_pelaksana > perusahaan > pemberi_pekerjaan)
     * Ambil earliest start & latest end, kumpulin posisi unik.
     * @param array $rows pengalaman_kerja
     * @return array
     */

    private function buildEmploymentRecord(array $rows)
    {
        $grouped = [];

        foreach ($rows as $r) {
            // ✅ hanya pakai pelaksana_proyek
            $employer = trim($r['pelaksana_proyek'] ?? '');

            // kalau kosong, skip (biar gak muncul baris tanpa nama perusahaan)
            if ($employer === '') {
                continue;
            }

            // normalisasi tanggal
            $mulai = !empty($r['waktu_mulai']) ? date('Y-m-d', strtotime($r['waktu_mulai'])) : null;
            $akhirRaw = $r['waktu_akhir'] ?? '';
            $akhir = ($akhirRaw === '' || $akhirRaw === null) ? null : date('Y-m-d', strtotime($akhirRaw));

            if (!isset($grouped[$employer])) {
                $grouped[$employer] = [
                    'employer'  => $employer,
                    'from'      => $mulai,
                    'to'        => $akhir,
                    'positions' => [],
                    'count'     => 0,
                ];
            }

            if ($mulai && (!$grouped[$employer]['from'] || $mulai < $grouped[$employer]['from'])) {
                $grouped[$employer]['from'] = $mulai;
            }
            if ($akhir) {
                if (!$grouped[$employer]['to'] || $akhir > $grouped[$employer]['to']) {
                    $grouped[$employer]['to'] = $akhir;
                }
            } else {
                $grouped[$employer]['to'] = null; // Present
            }

            $pos = trim($r['posisi'] ?? '');
            if ($pos !== '' && !in_array($pos, $grouped[$employer]['positions'], true)) {
                $grouped[$employer]['positions'][] = $pos;
            }

            $grouped[$employer]['count']++;
        }

        $out = array_values($grouped);
        usort($out, function ($a, $b) {
            $toA = $a['to'] ?? '9999-12-31';
            $toB = $b['to'] ?? '9999-12-31';
            if ($toA === $toB) {
                return strcmp($b['from'] ?? '0000-00-00', $a['from'] ?? '0000-00-00');
            }
            return strcmp($toB, $toA);
        });

        return $out;
    }


    public function getCv($user_id) {
        return $this->db->get_where('cv', ['user_id' => $user_id])->row_array();
    }

    public function getPendidikan($cv_id) {
        return $this->db->get_where('pendidikan_formal', ['cv_id' => $cv_id])->result_array();
    }

    public function getPelatihan($cv_id) {
        return $this->db->get_where('pendidikan_nonformal', ['cv_id' => $cv_id])->result_array();
    }

    public function getPengalaman($cv_id) {
        return $this->db->get_where('pengalaman_kerja', ['cv_id' => $cv_id])->result_array();
    }

    public function getProject($cv_id) {
        return $this->db->get_where('project_relevan', ['cv_id' => $cv_id])->result_array();
    }

    public function getBahasa($cv_id) {
        return $this->db->get_where('bahasa', ['cv_id' => $cv_id])->result_array();
    }

    public function getSertifikasi($cv_id) {
        return $this->db->get_where('sertifikasi_profesi', ['cv_id' => $cv_id])->result_array();
    }

    public function uploadFile($field_name, $index = null) {
        $user_id = $this->session->userdata('id');
        $upload_path = './uploads/cv/user_' . $user_id . '/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if ($index !== null) {
            if (empty($_FILES[$field_name]['name'][$index])) return null;

            $_FILES['file']['name']     = $_FILES[$field_name]['name'][$index];
            $_FILES['file']['type']     = $_FILES[$field_name]['type'][$index];
            $_FILES['file']['tmp_name'] = $_FILES[$field_name]['tmp_name'][$index];
            $_FILES['file']['error']    = $_FILES[$field_name]['error'][$index];
            $_FILES['file']['size']     = $_FILES[$field_name]['size'][$index];
        } else {
            if (empty($_FILES[$field_name]['name'])) return null;

            $_FILES['file']['name']     = $_FILES[$field_name]['name'];
            $_FILES['file']['type']     = $_FILES[$field_name]['type'];
            $_FILES['file']['tmp_name'] = $_FILES[$field_name]['tmp_name'];
            $_FILES['file']['error']    = $_FILES[$field_name]['error'];
            $_FILES['file']['size']     = $_FILES[$field_name]['size'];
        }

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = '*';
        $config['max_size']      = 2048;
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload('file')) {
            return 'user_' . $user_id . '/' . $this->upload->data('file_name');
        }

        return null;
    }

    public function saveFileFromField($field_name, $index = null) {
        $file = $this->uploadFile($field_name, $index);
        return $file ? $file : null;
    }

    // Hitung durasi (Y/M/D) dalam Bahasa Indonesia.
    // Input format tanggal: 'YYYY-MM-DD'. Kembalian: "X tahun Y bulan Z hari".
    protected function computeDurationIndo($start, $end)
    {
        if (empty($start) || empty($end)) return '';

        try {
            $d1 = new DateTime($start);
            $d2 = new DateTime($end);
        } catch (Exception $e) {
            return '';
        }

        // kalau end < start, yaudah kosongin (biar ga aneh)
        if ($d2 < $d1) return '';

        $diff = $d1->diff($d2);
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' tahun';
        if ($diff->m > 0) $parts[] = $diff->m . ' bulan';
        // tampilkan hari meski 0 kalau memang semuanya 0
        if ($diff->d > 0 || empty($parts)) $parts[] = $diff->d . ' hari';

        return implode(' ', $parts);
    }


    public function saveOrUpdate() {
        $user_id = $this->session->userdata('id');

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
            'employment_to'       => $this->input->post('employment_to') ?: null, // kosong = masih aktif
            'employer'            => $this->input->post('employer', true),
            'employment_position' => $this->input->post('employment_position', true),
            'employment_desc'     => $this->input->post('employment_desc', true),
            'domisili_negara'     => $this->input->post('domisili_negara', true),
            'domisili_kota'     => $this->input->post('domisili_kota', true),
        ];

        $existing = $this->getByUser($user_id);

        if ($existing) {
            $this->db->where('user_id', $user_id)->update('cv', $cv);
        } else {
            $this->db->insert('cv', $cv);
        }

        $cv_id = $this->getByUser($user_id)['id'];

        // save pendidikan_formal dengan file
        $this->db->delete('pendidikan_formal', ['cv_id' => $cv_id]);
        if ($this->input->post('institusi')) {
            foreach ($this->input->post('institusi') as $i => $ins) {
                $ijazah = $this->saveFileFromField('ijazah_file', $i);
                $this->db->insert('pendidikan_formal', [
                    'cv_id' => $cv_id,
                    'tingkat' => $this->input->post('tingkat')[$i],
                    'institusi' => $ins,
                    'jurusan' => $this->input->post('jurusan')[$i],
                    'tahun_lulus' => $this->input->post('tahun_lulus')[$i],
                    'ijazah_file' => $ijazah
                ]);
            }
        }

        // save pelatihan dengan file
        $this->db->delete('pendidikan_nonformal', ['cv_id' => $cv_id]);
        if ($this->input->post('nama_pelatihan')) {
            foreach ($this->input->post('nama_pelatihan') as $i => $nama) {
                $sertifikat = $this->saveFileFromField('sertifikat_file', $i);
                $this->db->insert('pendidikan_nonformal', [
                    'cv_id' => $cv_id,
                    'nama_pelatihan' => $nama,
                    'penyelenggara' => $this->input->post('penyelenggara')[$i],
                    'tahun' => $this->input->post('tahun_pelatihan')[$i],
                    'sertifikat_file' => $sertifikat
                ]);
            }
        }

        // ===== Pengalaman Kerja =====
        // Pakai kolom BARU: waktu_mulai, waktu_akhir, durasi.
        // Opsional: waktu_legacy (atau fallback ke 'waktu' kalau kolom itu belum ada).
        $this->db->delete('pengalaman_kerja', ['cv_id' => $cv_id]);

        $nama_kegiatan = $this->input->post('nama_kegiatan');
        if (!empty($nama_kegiatan)) {
            $lokasi                 = $this->input->post('lokasi')                ?: [];
            $negara_list            = $this->input->post('negara')                ?: [];
            $pemberi                = $this->input->post('pemberi_pekerjaan')     ?: [];
            $pelaksana_list         = $this->input->post('pelaksana_proyek')      ?: [];
            $perusahaan_peng        = $this->input->post('perusahaan_pengalaman') ?: [];
            $uraian_proyek_list     = $this->input->post('uraian_proyek')         ?: []; // <— PROYEK
            $uraian_tugas_list      = $this->input->post('uraian_tugas')          ?: []; // <— TUGAS
            $tahun_pengalaman_list  = $this->input->post('tahun_pengalaman')      ?: [];
            $waktu_legacy_list      = $this->input->post('waktu')                 ?: [];
            $waktu_mulai_list       = $this->input->post('waktu_mulai')           ?: [];
            $waktu_akhir_list       = $this->input->post('waktu_akhir')           ?: [];
            $posisi_list            = $this->input->post('posisi_pengalaman')     ?: [];
            $status_peg_list        = $this->input->post('status_pegawai')        ?: [];

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

                $ref = $this->saveFileFromField('referensi_file', $i);

                $row = [
                    'cv_id'              => $cv_id,
                    'tahun'              => $tahun_pengalaman,
                    'nama_kegiatan'      => $nama,
                    'lokasi'             => $lokasi[$i]           ?? null,
                    'negara'             => $negara_list[$i]      ?? null,
                    'pemberi_pekerjaan'  => $pemberi[$i]          ?? null,
                    'perusahaan'         => $perusahaan_peng[$i]  ?? null,   // legacy
                    'pelaksana_proyek'   => $pelaksana_list[$i]   ?? null,   // utama
                    'uraian_proyek'      => $uraian_proyek_list[$i] ?? null, // <— SIMPAN TERPISAH
                    'uraian_tugas'       => $uraian_tugas_list[$i]  ?? null, // <— SIMPAN TERPISAH
                    'waktu_mulai'        => $w_mulai,
                    'waktu_akhir'        => $w_akhir,
                    'durasi'             => $durasi,
                    'posisi'             => $posisi_list[$i]      ?? null,
                    'status_kepegawaian' => $status_peg_list[$i]  ?? null,
                    'referensi_file'     => $ref,
                    'created_at'         => date('Y-m-d H:i:s'),
                    'updated_at'         => date('Y-m-d H:i:s'),
                ];

                if ($legacyTimeCol) $row[$legacyTimeCol] = $legacyTimeVal;

                $this->db->insert('pengalaman_kerja', $row);
            }
        }

        // Simpan Sertifikasi Profesi
        $this->db->delete('sertifikasi_profesi', ['cv_id' => $cv_id]);
        if ($this->input->post('sertifikasi_nama')) {
            foreach ($this->input->post('sertifikasi_nama') as $i => $nama) {
                $this->db->insert('sertifikasi_profesi', [
                    'cv_id' => $cv_id,
                    'nama' => $nama,
                    'penerbit' => $this->input->post('sertifikasi_penerbit')[$i],
                    'tahun' => $this->input->post('sertifikasi_tahun')[$i]
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

        // Upload file baru (kalau ada)
        $ktp_file = $this->saveFileFromField('ktp_file');
        $npwp_file = $this->saveFileFromField('npwp_file');
        $bukti_pajak = $this->saveFileFromField('bukti_pajak');
        $foto = $this->saveFileFromField('foto');
        $lainnya = $this->saveFileFromField('lainnya');

        // Kalau tidak upload baru, pakai file lama
        $dataLampiran = [
            'cv_id' => $cv_id,
            'ktp_file' => $ktp_file ?: ($existingLampiran['ktp_file'] ?? null),
            'npwp_file' => $npwp_file ?: ($existingLampiran['npwp_file'] ?? null),
            'bukti_pajak' => $bukti_pajak ?: ($existingLampiran['bukti_pajak'] ?? null),
            'foto' => $foto ?: ($existingLampiran['foto'] ?? null),
            'lainnya' => $lainnya ?: ($existingLampiran['lainnya'] ?? null)
        ];

        // Simpan (update atau insert)
        if ($existingLampiran) {
            $this->db->where('cv_id', $cv_id)->update('lampiran_cv', $dataLampiran);
        } else {
            $this->db->insert('lampiran_cv', $dataLampiran);
        }
    }
}
