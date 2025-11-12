<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'third_party/PHPWord/src/PhpWord/Autoloader.php');
\PhpOffice\PhpWord\Autoloader::register();

class PHPWordLibrary {
    public function __construct() {
        // PHPWord ready to use
    }
}
