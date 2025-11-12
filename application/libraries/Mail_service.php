<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mail_service
{
    protected $CI;
    protected $map;

    // tema
    protected $theme = [];
    protected $themeDark = [];
    protected $variants = [];
    protected $variantsDark = [];

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('email');
        $this->CI->load->helper(['url','html']);
        $this->CI->load->config('mail_theme', true); // load config tema

        // ambil dari config (dengan fallback)
        $this->theme       = $this->CI->config->item('mail_theme', 'mail_theme') ?: [];
        $this->themeDark   = $this->CI->config->item('mail_theme_dark', 'mail_theme') ?: [];
        $this->variants    = $this->CI->config->item('mail_theme_variants', 'mail_theme') ?: [];
        $this->variantsDark= $this->CI->config->item('mail_theme_variants_dark', 'mail_theme') ?: [];

        $this->map = [
            'welcome'         => ['Selamat Datang di SISTA', 'welcome'],
            'verify'          => ['Verifikasi Akun Kamu',    'verify_account'],
            'reset'           => ['Reset Password SISTA',    'reset_password'],
            'pwd_changed'     => ['Password Berhasil Diubah','password_changed'],
            'profile_updated' => ['Profil Berhasil Diperbarui','profile_updated'],
            'generic'         => ['Pemberitahuan',           'generic'],
            'otp_login'       => ['Kode OTP Masuk SISTA',    'otp_login'],
        ];
    }

    private function buildTheme(?string $variant = null): array
    {
        // base
        $light = $this->theme;
        $dark  = $this->themeDark;

        // apply variant if any
        if ($variant && isset($this->variants[$variant])) {
            $light = array_merge($light, $this->variants[$variant]);
        }
        if ($variant && isset($this->variantsDark[$variant])) {
            $dark  = array_merge($dark,  $this->variantsDark[$variant]);
        }
        return [$light, $dark];
    }

    public function send(string $slug, $to, array $data = [], array $opts = []): bool
    {
        // 1) Map view + subject default
        if (!isset($this->map[$slug])) $slug = 'generic';
        [$defaultSubject, $view] = $this->map[$slug];

        // 2) Theme (light/dark) auto jika tidak disuplai
        $variant = $opts['variant'] ?? null;  // 'success' | 'warning' | 'info' | null
        if (empty($data['_theme']) || empty($data['_theme_dark'])) {
            [$light, $dark] = $this->buildTheme($variant);
            $data['_theme']      = $data['_theme']      ?? $light;
            $data['_theme_dark'] = $data['_theme_dark'] ?? $dark;
        }

        // 3) Subjek & identitas pengirim
        $subject   = $opts['subject'] ?? $defaultSubject;
        $cfg       = $this->CI->config->item('email');
        // gunakan default dari config/email.php
        $fromEmail = $opts['from_email'] ?? ($cfg['default_from_email'] ?? 'sista.lapi@gmail.com');
        $fromName  = $opts['from_name']  ?? ($cfg['default_from_name']  ?? 'SISTA');

        // 4) Render konten
        $data['_subject'] = $subject;
        $inner = $this->CI->load->view("emails/{$view}", $data, true);

        // Pastikan warna teks tetap konsisten (beberapa client mengabaikan CSS)
        $txtColor = $data['_theme']['text'] ?? '#0f172a';
        $inner = '<div style="color:'.$txtColor.';line-height:1.6;"><font color="'.$txtColor.'">'.$inner.'</font></div>';

        $html  = $this->CI->load->view('emails/_layout', ['content'=>$inner] + $data, true);
        $text  = trim(html_entity_decode(strip_tags($inner)));

        // 5) Siapkan email
        $this->CI->email->clear(true);

        // Set From + Return-Path (param ke-3 di CI3 adalah return-path)
        $this->CI->email->from($fromEmail, $fromName, $fromEmail);

        // Default Reply-To = email pengirim (bisa dioverride via $opts['reply_to'])
        if (!empty($opts['reply_to'])) {
            $this->CI->email->reply_to($opts['reply_to'], $opts['reply_name'] ?? $fromName);
        } else {
            $this->CI->email->reply_to($fromEmail, $fromName);
        }
        // Beberapa provider lebih patuh jika Return-Path dipaksakan juga sebagai header
        $this->CI->email->set_header('Return-Path', $fromEmail);

        // Penerima
        is_array($to) ? $this->CI->email->to($to) : $this->CI->email->to($to);
        if (!empty($opts['cc']))  $this->CI->email->cc($opts['cc']);
        if (!empty($opts['bcc'])) $this->CI->email->bcc($opts['bcc']);

        // Konten
        $this->CI->email->subject($subject);
        $this->CI->email->message($html);
        $this->CI->email->set_alt_message($text);

        // 6) Kirim
        $ok = $this->CI->email->send(false);
        if (!$ok) {
            log_message('error', 'MAIL ERR: '.$this->CI->email->print_debugger(['headers']));
        }
        return $ok;
    }

}
