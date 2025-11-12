<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('h')) {
  function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('dmy')) {
  function dmy($v){ return $v ? date('d M Y', strtotime($v)) : '—'; }
}
if (!function_exists('initials')) {
  function initials($name){
    $name = trim((string)$name);
    if ($name==='') return 'U';
    $parts = preg_split('/\s+/', $name);
    $ini = strtoupper(mb_substr($parts[0],0,1));
    if (count($parts)>1) $ini .= strtoupper(mb_substr(end($parts),0,1));
    return $ini;
  }
}
