<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_token_model extends CI_Model
{
    protected $table = 'auth_tokens'; // sesuaikan

    public function create($user_id, $type, $ttl_hours = 24)
    {
        $token = bin2hex(random_bytes(32));
        $data = [
            'user_id'    => (int)$user_id,
            'type'       => $type, // 'verify'
            'token'      => $token,
            'expires_at' => date('Y-m-d H:i:s', time() + $ttl_hours * 3600),
            'used_at'    => null,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->insert($this->table, $data);
        return $token;
    }

    /** Ambil token yang masih valid lalu tandai sebagai terpakai. */
    public function consume($token, $type = 'verify')
    {
        $row = $this->db->where('token', $token)
                        ->where('type', $type)
                        ->where('expires_at >=', date('Y-m-d H:i:s'))
                        ->where('used_at IS NULL', null, false)
                        ->get($this->table)->row_array();
        if (!$row) return null;

        $this->db->where('id', $row['id'])->update($this->table, [
            'used_at' => date('Y-m-d H:i:s'),
        ]);
        return $row;
    }
}