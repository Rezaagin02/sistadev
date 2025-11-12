<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card col-lg-8 col-sm-12 col-md-12">
        <div class="card-body">
            <div class="row">
        <div class="col">

            <?= form_open_multipart('user/edit'); ?>
            <div class="form-group row mb-3">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="email" name="email" value="<?= $user['email']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row mb-3">
                <label for="name" class="col-sm-2 col-form-label">Full name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" name="name" value="<?= $user['name']; ?>">
                    <?= form_error('name', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>
            </div>
            <div class="form-group row mb-3">
                <label for="name" class="col-sm-2 col-form-label">Gender</label>
                <div class="col-sm-10">
                    <select class="form-control" name="gender" id="gender">
                        <option value="" disable></option>
                        <option <?php if($user['gender'] == 1 ){ echo 'selected';} ?> value="1">Male</option>
                        <option <?php if($user['gender'] == 2 ){ echo 'selected';} ?> value="2">Female</option>
                        <option <?php if($user['gender'] == 3 ){ echo 'selected';} ?> value="3">Prefer not to say</option>
                        <option <?php if($user['gender'] == 4 ){ echo 'selected';} ?> value="4">Other</option>
                    </select>
                    <?= form_error('name', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>
            </div>
            <div class="form-group row mb-3">
                <div class="col-sm-2">Picture</div>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-sm-3">
                            <img src="<?= base_url('assets/img/profile/') . $user['image']; ?>" class="img-thumbnail">
                        </div>
                        <div class="col-sm-9">
                            <div class="custom-file">
                                <input id="formFile" type="file" class="form-control" id="image" name="image">
                                <!-- <label id="formFile" for="image">Choose file</label> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       


            
        </div>
    </div>
</div>
</div>

<div class="card col-lg-8">
    <div class="row">
        <div class="">
            <div class="card-header">
                <h5>Personal Information</h5>
            </div>
            <div class="card-body">
            
                <div class="form-group row mb-3 mt-4">
                    <input type="hidden" value="<?= $user['id'] ?>">
                <label for="email" class="col-sm-2 col-form-label">Address</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="address" name="address" value="<?= $user['address']; ?>">
                    </div>
                </div>
                <div class="form-group row mb-3 mt-4">
                <label for="email" class="col-sm-2 col-form-label">Instagram</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="instagram" name="instagram" value="<?= $user['instagram']; ?>">
                    </div>
                </div>
                <div class="form-group row mb-3 mt-4">
                <label for="email" class="col-sm-2 col-form-label">Facebook</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="facebook" name="facebook" value="<?= $user['facebook']; ?>">
                    </div>
                </div>
                <div class="form-group row mb-3 mt-4">
                <label for="email" class="col-sm-2 col-form-label">Twitter</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="twitter" name="twitter" value="<?= $user['twitter']; ?>">
                    </div>
                </div>
                <div class="form-group row mb-3">
                <label for="email" class="col-sm-2 col-form-label">No. WhatsApp</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="no_wa" name="no_wa" value="<?php if ($user['no_wa'] == 0){ echo '';} else { echo $user['no_wa'];}; ?>">
                    </div>
                </div>
                <div class="form-group row justify-content-end">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="<?= base_url('user') ?>" class="btn btn-danger">Discard</a>
                    </div>
                </div>
            </form>

           


        </div>
    </div>
   
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content --> 