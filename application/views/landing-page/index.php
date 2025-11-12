<!DOCTYPE html>
<html>
  <head>
    <!-- Basic -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Mobile Metas -->
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <!-- Site Metas -->
    <link rel="icon" href="<?= base_url('/') ?>assets/images/logos/itb_lab_color_potrait.png" type="image/gif" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>ITB Lab Digital</title>

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/landing-page/') ?>css/bootstrap.css" />

    <!-- fonts style -->
    <link
      href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap"
      rel="stylesheet"
    />

    <!-- font awesome style -->
    <link href="<?= base_url('assets/landing-page/') ?>css/font-awesome.min.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="<?= base_url('assets/landing-page/') ?>css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="<?= base_url('assets/landing-page/') ?>css/responsive.css" rel="stylesheet" />
    <style>
      .count-up {
        animation: countUp 2s ease-in-out;
      }

      @keyframes countUp {
        from {
          opacity: 0;
        }
        to {
          opacity: 1;
        }
      }
    </style>
  </head>

  <body>
    <div class="hero_area">
      <!-- header section strats -->
      <header class="header_section long_section px-0">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="navbar-brand" href="">
            <span> <img src="<?= base_url('/assets/images/logos/itb_lab_color.png') ?>" width="80" height="45.34" alt="">  </span>
          </a>
          <button
            class="navbar-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div
              class="d-flex mx-auto flex-column flex-lg-row align-items-center"
            ></div>
            <div class="quote_btn-container">
              <?php if($this->session->userdata('email') == NULL){ ?>
                <a href="<?= base_url('auth') ?>">
                  <span> Login </span>
                  <i class="fa fa-user" aria-hidden="true"></i>
                </a>
              <?php } else { ?>
                <a href="<?= base_url('user') ?>">
                  <span> <?= $this->session->userdata('username')  ?> </span>
                  <i class="fa fa-user" aria-hidden="true"></i>
                </a>
              <?php } ?>
              <!-- <form class="form-inline">
                <button class="btn my-2 my-sm-0 nav_search-btn" type="submit">
                  <i class="fa fa-search" aria-hidden="true"></i>
                </button>
              </form> -->
            </div>
          </div>
        </nav>
      </header>
      <!-- end header section -->
      <!-- slider section -->
      <section class="slider_section long_section" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
    url('https://www.itblab.ptlapiitb.co.id/assets/landing-page/images/hero.jpg') !important;">
        <div id="customCarousel" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <div class="container">
                <div class="row">
                  <div class="col">
                    <div class="detail-box text-center text-white">
                      <img src="<?= base_url('/assets/images/logos/itb_lab_light.png') ?>" alt="" class="img-fluid">
                      <!-- <h1>
                        ITB LAB <br />
                        DIGITAL
                      </h1> -->
                      <p>
                        Sistem Informasi Laboratorium Institut Teknologi Bandung
                      </p>

                      <a href="#contact" class="btn btn-danger mt-1"> Contact Us </a>
                      <?php if($this->session->userdata('email') == NULL){ ?>
                        <a href="<?= base_url('auth') ?>" class="btn btn-primary mt-1"><i class="fa fa-sign-in"></i> Log In</a>
                      <?php } else { ?>
                        <a href="<?= base_url('admin') ?>" class="btn btn-primary mt-1"><i class="fa fa-tachometer"></i> Dashboard</a>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- end slider section -->
    </div>

    <section>
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="card p-4 mb-5 mt-5 bg-primary text-white">
              <div class="row">
                <div class="col-lg-3 text-center">
                  <h1 id="facultyCount" class="count-up">0</h1>
                  <h5><b>Fakultas</b></h5>
                </div>
                <div class="col-lg-3 text-center">
                  <h1 id="programCount" class="count-up">0</h1>
                  <h5><b>Program Studi</b></h5>
                </div>
                <div class="col-lg-3 text-center">
                  <h1 id="labCount" class="count-up">0</h1>
                  <h5><b>Laboratorium</b></h5>
                </div>
                <div class="col-lg-3 text-center">
                  <h1 id="facilityCount" class="count-up">0</h1>
                  <h5><b>Fasilitas</b></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- about section -->

    <section class="about_section layout_padding long_section">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="img-box">
              <img src="<?= base_url('assets/images/backgrounds/lapi.jpg') ?>" alt="" />
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-box">
              <div class="heading_container">
                <h2>About Us</h2>
              </div>
              <p>
                PT LAPI ITB merupakan salah satu unit usaha yang dimiliki oleh
                Institut Teknologi Bandung. Berdiri sejak 2004, PT LAPI ITB
                telah memberikan layanan profesional bagi Pemerintah Indonesia,
                Badan Usaha Milik Daerah dan Nasional Indonesia serta Perusahaan
                Swasta Nasional & Internasional.
              </p>
              <a href="https://www.lapi-itb.com/"> Read More </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- end about section -->

    <!-- contact section -->
    <section class="contact_section long_section" id="contact">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="form_container">
              <div class="heading_container">
                <h2>Contact Us</h2>
              </div>
              <form action="<?= base_url('welcome/contact') ?>" method="POST">
                <div>
                  <input type="text" placeholder="Your Name" name="name" disabled />
                </div>
                <div>
                  <input type="text" placeholder="Phone Number" name="no_telp" disabled />
                </div>
                <div>
                  <input type="email" placeholder="Email" name="email" disabled />
                </div>
                <div>
                  <input
                    type="text"
                    class="message-box"
                    placeholder="Message"
                    name="message"
                    disabled
                  />
                </div>
                <div class="btn_box">
                  <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Under Construction">
                    <button type="submit" disabled>SEND</button>
                  </span>
                </div>
              </form>
            </div>
          </div>
          <div class="col-md-6">
            <div class="map_container">
              <div class="map">
                <div id="googleMap"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- end contact section -->

    <!-- footer section -->
    <footer class="footer_section">
      <div class="container">
        <p>
          &copy; <span id="displayYear"></span> All Rights Reserved By
          <a href="">Dept. TIK</a>
          Distribution <a href="">PT LAPI ITB</a>
        </p>
      </div>
    </footer>
    <!-- footer section -->

    <?php 

      $query = $this->db->get('faculties');
      $fac = $query->num_rows();

      $query1 = $this->db->get('majors');
      $maj = $query1->num_rows();

      $query2 = $this->db->get('laboratories');
      $lab = $query2->num_rows();

      $query3 = $this->db->get('tools');
      $tool = $query3->num_rows();

    ?>

    <!-- jQery -->
    <script src="<?= base_url('assets/landing-page/') ?>js/jquery-3.4.1.min.js"></script>
    <!-- bootstrap js -->
    <script src="<?= base_url('assets/landing-page/') ?>js/bootstrap.js"></script>
    <!-- custom js -->
    <script src="<?= base_url('assets/landing-page/') ?>js/custom.js"></script>
    <!-- Google Map -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCh39n5U-4IoWpsVGUHWdqB6puEkhRLdmI&callback=myMap"></script>
    <!-- End Google Map -->
    <script>
      function animateValue(id, start, end, duration) {
        if (start === end) return;
        var range = end - start;
        var current = start;
        var increment = end > start ? 1 : -1;
        var stepTime = Math.abs(Math.floor(duration / range));
        var obj = document.getElementById(id);
        var timer = setInterval(function () {
          current += increment;
          obj.textContent = current;
          if (current === end) {
            clearInterval(timer);
          }
        }, stepTime);
      }

      // Set the values to be counted up
      var facultyCount = <?= $fac ?>;
      var programCount = <?= $maj ?>;
      var labCount = <?= $lab ?>;
      var facilityCount = <?= $tool ?>;

      // Animate the values
      animateValue("facultyCount", 0, facultyCount, 2000);
      animateValue("programCount", 0, programCount, 2000);
      animateValue("labCount", 0, labCount, 2000);
      animateValue("facilityCount", 0, facilityCount, 2000);

      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })
    </script>

    
  </body>
</html>
