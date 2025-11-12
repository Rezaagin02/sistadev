<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>


    <div class="row">
        <div class="col-lg-6">
            <?= $this->session->flashdata('message'); ?>
            <form id="ubahPasswordForm" action="<?= base_url('user/changepassword') ?>" method="POST">
                  <div class="mb-3">
                    <label for="currentPassword" class="form-label">Password Saat Ini</label>
                    <input type="password" class="form-control" name="current_password" id="currentPassword" required>
                  </div>
                  <div class="mb-3">
                    <label for="newPassword" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" name="new_password1" id="newPassword" required>
                  </div>
                  <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" name="new_password2" id="confirmPassword" oninput="checkPasswordMatch()" required>
                    <small id="passwordError" class="mt-1"></small>
                  </div>
                
                <script>

                  function checkPasswordMatch() {
                      var newPassword = document.getElementById('newPassword').value;
                      var confirmPassword = document.getElementById('confirmPassword').value;
                      var passwordError = document.getElementById('passwordError');

                      if (newPassword === confirmPassword) {
                        passwordError.innerHTML = 'Password cocok';
                        passwordError.classList.remove('text-danger');
                        passwordError.classList.add('text-success');
                      } else {
                        passwordError.innerHTML = 'Password tidak cocok';
                        passwordError.classList.remove('text-success');
                        passwordError.classList.add('text-danger');
                      }
                    }
                  
                </script>
                 <button type="submit" class="btn btn-primary">Ubah Password</button>
                </form>

        </div>
    </div>



</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content --> 