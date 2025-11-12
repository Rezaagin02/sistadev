<?php
// application/controllers/Admin.php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Wajib login
        if (!$this->session->userdata('id')) {
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg', 'Silakan login dulu.');
            redirect('auth');
        }

        // Hanya admin (role_id == 1)
        if ((int)$this->session->userdata('role_id') !== 1) {
            $this->session->set_flashdata('alert_type', 'error');
            $this->session->set_flashdata('alert_msg', 'Akses ditolak. Khusus admin.');
            redirect('user');
        }

        // TODO: protect this controller
        // e.g. check is_admin / role_id == 1
        $this->load->database();
        $this->load->helper(['url','security']);
        $this->load->library(['session']);

        // contoh: load model jika perlu
        // $this->load->model('User_model', 'users');
    }

    

    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['menu']  = 'dashboard';

        // Batas hari ini (epoch detik)
        $start_of_today = strtotime('today');

        // Helper kecil untuk count user non-admin
        $count = function(callable $fn){
            $qb = $this->db->from('user')->where('role_id !=', 1);
            $fn($qb);
            return $qb->count_all_results();
        };

        // Total pengguna (non-admin)
        $data['total_users'] = $count(function($qb){ /* no extra filter */ });

        // Email terverifikasi (email_verified_at NOT NULL)
        $data['verified_email'] = $count(function($qb){
            $qb->where('email_verified_at IS NOT NULL', null, false);
        });

        // Menunggu verifikasi admin
        $data['pending_admin'] = $count(function($qb){
            $qb->where('status', 'pending_admin');
        });

        // Menunggu verifikasi email
        $data['pending_email'] = $count(function($qb){
            $qb->where('status', 'pending_email');
        });

        // Akun aktif (sudah lolos verif admin)
        $data['active_users'] = $count(function($qb){
            $qb->where('status', 'active');
        });

        // Login hari ini
        $data['active_today'] = $count(function($qb) use ($start_of_today){
            $qb->where('last_login >=', $start_of_today);
        });

        // Pendaftar baru hari ini
        $data['new_today'] = $count(function($qb) use ($start_of_today){
            $qb->where('date_created >=', $start_of_today);
        });

        // Daftar pengguna terbaru (untuk widget/section bawah)
        $data['latest_users'] = $this->db->select('id,name,username,email,status,email_verified_at,admin_verified_at,last_login,date_created')
            ->from('user')
            ->where('role_id !=', 1)
            ->order_by('date_created', 'DESC')
            ->limit(8)
            ->get()->result_array();

        $this->render('admin/dashboard', $data);
    }

    public function users()
    {
        $data['title'] = 'Pengguna';
        $data['menu']  = 'users';

        // ambil filter status dari query string (optional)
        $status = $this->input->get('status', true);
        $data['status'] = $status ?? '';

        // Ambil data pengguna (non-admin), join untuk nama verified_by
        $this->db->select('u.*, vb.name AS verified_by_name');
        $this->db->from('user u');
        $this->db->join('user vb', 'vb.id = u.verified_by', 'left');
        $this->db->where('u.role_id !=', 1);
        if ($status !== null && $status !== '') {
            $this->db->where('u.status', $status);
        }
        $this->db->order_by('u.date_created', 'DESC');

        $data['users'] = $this->db->get()->result_array();

        // pakai loader template milikmu (render())
        $this->render('admin/users_index', $data);
    }




    /** -------- Datatables JSON (server-side) -------- */
    public function users_json()
    {
        // Columns to output
        $cols = [
            'u.id','u.name','u.email','u.role_id','u.status','u.is_active',
            'u.email_verified_at','vb.name as verified_by_name'
        ];

        // Build base query
        $this->db->from('user u');
        $this->db->select(implode(',', $cols));
        $this->db->join('user vb', 'vb.id = u.verified_by', 'left');

        // ---- (Optional) STATUS FILTER
        $status = $this->input->get('status', true);
        if ($status !== null && $status !== '') {
            $this->db->where('u.status', $status);
        }

        // ---- GLOBAL SEARCH
        $search = $this->input->get('search')['value'] ?? '';
        if ($search !== '') {
            $this->db->group_start()
                ->like('u.name', $search)
                ->or_like('u.email', $search)
                ->or_like('vb.name', $search)
            ->group_end();
        }

        // ---- COUNT FILTERED (keep the built query)
        $recordsFiltered = $this->db->count_all_results('', false); // DON'T RESET

        // ---- ORDERING
        $order = $this->input->get('order')[0] ?? ['column'=>1,'dir'=>'asc'];
        $dir   = strtolower($order['dir']) === 'desc' ? 'DESC' : 'ASC';
        // DT columns: 0 No, 1 Name, 2 Email, 3 Role, 4 Status, 5 VerifiedBy, 6 Actions
        $orderColMap = [
            1 => 'u.name',
            2 => 'u.email',
            3 => 'u.role_id',
            4 => 'u.status',
            5 => 'vb.name',
        ];
        $dbCol = $orderColMap[$order['column']] ?? 'u.name';
        $this->db->order_by($dbCol.' '.$dir);

        // ---- PAGING
        $length = (int)($this->input->get('length') ?? 10);
        $start  = (int)($this->input->get('start')  ?? 0);
        if ($length > 0) $this->db->limit($length, $start);

        // ---- FETCH ROWS
        $rows = $this->db->get()->result_array();

        // ---- TOTAL ALL (no filters)
        $recordsTotal = $this->db->count_all('user');

        // ---- BUILD RESPONSE
        $data = [];
        $no = $start + 1;
        foreach ($rows as $r) {
            $role = ((int)$r['role_id'] === 1) ? 'Admin' : 'User';
            $statusBadge = $this->_status_badge($r['status'], (int)$r['is_active']);
            $verifiedBy = $r['verified_by_name'] ? html_escape($r['verified_by_name']) : '—';

            // actions
            $btns = '<div class="btn-group btn-group-sm" role="group">';
            if ((int)$r['role_id'] !== 1) { // don't touch admin
                if ($r['status'] !== 'active') {
                    $btns .= '<button type="button" class="btn btn-success js-verify" data-id="'.$r['id'].'">Konfirmasi</button>';
                }
                $btns .= '<button type="button" class="btn btn-outline-danger js-disable" data-id="'.$r['id'].'">Disable</button>';
            } else {
                $btns .= '<button type="button" class="btn btn-outline-secondary" disabled>Admin</button>';
            }
            $btns .= '</div>';

            $data[] = [
                $no++,
                html_escape($r['name']),
                html_escape($r['email']),
                $role,
                $statusBadge,
                $verifiedBy,
                $btns
            ];
        }

        $resp = [
            'draw'            => (int)($this->input->get('draw') ?? 1),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];

        return $this->_json_ok($resp);
    }


    private function _status_badge($status, $is_active)
    {
        $status = (string)$status;
        $map = [
            'pending_email' => ['secondary','Menunggu verifikasi email'],
            'pending_admin' => ['warning','Menunggu verifikasi admin'],
            'active'        => ['success','Aktif'],
        ];
        $cls = $map[$status][0] ?? 'secondary';
        $txt = $map[$status][1] ?? ucfirst($status ?: 'unknown');
        if ((int)$is_active === 0 && $status === 'active') {
            $cls = 'secondary'; $txt = 'Nonaktif';
        }
        return '<span class="badge bg-'.$cls.'">'.$txt.'</span>';
    }



    public function verifications()
    {
        $data = [
            'title' => 'Verifikasi Akun',
            'menu'  => 'verifications',
            // 'rows'  => $this->users->get_pending(),
        ];
        $this->render('admin/verifications', $data);
    }

    public function verify()
    {
        if ($this->input->method() !== 'post') return $this->_json_err('Invalid method', 405);

        $id = (int)$this->input->post('id');
        if (!$id) return $this->_json_err('ID tidak valid');

        $user = $this->db->get_where('user', ['id'=>$id])->row_array();
        if (!$user) return $this->_json_err('User tidak ditemukan');
        if ((int)$user['role_id'] === 1) return $this->_json_err('Tidak boleh memodifikasi admin');

        $adminId = (int)$this->session->userdata('id');
        if (!$adminId) return $this->_json_err('Unauthorized', 401);

        $data = [
            'status'            => 'active',
            'is_active'         => 1,
            'email_verified_at' => $user['email_verified_at'] ?: date('Y-m-d H:i:s'),
            'verified_by'       => $adminId,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        $this->db->where('id', $id)->update('user', $data);

        return $this->_json_ok(['msg'=>'Verified']);
    }

    /** -------- Disable user (AJAX) -------- */
    public function disable()
    {
        if ($this->input->method() !== 'post') return $this->_json_err('Invalid method', 405);

        $id = (int)$this->input->post('id');
        if (!$id) return $this->_json_err('ID tidak valid');

        $user = $this->db->get_where('user', ['id'=>$id])->row_array();
        if (!$user) return $this->_json_err('User tidak ditemukan');
        if ((int)$user['role_id'] === 1) return $this->_json_err('Tidak boleh memodifikasi admin');

        $data = [
            'is_active'  => 0,
            'status'     => 'pending_admin', // sesuai enum kamu
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->where('id', $id)->update('user', $data);

        return $this->_json_ok(['msg'=>'Disabled']);
    }

    


    public function settings()
    {
        $data = [
            'title' => 'Pengaturan Admin',
            'menu'  => 'settings',
        ];
        $this->render('admin/settings', $data);
    }

    private function _json_ok($payload = [], $code = 200){
        $payload['ok'] = true;
        // send new csrf hash (works whether regenerate true/false)
        $payload['csrf_hash'] = $this->security->get_csrf_hash();
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
    private function _json_err($message = 'Error', $code = 400){
        $payload = ['ok'=>false, 'error'=>$message, 'csrf_hash'=>$this->security->get_csrf_hash()];
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /** Loader template admin */
   // controllers/Admin.php
private function render($view, $data = [])
{
    $this->load->view('admin/_partials/header', $data);   // buka <div class="layout">
    $this->load->view('admin/_partials/sidebar', $data);  // kolom kiri
    $this->load->view('admin/_partials/topbar', $data);   // buka .content-wrap + <main>
    $this->load->view($view, $data);                      // isi
    $this->load->view('admin/_partials/footer', $data);   // tutup </main></div></div>
}

}
