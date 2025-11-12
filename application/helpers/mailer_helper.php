<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('send_mail')) {
  function send_mail(string $slug, $to, array $data = [], array $opts = []): bool {
    $CI =& get_instance();
    $CI->load->library('mail_service');
    return $CI->mail_service->send($slug, $to, $data, $opts);
  }
}
