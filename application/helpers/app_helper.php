<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('media_url')) {
  function media_url($rel) {
    if (!$rel) return '';
    $rel = ltrim($rel, '/');                 // pastikan relatif
    $rel = str_replace(' ', '%20', $rel);    // encode spasi nakal
    return base_url($rel);
  }
}

if (!function_exists('h')) {
  function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
  }
}

if (!function_exists('dmy')) {
  function dmy($date) {
    if (empty($date)) return '—';
    $ts = strtotime($date);
    return $ts ? date('d M Y', $ts) : $date;
  }
}

if (!function_exists('initials')) {
  function initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $first = isset($parts[0][0]) ? $parts[0][0] : '';
    $last  = isset($parts[count($parts)-1][0]) ? $parts[count($parts)-1][0] : '';
    return strtoupper($first.$last) ?: 'U';
  }
}
