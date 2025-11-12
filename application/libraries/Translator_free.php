<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Translator_free
{
    protected $CI;
    protected $cfg;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->cfg = $this->CI->config->item('translator_free');
        $this->CI->load->database();
    }

    public function translate($text, $to = null, $from = null)
    {
        if (!is_string($text) || $text === '') return $text;

        $to   = $to   ?: ($this->cfg['default_to'] ?? 'en');
        $from = $from ?: ($this->cfg['default_from'] ?? 'auto');

        // Cek cache
        $hash = hash('sha256', $to . '|' . $text);
        $row  = $this->CI->db->get_where('translation_cache', ['hash' => $hash])->row_array();
        if ($row) return $row['translated_text'];

        // 1) Coba LibreTranslate (gratis, no key)
        $translated = $this->via_libretranslate($text, $to, $from);

        // 2) Fallback MyMemory (gratis)
        if (($translated === null || $translated === '') && !empty($this->cfg['use_mymemory_fallback'])) {
            $translated = $this->via_mymemory($text, $to, $from === 'auto' ? 'id' : $from);
        }

        // Simpan cache jika sukses; jika gagal, kembalikan teks asli
        if ($translated !== null && $translated !== '') {
            $this->CI->db->insert('translation_cache', [
                'hash'            => $hash,
                'src_lang'        => $from,
                'target_lang'     => $to,
                'original_text'   => $text,
                'translated_text' => $translated,
            ]);
            return $translated;
        }

        return $text;
    }

    public function translateArray(array $arr, $to = null, $from = null)
    {
        $out = [];
        foreach ($arr as $k => $v) {
            $out[$k] = $this->translate($v, $to, $from);
        }
        return $out;
    }

    protected function via_libretranslate($text, $to, $from)
    {
        $endpoints = $this->cfg['libre_endpoints'] ?? [];
        foreach ($endpoints as $base) {
            $url = rtrim($base, '/') . '/translate';
            $data = [
                'q'      => $text,
                'source' => $from,        // 'auto' ok
                'target' => $to,
                'format' => 'text',
            ];
            $resp = $this->curl_post($url, $data, $this->cfg['timeout'] ?? 12);
            if (!$resp) continue;

            $json = json_decode($resp, true);
            if (isset($json['translatedText'])) {
                return $json['translatedText'];
            }
        }
        return null;
    }

    protected function via_mymemory($text, $to, $from)
    {
        // MyMemory GET (tanpa API key). Simple & gratis.
        $url = "https://api.mymemory.translated.net/get?q=" . urlencode($text) . "&langpair={$from}|{$to}";
        $resp = $this->curl_get($url, $this->cfg['timeout'] ?? 12);
        if (!$resp) return null;

        $json = json_decode($resp, true);
        if (isset($json['responseData']['translatedText'])) {
            return $json['responseData']['translatedText'];
        }
        return null;
    }

    protected function curl_post($url, array $fields, $timeout = 10)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($fields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_USERAGENT      => 'CI3-TranslatorFree/1.0',
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp;
    }

    protected function curl_get($url, $timeout = 10)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_USERAGENT      => 'CI3-TranslatorFree/1.0',
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp;
    }
}
