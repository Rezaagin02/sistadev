<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model(['User_model','Auth_token_model']);
        $this->load->library(['form_validation']);
        $this->load->helper(['url','security','mailer']); // send_mail()
    }

    private function generate_otp_code(): string
    {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT); // 6 digit
    }

    private function device_token(): string
    {
        // token untuk cookie perangkat tepercaya
        return bin2hex(random_bytes(32)); // 64 hex
    }

    public function index()
    {
        if ($this->session->userdata('email')) {
            redirect('admin');
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login Page';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            // validasinya success
            $this->_login();
        }
    }


    private function _login()
    {
        $email    = trim($this->input->post('email', true));
        $password = (string)$this->input->post('password');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        // Jika user tidak ada -> pesan umum
        if (!$user) {
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg',  'Email atau password salah.');
            return redirect('auth');
        }

        // Password salah -> pesan umum
        if (!password_verify($password, $user['password'])) {
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg',  'Email atau password salah.');
            return redirect('auth');
        }

        // Password benar, cek status/verifikasi
        $status   = $user['status'] ?? '';
        $isActive = (int)$user['is_active'];

        // 1) Belum verifikasi email
        if ($status === 'pending_email' || empty($user['email_verified_at'])) {
            $this->session->set_flashdata('alert_type', 'warning');
            $this->session->set_flashdata(
                'alert_msg',
                'Akun kamu belum terverifikasi. Silakan cek email untuk verifikasi. ' .
                'Jika belum menerima email, cek folder Spam/Junk.'
            );
            return redirect('auth');
        }

        // 2) Menunggu persetujuan admin / nonaktif
        if ($status === 'pending_admin' || $isActive !== 1) {
            $this->session->set_flashdata('alert_type', 'info');
            $this->session->set_flashdata(
                'alert_msg',
                'Akun kamu menunggu persetujuan admin. Silakan hubungi admin untuk percepatan proses.'
            );
            return redirect('auth');
        }

        // ==== 3) Akun aktif -> cek perangkat tepercaya, kalau tidak -> OTP ====
        $trusted = false;
        if (!empty($_COOKIE['sista_trust'])) {
            $token = $_COOKIE['sista_trust'];
            $hash  = hash('sha256', $token);
            $dev   = $this->db->get_where('user_devices', [
                'user_id'    => (int)$user['id'],
                'token_hash' => $hash,
            ])->row_array();

            if ($dev) {
                $trusted = true;
                $this->db->where('id', (int)$dev['id'])->update('user_devices', [
                    'last_used_at' => date('Y-m-d H:i:s'),
                    'ip'           => $this->input->ip_address(),
                    'ua'           => substr($this->input->user_agent(), 0, 255),
                ]);
            }
        }

        if ($trusted) {
            // langsung buat sesi & redirect seperti alur lamamu
            return $this->_do_create_session_and_redirect($user);
        }

        // Tidak trusted -> mulai alur OTP (kirim email + redirect ke /auth/otp)
        $this->_start_otp_flow($user);
        return;
    }

    private function _do_create_session_and_redirect(array $user)
    {
        $this->session->set_userdata([
            'id'        => (int)$user['id'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role_id'   => (int)$user['role_id'],
            'is_admin'  => (int)($user['is_admin'] ?? 0),
            'id_access' => $user['id_access'] ?? null,
        ]);

        $this->db->where('id', (int)$user['id'])
                ->update('user', ['last_login' => time()]);

        if ((int)$user['role_id'] === 1) {
            $this->session->set_flashdata('alert_type', 'success');
            $this->session->set_flashdata('alert_msg',  'Welcome back, admin!');
            redirect('admin');
            return;
        }

        // cek CV seperti versi kamu
        $hasCv = $this->db->where('user_id', (int)$user['id'])
                        ->count_all_results('cv') > 0;

        if (!$hasCv) {
            $this->session->set_flashdata('alert_type', 'warning');
            $this->session->set_flashdata('alert_msg',  'Profil kamu belum lengkap. Lengkapi CV dulu ya.');
            redirect('user');
            return;
        }

        $this->session->set_flashdata('alert_type', 'success');
        $this->session->set_flashdata('alert_msg',  'Berhasil masuk. Have a great day!');
        redirect('user');
    }





     // GET+POST /auth/register
    public function register()
    {
        if ($this->input->method() === 'post') {
            // Honeypot anti-bot
            if ($this->input->post('website')) {
                show_error('Bot detected', 400);
            }

            $this->load->library('form_validation');

            $this->form_validation->set_rules('name', 'Nama', 'required|trim|min_length[3]');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
            // pakai alpha_dash agar _ dan - boleh; kalau mau titik juga, ganti ke regex/alpha_numeric PLUS validasi manual
            $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[4]|alpha_dash');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
            // ✅ sinkron dengan view kita
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');

            if ($this->form_validation->run()) {
                $email    = strtolower(trim($this->input->post('email', true)));
                $username = strtolower(trim($this->input->post('username', true)));

                // Unik?
                if ($this->User_model->get_by_email($email)) {
                    $this->session->set_flashdata('error', 'Email sudah terdaftar.');
                    return redirect('auth/register');
                }
                if ($this->User_model->get_by_username($username)) {
                    $this->session->set_flashdata('error', 'Username sudah dipakai.');
                    return redirect('auth/register');
                }

                $pwd = $this->input->post('password', false);
                if ($pwd === '123456') {
                    $this->session->set_flashdata('warning', 'Password terlalu lemah. Gunakan minimal 8 karakter, bukan 123456.');
                    return redirect('auth/register');
                }

                // Buat user
                $uid = $this->User_model->create([
                    'name'         => $this->input->post('name', true),
                    'email'        => $email,
                    'username'     => $username,
                    'password'     => password_hash($pwd, PASSWORD_BCRYPT),
                    'role_id'      => 2, // default user   
                    'status'           => 'pending_email',
                    'is_active'        => 0,
                    'email_verified_at'=> null,
                    'admin_verified_at'=> null,
                    'verified_by'      => null,
                    'date_created' => time(),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);

                // Token verifikasi & kirim email
                $token  = $this->Auth_token_model->create($uid, 'verify', 24); // 24 jam
                $verify = base_url('auth/verify/'.$token);

                send_mail('verify', $email, [
                    'name'         => $this->input->post('name', true),
                    'verify_url'   => $verify,
                    'expires_text' => '24 jam',
                ], [
                    'subject' => 'Verifikasi Akun SISTA',
                ]);

                $this->session->set_flashdata('success', 'Akun dibuat. Cek email untuk verifikasi.');
                return redirect('auth');
            }

            // Invalid
            $this->session->set_flashdata('error', validation_errors('', ''));
            return redirect('auth/register');
        }

        // GET
        $data['title'] = 'Daftar Akun';
        $this->load->view('templates/auth_header', $data);
        $this->load->view('auth/register', $data);
        $this->load->view('templates/auth_footer');
    }


    // GET /auth/verify/{token}
    public function verify($token = '')
    {
        if (!$token) {
            $this->session->set_flashdata('error', 'Token verifikasi tidak ditemukan.');
            return redirect('auth');
        }

        // pakai token sekali
        $tok = $this->Auth_token_model->consume($token, 'verify');
        if (!$tok) {
            $this->session->set_flashdata('error', 'Token verifikasi tidak valid atau sudah kadaluarsa.');
            return redirect('auth');
        }

        // safety: cek kalau user sudah verified
        $u = $this->User_model->get_by_id($tok['user_id']);
        if (!$u) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            return redirect('auth');
        }
        if (!empty($u['email_verified_at'])) {
            $this->session->set_flashdata('info', 'Email sudah diverifikasi sebelumnya.');
            return redirect('auth');
        }

        // set verified → status pending_admin
        $this->User_model->mark_email_verified($tok['user_id']);

        $this->session->set_flashdata(
            'success',
            'Email berhasil diverifikasi. Akun akan diaktifkan setelah disetujui admin.'
        );
        return redirect('auth'); // ke halaman login
    }

    // POST /auth/resend (optional)
    public function resend_verification()
    {
        $email = strtolower(trim($this->input->post('email', true)));
        if (!$email) return redirect('auth');

        $u = $this->User_model->get_by_email($email);
        if (!$u) {
        $this->session->set_flashdata('error', 'Email tidak ditemukan.');
        return redirect('auth');
        }
        if ((int)$u['is_active'] === 1) {
        $this->session->set_flashdata('info', 'Akun sudah aktif. Silakan login.');
        return redirect('auth');
        }

        $token  = $this->Auth_token_model->create($u['id'], 'verify', 24);
        $verify = base_url('auth/verify/'.$token);

        send_mail('verify', $email, [
        'name'         => $u['name'] ?? 'Pengguna',
        'verify_url'   => $verify,
        'expires_text' => '24 jam',
        ], ['subject' => 'Kirim Ulang Verifikasi SISTA']);

        $this->session->set_flashdata('success', 'Link verifikasi sudah dikirim ulang.');
        return redirect('auth');
    }


    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');
        $this->session->sess_destroy();

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">You have been logged out!</div>');
        redirect('auth');
    }


    public function blocked()
    {
        $this->load->view('auth/blocked');
    }


    public function forgotPassword()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Forgot Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgot-password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email');
            $user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();

            if ($user) {
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ];

                $this->db->insert('user_token', $user_token);
                $this->_sendEmail($token, 'forgot');

                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Please check your email to reset your password!</div>');
                redirect('auth/forgotpassword');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email is not registered or activated!</div>');
                redirect('auth/forgotpassword');
            }
        }
    }


    public function resetPassword()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

            if ($user_token) {
                $this->session->set_userdata('reset_email', $email);
                $this->changePassword();
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Reset password failed! Wrong token.</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Reset password failed! Wrong email.</div>');
            redirect('auth');
        }
    }


    public function changePassword()
    {
        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
        }

        $this->form_validation->set_rules('password1', 'Password', 'trim|required|min_length[3]|matches[password2]');
        $this->form_validation->set_rules('password2', 'Repeat Password', 'trim|required|min_length[3]|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/change-password');
            $this->load->view('templates/auth_footer');
        } else {
            $password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');

            $this->db->set('password', $password);
            $this->db->where('email', $email);
            $this->db->update('user');

            $this->session->unset_userdata('reset_email');

            $this->db->delete('user_token', ['email' => $email]);

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Password has been changed! Please login.</div>');
            redirect('auth');
        }
    }

    private function _start_otp_flow(array $user): void
    {
        // hapus otp lama yang belum dipakai
        $this->db->where('user_id', (int)$user['id'])
                ->where('purpose', 'login')
                ->where('used_at IS NULL', null, false)
                ->delete('user_otp');

        $code = $this->generate_otp_code();

        $this->db->insert('user_otp', [
            'user_id'      => (int)$user['id'],
            'purpose'      => 'login',
            'code'         => $code,
            'sent_to'      => $user['email'],
            'expires_at'   => date('Y-m-d H:i:s', time() + 600), // 10 menit
            'attempts'     => 0,
            'max_attempts' => 5,
            'used_at'      => null,
            'created_at'   => date('Y-m-d H:i:s'),
            'ip'           => $this->input->ip_address(),
            'ua'           => substr($this->input->user_agent(), 0, 255),
            'resend_after' => date('Y-m-d H:i:s', time() + 60),  // throttle 60s
        ]);
        $otp_id = $this->db->insert_id();

        // kirim email (pakai helper send_mail punyamu)
        send_mail('otp_login', $user['email'], [
            'name'    => $user['name'],
            'code'    => $code,
            'expires' => '10 menit',
            'brand'   => 'SISTA',
        ], ['subject' => 'Kode OTP Masuk SISTA']);

        // simpan state pending di session
        $this->session->set_userdata('pending_otp', [
            'otp_id' => $otp_id,
            'uid'    => (int)$user['id'],
            'email'  => $user['email']
        ]);

        redirect('auth/otp');
    }

  
    private function _new_otp_code(int $digits = 6): string
    {
        $min = (int) str_pad('1', $digits, '0');
        $max = (int) str_pad('',  $digits, '9');
        return (string) random_int($min, $max);
    }

    

    public function otp()
    {
        $pending = $this->session->userdata('pending_otp');
        if (!$pending) { redirect('auth'); return; }

        $masked = $pending['email'];
        // masking sederhana
        if (strpos($masked, '@') !== false) {
            [$a,$b] = explode('@', $masked, 2);
            $masked = substr($a,0,1) . str_repeat('•', max(1, strlen($a)-2)) . substr($a,-1) . '@' . $b;
        }

        $data['title'] = 'Verifikasi OTP';
        $data['email_masked'] = $masked;
        $this->load->view('templates/auth_header', $data);
        $this->load->view('auth/otp', $data);
        $this->load->view('templates/auth_footer');
    }

    public function otp_verify()
    {
        $pend = $this->session->userdata('pending_otp');
        if (!$pend || empty($pend['uid'])) {
            $this->session->set_flashdata('alert_type', 'warning');
            $this->session->set_flashdata('alert_msg', 'Sesi OTP tidak ditemukan. Silakan login ulang.');
            return redirect('auth');
        }

        $code = preg_replace('/\D/','', (string)$this->input->post('code'));
        if (strlen($code) !== 6) {
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg', 'Kode OTP tidak valid.');
            return redirect('auth/otp');
        }

        $otp = $this->db->get_where('user_otp', [
            'id'      => (int)$pend['otp_id'],
            'user_id' => (int)$pend['uid'],
            'purpose' => 'login',
        ])->row_array();

        if (!$otp) {
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg', 'Sesi OTP tidak valid.');
            return redirect('auth');
        }

        if (!empty($otp['used_at'])) {
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg', 'Kode OTP telah digunakan.');
            return redirect('auth');
        }

        if (strtotime($otp['expires_at']) < time()) {
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg', 'Kode OTP kedaluwarsa. Silakan login ulang.');
            return redirect('auth');
        }

        // cek attempts
        if ((int)$otp['attempts'] >= (int)$otp['max_attempts']) {
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg', 'Percobaan OTP melebihi batas.');
            return redirect('auth');
        }

        if (!hash_equals($otp['code'], $code)) {
            // tambah attempt
            $this->db->where('id', (int)$otp['id'])->update('user_otp', [
                'attempts' => (int)$otp['attempts'] + 1
            ]);
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg', 'Kode OTP salah.');
            return redirect('auth/otp');
        }

        // valid -> tandai used
        $this->db->where('id', (int)$otp['id'])->update('user_otp', [
            'used_at' => date('Y-m-d H:i:s')
        ]);

        // “ingat perangkat” (boleh dari OTP view atau dari pending_otp)
        $rememberFlag = (bool)$this->input->post('remember');
        if (!$rememberFlag && isset($pend['remember'])) {
            $rememberFlag = (bool)$pend['remember'];
        }

        if ($rememberFlag) {
            $token = bin2hex(random_bytes(32));
            $hash  = hash('sha256', $token);

            // simpan device
            $this->db->insert('user_devices', [
                'user_id'     => (int)$pend['uid'],
                'token_hash'  => $hash,
                'ip'          => $this->input->ip_address(),
                'ua'          => substr($this->input->user_agent(), 0, 255),
                'last_used_at'=> date('Y-m-d H:i:s'),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            // set cookie 30 hari
            $exp = time() + 60*60*24*30;
            setcookie('sista_trust', $token, [
                'expires'  => $exp,
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        // selesai OTP -> buat sesi seperti biasa
        $user = $this->db->get_where('user', ['id' => (int)$pend['uid']])->row_array();
        $this->session->unset_userdata('pending_otp');

        return $this->_do_create_session_and_redirect($user);
    }

    public function otp_resend()
    {
        if ($this->input->method() !== 'post') {
            return $this->output->set_status_header(405)->set_output(json_encode(['ok'=>false,'error'=>'Method not allowed']));
        }
        $pend = $this->session->userdata('pending_otp');
        if (!$pend || empty($pend['uid'])) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['ok'=>false,'error'=>'Sesi OTP tidak ada']));
        }

        $otp = $this->db->get_where('user_otp', [
            'id'      => (int)$pend['otp_id'],
            'user_id' => (int)$pend['uid'],
            'purpose' => 'login',
        ])->row_array();

        if (!$otp) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['ok'=>false,'error'=>'OTP tidak ditemukan']));
        }

        if (strtotime($otp['resend_after']) > time()) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['ok'=>false,'error'=>'Tunggu sebelum kirim ulang']));
        }

        // generate baru
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->db->where('id', (int)$otp['id'])->update('user_otp', [
            'code'        => $code,
            'expires_at'  => date('Y-m-d H:i:s', time()+600),
            'resend_after'=> date('Y-m-d H:i:s', time()+60),
            'attempts'    => 0,
            'used_at'     => null,
            'ip'          => $this->input->ip_address(),
            'ua'          => substr($this->input->user_agent(), 0, 255),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $user = $this->db->get_where('user', ['id'=>(int)$pend['uid']])->row_array();
        send_mail('otp_login', $user['email'], [
            'name'    => $user['name'],
            'code'    => $code,
            'expires' => '10 menit',
            'brand'   => 'SISTA',
        ], ['subject'=>'Kode OTP Masuk SISTA']);

        return $this->output->set_content_type('application/json')->set_output(json_encode(['ok'=>true]));
    }

    

    private function _otp_fail(string $msg)
    {
        $this->session->set_flashdata('alert_type','error');
        $this->session->set_flashdata('alert_msg',$msg);
        redirect('auth/otp');
        return;
    }



}
