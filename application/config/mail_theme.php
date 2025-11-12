<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tema default (LIGHT)
 */
$config['mail_theme'] = [
  'primary'     => '#0b5ed7',
  'accent'      => '#22c55e',
  'brand_bg'    => '#eef4ff',
  'brand_text'  => '#0f172a',
  'text'        => '#0f172a',
  'muted'       => '#64748b',
  'border'      => '#eef2f7',
  'card_bg'     => '#ffffff',
  'page_bg'     => '#f4f6fb',
  'btn_bg'      => '#0b5ed7',
  'btn_text'    => '#ffffff',
  'btn_outline' => '#dbe7ff',
];

/**
 * Tema default (DARK)
 */
$config['mail_theme_dark'] = [
  'primary'     => '#3b82f6',
  'accent'      => '#22c55e',
  'brand_bg'    => '#0b172d',
  'brand_text'  => '#e5e7eb',
  'text'        => '#e5e7eb',
  'muted'       => '#9ca3af',
  'border'      => '#1f2937',
  'card_bg'     => '#111827',
  'page_bg'     => '#0b1220',
  'btn_bg'      => '#3b82f6',
  'btn_text'    => '#ffffff',
  'btn_outline' => '#21407a',
];

/**
 * Variants opsional (buat email tertentu: success/warn/info).
 * Dipanggil via $opts['variant'] = 'success' saat send().
 */
$config['mail_theme_variants'] = [
  'success' => [
    'primary'   => '#16a34a',  // hijau
    'accent'    => '#22c55e',
    'brand_bg'  => '#e7f7ee',
    'btn_bg'    => '#16a34a',
  ],
  'warning' => [
    'primary'   => '#f59e0b',  // amber
    'accent'    => '#f97316',
    'brand_bg'  => '#fff4e5',
    'btn_bg'    => '#f59e0b',
  ],
  'info' => [
    'primary'   => '#0ea5e9',  // biru muda
    'accent'    => '#38bdf8',
    'brand_bg'  => '#e0f2fe',
    'btn_bg'    => '#0ea5e9',
  ],
];

$config['mail_theme_variants_dark'] = [
  'success' => [
    'primary'   => '#22c55e', 'btn_bg' => '#22c55e', 'brand_bg' => '#0d1f17',
  ],
  'warning' => [
    'primary'   => '#fbbf24', 'btn_bg' => '#f59e0b', 'brand_bg' => '#2b1c05',
  ],
  'info' => [
    'primary'   => '#38bdf8', 'btn_bg' => '#38bdf8', 'brand_bg' => '#0b172d',
  ],
];
