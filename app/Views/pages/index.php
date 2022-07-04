<?= $this->extend('layout/frontendTemplate'); ?>

<?= $this->section('content'); ?>

<div class="container d-flex align-items-center vh-100">

    <div class="container">
        <div class="container w-50 d-flex justify-content-between align-items-center">
            <h1 class="w-25">Beranda</h1>
            <a href="<?= route_to('login.index'); ?>" class="btn btn-outline-primary">Login</a>
        </div>
        <div class="container w-50 pt-3">
            <?= $this->include('layout/flashMessageAlert'); ?>

            <h3 class="my-3">Kontak Kami</h1>
                <form action="#" method="POST">
                    <?= csrf_field(); ?>

                    <div class="mb-3 row">
                        <label for="name" class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control <?= $validation->hasError('name') ? 'is-invalid' : ''; ?>" name="name" value="<?= old('name'); ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('name'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control <?= $validation->hasError('email') ? 'is-invalid' : ''; ?>" name="email" value="<?= old('email'); ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('email'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="title" class="col-sm-2 col-form-label">Judul</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control <?= $validation->hasError('title') ? 'is-invalid' : ''; ?>" name="title" value="<?= old('title'); ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('title'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="messages" class="col-sm-2 col-form-label">Pesan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control <?= $validation->hasError('messages') ? 'is-invalid' : ''; ?>" name="messages" rows="3"><?= old('messages'); ?></textarea>
                            <div class="invalid-feedback">
                                <?= $validation->getError('messages'); ?>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>