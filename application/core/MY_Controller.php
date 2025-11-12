<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $me;         // row user login
    protected $cv;         // cv terbaru
    protected $cv_counts;  // pengalaman/pendidikan/sertif counts
    protected $cv_progress;
    protected $cv_missing;
    protected $activity_count;

    public function __construct()
    {
        parent::__construct();
        // pastiin yg ini autoload di config/autoload.php: database, session, url, etc.

        // --- Load helper & model umum ---
        $this->load->helper(['url', 'cv']); // cv_helper dari kita
        $this->load->model('Profile_model', 'profile');

        // --- Ambil user login (early return kalau belum login) ---
        $email = (string) $this->session->userdata('email');
        if (!$email) {
            // Guest mode
            $this->load->vars([
                'user'             => null,
                'cv'               => null,
                'counts' => [
    'pengalaman'         => (int)($this->cv_counts['pengalaman'] ?? 0),
    'pendidikan'         => (int)($this->cv_counts['pendidikan'] ?? 0),
    'pendidikan_formal'  => (int)($this->cv_counts['pendidikan_formal'] ?? 0),
    'pelatihan'          => (int)($this->cv_counts['pelatihan'] ?? 0), // << ensure ada
    'sertifikasi'        => (int)($this->cv_counts['sertifikasi'] ?? 0),
    'bahasa'             => (int)($this->cv_counts['bahasa'] ?? 0),
    'lampiran'           => (int)($this->cv_counts['lampiran'] ?? 0),
  ],

                'cv_progress'      => null,
                'missing_sections' => [],
                'activity_count'   => 0,
            ]);
            return;
        }

        // Suggested people (5 orang paling lengkap + terbaru)
       
        


        $this->me = $this->profile->get_user_by_email($email);        // table user
        $user_id  = (int)($this->me['id'] ?? 0);

        $suggested_people = $this->profile->get_suggested_people($user_id, 5);

        $this->load->vars([
            // ... variabel lain sebelumnya ...
            'suggested_people' => $suggested_people,
        ]);

        // --- Data CV + counts ---
        $this->cv         = $this->profile->get_latest_cv($user_id);   // table cv (terbaru)
        $cv_id            = (int)($this->cv['id'] ?? 0);
        $this->cv_counts = $this->profile->get_cv_counts_by_user($user_id);     // pengalaman/pendidikan/sertif/bahasa/lampiran
        $this->activity_count = $this->profile->get_activity_count($user_id); // opsional

        // --- Progress helper kita ---
        list($p, $missing) = cv_compute_progress($this->db, $user_id);
        $this->cv_progress = $p;
        $this->cv_missing  = $missing;

        // --- Inject ke semua view (global vars) ---
        $this->load->vars([
            'user'             => $this->me,
            'cv'               => $this->cv,
            'counts' => [
                'pengalaman'         => (int)($this->cv_counts['pengalaman'] ?? 0),
                'pendidikan'         => (int)($this->cv_counts['pendidikan'] ?? 0),        // formal only
                'pendidikan_formal'  => (int)($this->cv_counts['pendidikan_formal'] ?? 0),
                'pelatihan'          => (int)($this->cv_counts['pelatihan'] ?? 0),         // << penting!
                'sertifikasi'        => (int)($this->cv_counts['sertifikasi'] ?? 0),
                'bahasa'             => (int)($this->cv_counts['bahasa'] ?? 0),
                'lampiran'           => (int)($this->cv_counts['lampiran'] ?? 0),
            ],
            'cv_progress'      => $this->cv_progress,
            'missing_sections' => $this->cv_missing,
            'activity_count'   => (int)$this->activity_count,
            'show_cv_nudge'    => ($this->cv_progress !== null && $this->cv_progress < 100),
        ]);

    }
}
