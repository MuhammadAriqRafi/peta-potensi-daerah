<?= $this->extend('layout/template'); ?>

<?= $this->section('toolbar'); ?>
<!-- Button trigger modal -->
<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Tambah Data
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Administrator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= route_to('backend.administrators.store'); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" class="form-control <?= $validation->hasError('nik') ? 'is-invalid' : ''; ?>" name="nik" value="<?= old('nik'); ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nik'); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" name="nama" value="<?= old('nama'); ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nama'); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control <?= $validation->hasError('username') ? 'is-invalid' : ''; ?>" name="username" value="<?= old('username'); ?>">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>

<table class="table">
    <thead>
        <th>NIK</th>
        <th>Nama</th>
        <th>Username</th>
        <th>Actions</th>
    </thead>
    <tbody>
        <?php foreach ($administrators as $administrator) : ?>
            <tr>
                <td><?= $administrator['nik']; ?></td>
                <td><?= $administrator['nama']; ?></td>
                <td><?= $administrator['username']; ?></td>
                <td>
                    <form action="<?= route_to('backend.administrators.delete', $administrator['admin_id']); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <a href="<?= route_to('backend.administrators.edit', base64_encode($administrator['admin_id'])); ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah anda yakin?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection(); ?>