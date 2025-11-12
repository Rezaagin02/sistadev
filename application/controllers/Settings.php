<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session']);
        $this->load->helper(['url', 'html']); // html_escape
        $this->load->database();

        // Guard login sederhana
        if (!$this->session->userdata('id')) {
            $this->session->set_flashdata('alert_type','info');
            $this->session->set_flashdata('alert_msg','Silakan login dulu ya.');
            redirect('auth');
        }

        // Ambil data user
        $this->user = $this->db->get_where('user', [
            'id' => (int)$this->session->userdata('id')
        ])->row_array();
    }

    /** GET /settings */
    public function index()
    {
        $data = [
            'title'      => 'Pengaturan',
            'user'       => $this->user,
            'breadcrumb' => [
                ['label'=>'Pengaturan','url'=>null],
            ],
        ];
        $this->load->view('templates/header',  $data);
        $this->load->view('templates/topbar',  $data);
        $this->load->view('settings/index',    $data);
        $this->load->view('templates/footer',  $data);
    }

    /** GET /settings/email */
    public function email()
    {
        $data = [
            'title'      => 'Ganti Email',
            'user'       => $this->user,
            'breadcrumb' => [
                ['label'=>'Pengaturan','url'=>base_url('settings')],
                ['label'=>'Ganti Email','url'=>null],
            ],
        ];
        $this->load->view('templates/header',  $data);
        $this->load->view('templates/topbar',  $data);
        $this->load->view('settings/change_email', $data);
        $this->load->view('templates/footer',  $data);
    }

    /** GET /settings/password */
    public function password()
    {
        $data = [
            'title'      => 'Ganti Password',
            'user'       => $this->user,
            'breadcrumb' => [
                ['label'=>'Pengaturan','url'=>base_url('settings')],
                ['label'=>'Ganti Password','url'=>null],
            ],
        ];
        $this->load->view('templates/header',  $data);
        $this->load->view('templates/topbar',  $data);
        $this->load->view('settings/change_password', $data);
        $this->load->view('templates/footer',  $data);
    }

    // application/controllers/Settings.php (potongan)
    public function change_password()
    {
        $this->load->library(['session','form_validation']);
        $this->load->helper(['url','security','html']);
        $this->load->database();

        // Guard login
        if (!$this->session->userdata('id')) {
            $this->_flash('info','Silakan login dulu ya.');
            redirect('auth');
        }

        // Ambil user
        $userId = (int)$this->session->userdata('id');
        $this->user = $this->db->get_where('user', ['id'=>$userId])->row_array();
        if (!$this->user) { $this->session->sess_destroy(); redirect('auth'); }

        // GET? Biasanya single-page kamu nggak ke sini pakai GET, tapi biarin aman
        if ($this->input->method() !== 'post') {
            redirect('settings#password');
        }

        // ---- Throttle sederhana per session ----
        $now   = time();
        $last  = (int)($this->session->userdata('cpw_last') ?? 0);
        $tries = (int)($this->session->userdata('cpw_tries') ?? 0);
        if ($last && ($now - $last) < 10 && $tries >= 3) { // >3 attempt dalam 10 detik
            $this->_flash('warning','Terlalu banyak percobaan. Coba lagi sebentar.');
            redirect('settings#password');
        }

        // ---- Validasi input dasar ----
        $this->form_validation->set_rules('current_password','Password Saat Ini','required|trim|min_length[6]');
        $this->form_validation->set_rules('new_password','Password Baru','required|trim|min_length[8]');
        $this->form_validation->set_rules('confirm_password','Konfirmasi Password','required|trim|matches[new_password]');

        if (!$this->form_validation->run()) {
            $this->_flash('error', strip_tags(validation_errors("\n")));
            $this->_bumpThrottle($now, $tries);
            redirect('settings#password');
        }

        $current = (string)$this->input->post('current_password', true);
        $new     = (string)$this->input->post('new_password', true);

        // ---- Verifikasi password saat ini (dukung legacy hash opsional) ----
        if (!$this->_verify_password($current, (string)$this->user['password'])) {
            $this->_flash('error','Password saat ini salah.');
            $this->_bumpThrottle($now, $tries);
            redirect('settings#password');
        }

        // ---- Tidak boleh sama dgn current ----
        if (hash_equals($this->_hash_preview($current), $this->_hash_preview($new))) {
            $this->_flash('warning','Password baru tidak boleh sama dengan password saat ini.');
            $this->_bumpThrottle($now, $tries);
            redirect('settings#password');
        }

        // ---- Cek policy (min 8; passphrase ≥14 boleh tanpa komposisi; 3/4 class; no common; no identity; no sequence) ----
        $policyErr = $this->_password_policy_check($new, $this->user);
        if ($policyErr !== true) {
            $this->_flash('warning', $policyErr);
            $this->_bumpThrottle($now, $tries);
            redirect('settings#password');
        }

        // ---- Cek reuse (5 password terakhir + current) ----
        if ($this->_is_reused_password($userId, $new)) {
            $this->_flash('warning','Password baru tidak boleh sama dengan 5 password terakhir.');
            $this->_bumpThrottle($now, $tries);
            redirect('settings#password');
        }

        // ---- Hash & update ----
        // // kalau server support, boleh ganti ke: PASSWORD_ARGON2ID
        $newHash = password_hash($new, PASSWORD_DEFAULT);

        $this->db->trans_begin();

        // simpan history
        $this->db->insert('password_history', [
            'user_id'       => $userId,
            'password_hash' => $newHash,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        // keep 5 terbaru (hapus yang lebih lama)
        $this->db->query("
            DELETE ph FROM password_history ph
            JOIN (
            SELECT id FROM password_history
            WHERE user_id=?
            ORDER BY created_at DESC, id DESC
            LIMIT 18446744073709551615 OFFSET 5
            ) old ON old.id = ph.id
        ", [$userId]);

        // update user
        $this->db->where('id', $userId)->update('user', [
            'password'         => $newHash,
            'updated_at'       => date('Y-m-d H:i:s'),
            'password_version' => (int)($this->user['password_version'] ?? 0) + 1
        ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->_flash('error','Terjadi masalah saat menyimpan. Coba lagi.');
            redirect('settings#password');
        }

        $this->db->trans_commit();

        // reset throttle
        $this->session->unset_userdata(['cpw_last','cpw_tries']);

        $this->_flash('success','Password berhasil diubah.');
        redirect('settings#password');
    }

    /* ==================== Helpers di controller yang sama ==================== */

    // Flash helper (pakai modal alert lo)
    private function _flash($type, $msg){
        $this->session->set_flashdata('alert_type', $type);
        $this->session->set_flashdata('alert_msg',  $msg);
    }
    // Throttle bump
    private function _bumpThrottle($now, $tries){
        $this->session->set_userdata('cpw_last',  $now);
        $this->session->set_userdata('cpw_tries', $tries + 1);
    }

    // Verify hash (support legacy opsional)
    private function _verify_password($plain, $hash)
    {
        if (!$hash) return false;
        if (password_verify($plain, $hash)) return true;
        // legacy fallback (hapus kalau ga dipakai)
        if (strlen($hash) === 40 && sha1($plain) === $hash) return true;
        if (strlen($hash) === 32 && md5($plain)  === $hash) return true;
        return false;
    }

    // preview hash ringan buat compare cepat (bukan buat simpan)
    private function _hash_preview($plain){ return hash('sha256', $plain); }

    /** Policy: panjang ≥8; passphrase ≥14 bebas komposisi; 3/4 class; no common; no identity; no sequence */
    private function _password_policy_check(string $pwd, array $user)
    {
        $len = mb_strlen($pwd, 'UTF-8');
        if ($len < 8) return 'Password minimal 8 karakter. Kamu juga bisa pakai passphrase (≥14 karakter).';

        $isPassphrase = ($len >= 14);

        $hasLower = preg_match('/[a-z]/', $pwd);
        $hasUpper = preg_match('/[A-Z]/', $pwd);
        $hasDigit = preg_match('/\d/',    $pwd);
        $hasSym   = preg_match('/[^A-Za-z0-9]/', $pwd);
        $classes  = $hasLower + $hasUpper + $hasDigit + $hasSym;

        if (!$isPassphrase && $classes < 3) {
            return 'Gunakan kombinasi minimal 3 dari 4: huruf kecil, huruf besar, angka, dan simbol (atau pakai passphrase ≥14 karakter).';
        }

        // Jangan mengandung identitas
        $name  = strtolower(trim((string)($user['name'] ?? '')));
        $email = strtolower(trim((string)($user['email'] ?? '')));
        $local = strtok($email, '@') ?: '';
        $low   = strtolower($pwd);

        $nameParts = array_filter(preg_split('/\s+/', $name), fn($x)=>mb_strlen($x) >= 3);
        foreach ($nameParts as $np) {
            if ($np && strpos($low, $np) !== false) {
                return 'Password tidak boleh mengandung nama kamu.';
            }
        }
        if ($local && mb_strlen($local) >= 3 && strpos($low, $local) !== false) {
            return 'Password tidak boleh mengandung bagian alamat email kamu.';
        }

        // Blocklist sederhana
        $common = [
            'password','passw0rd','qwerty','123456','12345678','iloveyou',
            'admin','welcome','secret','letmein','abc123','1q2w3e','654321',
            'p@ssw0rd','indonesia','sandi','kata_sandi'
        ];
        foreach ($common as $c) {
            if ($low === $c) return 'Password terlalu umum. Gunakan kombinasi yang lebih kuat.';
        }

        // Hindari urutan keyboard/angka/huruf (naik/turun)
        $seqs = ['abcdefghijklmnopqrstuvwxyz','qwertyuiopasdfghjklzxcvbnm','0123456789'];
        foreach ($seqs as $seq) {
            for ($i=0;$i<=strlen($seq)-4;$i++){
                $sub = substr($seq,$i,4);
                if (stripos($pwd,$sub)!==false) return 'Hindari urutan keyboard/angka/huruf yang mudah ditebak.';
                if (stripos(strrev($pwd),$sub)!==false) return 'Hindari urutan keyboard/angka/huruf yang mudah ditebak.';
            }
        }
        return true;
    }

    /** Cek reuse: tolak jika sama dengan 5 password terakhir (termasuk current) */
    private function _is_reused_password(int $userId, string $plain): bool
    {
        // ambil 5 history terbaru
        $history = $this->db->select('password_hash')
                    ->from('password_history')
                    ->where('user_id', $userId)
                    ->order_by('created_at','DESC')
                    ->order_by('id','DESC')
                    ->limit(5)->get()->result_array();

        // plus current
        if (!empty($this->user['password'])) {
            $history[] = ['password_hash' => $this->user['password']];
        }

        foreach ($history as $row) {
            if (password_verify($plain, (string)$row['password_hash'])) return true;
        }
        return false;
    }

}
