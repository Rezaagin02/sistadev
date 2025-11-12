<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * QUICK-START (hardcode) — cepat tapi jangan di-commit ke repo publik.
 * Untuk mode aman, lihat "email_local.php" di bawah.
 */
$config['protocol']    = 'smtp';
$config['smtp_host']   = 'smtp.gmail.com';
$config['smtp_user']   = 'sista.lapi@gmail.com';      // <= punyamu
$config['smtp_pass']   = 'tjmvjwdmkhigkxpq';         // <= App Password (tanpa spasi)
$config['smtp_port']   = 587;                         // TLS
$config['smtp_crypto'] = 'tls';                       // atau 'ssl' + 465
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
$config['crlf']        = "\r\n";
$config['useragent']   = 'SISTA Mailer';
$config['wordwrap']    = TRUE;

$config['default_from_email'] = 'sista.lapi@gmail.com';
$config['default_from_name']  = 'SISTA · PT LAPI ITB';
