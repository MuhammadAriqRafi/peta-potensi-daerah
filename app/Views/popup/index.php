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
                <h5 class="modal-title" id="exampleModalLabel">Tambah Pop Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= route_to('backend.popups.store'); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="value" class="form-label">Gambar Pop Up</label>
                        <input class="form-control" type="file" id="value" name="value">
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
<div class="row">
    <div class="col-8">
        <!-- Flash Message -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <table class="table table-striped table-dark">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Judul</th>
                    <th scope="col">Gambar Pop Up</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1 ?>
                <?php foreach ($popups as $popup) : ?>
                    <tr>
                        <th scope="row"><?= $i++; ?></th>
                        <td><?= $popup['title']; ?></td>
                        <td><?= $popup['value']; ?></td>
                        <td>
                            <form action="<?= route_to('backend.popups.delete', $popup['popup_id']); ?>" method="POST">
                                <a href="<?= route_to('backend.popups.edit', base64_encode($popup['popup_id'])); ?>" class="btn btn-sm btn-warning">Ubah</a>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('apakah anda yakin?');">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="col-4">
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam neque similique et rem dolorum sint repellat, aut eum eveniet non?</p>
    </div>
</div>
<?= $this->endSection(); ?>