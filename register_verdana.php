<?php
require_once 'application/third_party/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use Dompdf\Adapter\CPDF;
use Dompdf\Canvas;

use Dompdf\FontMetrics;

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$fontDir = APPPATH . 'third_party/dompdf/vendor/dompdf/dompdf/lib/fonts/verdana/';
$fontName = 'verdana';

$fontMetrics = new \Dompdf\FontMetrics($dompdf->getCanvas(), $options);

$fontMetrics->registerFont(
    $fontName,
    $fontDir . 'Verdana.ttf',
    $fontDir . 'Verdana-Bold.ttf',
    $fontDir . 'Verdana-Italic.ttf',
    $fontDir . 'Verdana-BoldItalic.ttf'
);

echo 'Font Verdana registered!';
