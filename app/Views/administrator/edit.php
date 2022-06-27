<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>

<form action="<?= route_to('backend.administrators.update', $administrator['admin_id']) ?>" method="POST" class="pb-4">
    <?= csrf_field(); ?>
    <input type="hidden" name="_method" value="PATCH">
    <div class="mb-3">
        <label for="nik" class="form-label">NIK</label>
        <input type="text" class="form-control <?= $validation->hasError('nik') ? 'is-invalid' : ''; ?>" name="nik" value="<?= old('nik', $administrator['nik']); ?>">
        <div class="invalid-feedback">
            <?= $validation->getError('nik'); ?>
        </div>
    </div>
    <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" name="nama" value="<?= old('nama', $administrator['nama']); ?>">
        <div class="invalid-feedback">
            <?= $validation->getError('nama'); ?>
        </div>
    </div>
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control <?= $validation->hasError('username') ? 'is-invalid' : ''; ?>" name="username" value="<?= old('username', $administrator['username']); ?>">
        <div class="invalid-feedback">
            <?= $validation->getError('username'); ?>
        </div>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control <?= $validation->hasError('password') ? 'is-invalid' : ''; ?>" name="password">
        <div class="invalid-feedback">
            <?= $validation->getError('password'); ?>
        </div>
    </div>
    <div class="mb-3">
        <label for="passconf" class="form-label">Password Confirm</label>
        <input type="password" class="form-control <?= $validation->hasError('passconf') ? 'is-invalid' : ''; ?>" name="passconf">
        <div class="invalid-feedback">
            <?= $validation->getError('passconf'); ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Ubah</button>
    <a href="<?= route_to('backend.administrators.index'); ?>" class="btn btn-danger">Batal</a>
</form>

<?= $this->endSection(); ?>