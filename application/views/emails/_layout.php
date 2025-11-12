<?php
$subject    = $_subject   ?? 'SISTA';
$preheader  = $_preheader ?? 'Notifikasi dari SISTA PT LAPI ITB';
$logoUrl    = $_logo_url  ?? base_url('assets/img/main.png');
$logoWidth  = (int)($_logo_width ?? 132);

// Warna yang DIKUNCI untuk area konten (tidak terpengaruh dark mode)
$CARD_BG    = '#ffffff';
$TEXT_COLOR = '#0f172a';
$MUTED      = '#64748b';
$BORDER     = '#eef2f7';

// Aksen (boleh berubah sesuai tema kalau mau)
$PRIMARY = '#0b5ed7';
$ACCENT  = '#22c55e';

// Latar luar (boleh gelap/terang, tapi tidak mempengaruhi card putih)
$PAGE_BG_LIGHT = '#f4f6fb';
$PAGE_BG_DARK  = '#0b1220'; // dipakai pada dark mode hanya untuk area luar
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?= html_escape($subject) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <style>
      body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}
      table,td{mso-table-lspace:0pt;mso-table-rspace:0pt}
      img{-ms-interpolation-mode:bicubic;border:0;outline:none;text-decoration:none;display:block}
      body{margin:0;padding:0;width:100%!important;height:100%!important;background:<?= $PAGE_BG_LIGHT ?>}
      a{color:<?= $PRIMARY ?>;text-decoration:none}

      .container{max-width:640px;margin:24px auto;width:100%}
      .card{
        background:<?= $CARD_BG ?>; /* kunci putih */
        border-radius:16px;overflow:hidden;box-shadow:0 6px 24px rgba(16,24,40,.08)
      }
      .px{padding-left:24px;padding-right:24px}
      .py{padding-top:20px;padding-bottom:20px}
      .muted{color:<?= $MUTED ?>}.small{font-size:13px;line-height:1.5}
      .h1{font:700 22px/1.35 Inter,Segoe UI,Arial,sans-serif;color:<?= $TEXT_COLOR ?>;margin:0 0 6px}
      .divider{height:1px;background:<?= $BORDER ?>}
      .topbar{height:4px;background:linear-gradient(90deg,<?= $PRIMARY ?>, <?= $ACCENT ?>)}
      .brand-wrap{background:<?= $CARD_BG ?>} /* header juga putih */

      .brand-title{font-weight:800;letter-spacing:.2px;color:<?= $TEXT_COLOR ?>}
      .brand-sub{font-weight:600;color:#94a3b8;padding-left:6px}

      .btn{background:<?= $PRIMARY ?>;border-radius:10px}
      .btn a{display:inline-block;padding:12px 18px;color:#ffffff!important;font-weight:600}
      .btn-outline{border:1px solid #dbe7ff;border-radius:10px}
      .btn-outline a{display:inline-block;padding:10px 16px;color:<?= $PRIMARY ?>!important;font-weight:600}

      /* Dark mode: HANYA latar luar yang gelap. Card/teks tetap putih/gelap-kontras. */
      @media (prefers-color-scheme:dark){
        body{background:<?= $PAGE_BG_DARK ?>}
        a{color:<?= $PRIMARY ?>}
      }

      @media screen and (max-width:520px){
        .px{padding-left:18px!important;padding-right:18px!important}
        .py{padding-top:18px!important;padding-bottom:18px!important}
      }
    </style>
  </head>
  <body style="font-family:Inter,Segoe UI,Arial,sans-serif;">
    <!-- Preheader -->
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
      <?= html_escape($preheader) ?>&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center">

          <div class="container">
            <!-- gunakan bgcolor di TABLE untuk kunci putih pada klien bandel -->
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" class="card" bgcolor="#ffffff" style="background-color:#ffffff;">
              <tr><td class="topbar"></td></tr>

              <!-- Header / Brand -->
              <tr>
                <!-- juga tambahkan bgcolor di TD -->
                <td class="brand-wrap px py" bgcolor="#ffffff" style="background-color:#ffffff;">
                  <table role="presentation" width="100%">
                    <tr>
                      <td valign="middle" style="width:<?= $logoWidth ?>px;">
                        <img src="<?= html_escape($logoUrl) ?>"
                             width="<?= $logoWidth ?>"
                             style="width:<?= $logoWidth ?>px;height:auto;max-width:100%;border-radius:6px"
                             alt="SISTA">
                      </td>
                      <td style="padding-left:10px">
                        <div class="brand-title">SISTA <span class="brand-sub">PT LAPI ITB</span></div>
                        <div class="small" style="color:<?= $MUTED ?>;"><?= html_escape($subject) ?></div>
                      </td>
                      <td align="right" class="small" style="white-space:nowrap;color:<?= $MUTED ?>;">
                        <?= date('d M Y') ?>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <tr><td class="divider"></td></tr>

              <!-- Body (kunci warna teks + font fallback) -->
              <tr>
                <td class="px py" bgcolor="#ffffff" style="background-color:#ffffff;color:<?= $TEXT_COLOR ?>;">
                  <div style="color:<?= $TEXT_COLOR ?>; line-height:1.6;">
                    <!--[if mso]><span style="color:<?= $TEXT_COLOR ?>;"><![endif]-->
                    <font color="<?= $TEXT_COLOR ?>">
                      <?= $content ?>
                    </font>
                    <!--[if mso]></span><![endif]-->
                  </div>
                </td>
              </tr>

              <tr><td class="divider"></td></tr>

              <!-- Footer -->
              <tr>
                <td class="px py small" bgcolor="#ffffff" style="background-color:#ffffff;color:<?= $MUTED ?>;">
                  Email ini dikirim otomatis oleh sistem SISTA. Butuh bantuan?
                  balas email ini atau hubungi <a href="mailto:sista.lapi@gmail.com">sista.lapi@gmail.com</a>.
                  <br>© <?= date('Y') ?> PT LAPI ITB.
                </td>
              </tr>
            </table>
          </div>

        </td>
      </tr>
    </table>
  </body>
</html>
