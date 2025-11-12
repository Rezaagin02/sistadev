<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'user';
    protected $pk    = 'id';

    protected $updatable_user = [ /* lihat poin #1 */ ];
    protected $updatable_admin = [ /* lihat poin #1 */ ];

    public function getById($id){ return $this->db->where($this->pk,(int)$id)->get($this->table)->row_array(); }
    public function get_by_id($id){ return $this->getById($id); } // optionally remove one

    public function updateById($id, array $data, $scope='user'){ /* seperti di atas */ }
    public function update($id, $data){ return $this->updateById($id, $data, 'admin'); } // backward-compat, used by admin paths

    public function get_by_email($email){
        return $this->db->where('LOWER(email)=', strtolower($email))->get($this->table)->row_array();
    }

    public function exists_other_with_email(string $email, int $excludeUserId): bool
    {
        return (bool) $this->db->where('LOWER(email)=', strtolower($email))
            ->where('id !=', (int)$excludeUserId)
            ->count_all_results($this->table);
    }

    public function get_by_any_email(string $email): ?array
    {
        $email = strtolower(trim($email));
        $row = $this->db->where('LOWER(email)=', $email)->get($this->table)->row_array();
        if ($row) return $row;

        $this->db->where("LOWER(emails_all) REGEXP", '(^|,\\s*)'.preg_quote($email, '/').'(\\s*,|$)', false);
        $row = $this->db->get($this->table)->row_array();
        return $row ?: null;
    }

    public function get_by_username($username){
        return $this->db->get_where($this->table, ['username' => $username])->row_array();
    }

    public function create(array $data){
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function activate($user_id){
        $this->db->where('id', (int)$user_id)->update($this->table, [
            'is_active'  => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->db->affected_rows() > 0;
    }

    public function mark_email_verified($user_id){
        $data = [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'status'            => 'pending_admin',
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        return $this->updateById($user_id, $data, 'admin');
    }

    public function approve_by_admin($user_id, $admin_id){
        $data = [
            'is_active'         => 1,
            'status'            => 'active',
            'admin_verified_at' => date('Y-m-d H:i:s'),
            'verified_by'       => (int)$admin_id,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        return $this->updateById($user_id, $data, 'admin');
    }

    public function getPeopleYouMayKnow($currentUserId, $limit=12, $offset=0, $q=null)
    {
        $this->db->select("
            u.id, u.name, u.username, u.email, u.photo, u.cover, u.is_active, u.last_login, u.updated_at AS user_updated,
            cv.posisi, cv.perusahaan, cv.domisili_kota, cv.domisili_negara, cv.updated_at AS cv_updated
        ");
        $this->db->from('user u');
        $this->db->join('cv', 'cv.user_id = u.id', 'left');
        $this->db->where('u.is_active', 1);
        $this->db->where('u.id !=', (int)$currentUserId);

        if (!empty($q)) {
            $this->db->group_start()
                     ->like('u.name', $q)
                     ->or_like('cv.posisi', $q)
                     ->or_like('cv.perusahaan', $q)
                     ->group_end();
        }

        $this->db->order_by('COALESCE(cv.updated_at, u.updated_at, FROM_UNIXTIME(u.last_login))', 'DESC', false);
        $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    public function countPeopleYouMayKnow($currentUserId, $q=null)
    {
        $this->db->from('user u');
        $this->db->join('cv', 'cv.user_id = u.id', 'left');
        $this->db->where('u.is_active', 1);
        $this->db->where('u.id !=', (int)$currentUserId);

        if (!empty($q)) {
            $this->db->group_start()
                     ->like('u.name', $q)
                     ->or_like('cv.posisi', $q)
                     ->or_like('cv.perusahaan', $q)
                     ->group_end();
        }

        return (int)$this->db->count_all_results();
    }
}
