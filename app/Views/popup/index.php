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
                        <label for="title" class="form-label">Judul</label>
                        <input type="text" class="form-control <?= $validation->hasError('title') ? 'is-invalid' : ''; ?>" name="title" autofocus value="<?= old('title'); ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('title'); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Pop Up</label><br>
                        <img src="#" height="100" class="img-thumbnail mb-3 img-preview">
                        <input class="form-control <?= $validation->hasError('image') ? 'is-invalid' : ''; ?>" type="file" id="image" name="image" onchange="previewImg()">
                        <div class="invalid-feedback">
                            <?= $validation->getError('image'); ?>
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

<div class="row">
    <div class="col-8">
        <?= $this->include('layout/flashMessageAlert'); ?>

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
                        <td><?= $popup['value'] ?? '-'; ?></td>
                        <td>
                            <form action="<?= route_to('backend.popups.delete', $popup['popup_id']); ?>" method="POST">
                                <input type="hidden" name="_method" value="DELETE">
                                <a href="<?= route_to('backend.popups.edit', base64_encode($popup['popup_id'])); ?>" class="btn btn-sm btn-warning">Ubah</a>
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('apakah anda yakin?');">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="col-4">
        <img src="<?= base_url('img/' . $popups[3]['value']); ?>" alt="" width="100%">
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam neque similique et rem dolorum sint repellat, aut eum eveniet non?</p>
        <form action="#" method="POST">
            <!-- Active Pop Up Dropdown -->
            <div class="mb-3">
                <label for="status" class="form-label fw-bold">Pop Up Active</label>
                <select name="status" class="form-select <?= $validation->hasError('status') ? 'is-invalid' : ''; ?>">
                    <?php foreach ($popups as $popup) : ?>
                        <option value="<?= base64_encode($popup['status']); ?>" <?= old('status') == base64_encode($popup['status']) ? 'selected' : ''; ?>><?= $popup['title']; ?></option>
                    <?php endforeach ?>
                </select>
                <div class="invalid-feedback">
                    <?= $validation->getError('status'); ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    function previewImg() {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('.img-preview');
        const fileImage = new FileReader();

        fileImage.readAsDataURL(image.files[0]);
        fileImage.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }
</script>
<?= $this->endSection(); ?>