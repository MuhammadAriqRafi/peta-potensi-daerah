<?= $this->extend('layout/template'); ?>

<?= $this->section('toolbar'); ?>
<!-- Button trigger modal -->
<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPopupModal">
    Tambah Data
</button>

<!-- Modal -->
<div class="modal fade" id="addPopupModal" tabindex="-1" aria-labelledby="addPopupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPopupModalLabel">Tambah Pop Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- TODO: Build ajaxStore for popups -->
            <form method="POST" enctype="multipart/form-data" id="addPopupForm">
                <div class="modal-body">
                    <div class="mb-3" onclick="resetInvalidClass(this)">
                        <label for="title" class="form-label">Judul</label>
                        <input type="text" class="form-control" name="title" autofocus>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3" onclick="resetInvalidClass(this)">
                        <label for="image" class="form-label">Gambar Pop Up</label><br>
                        <img src="#" height="100" class="img-thumbnail mb-3 img-preview">
                        <input class="form-control" type="file" id="image" name="image" onchange="previewImg()" accept="image/jpg, image/jpeg, image/png">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="store()">Tambah</button>
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

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Judul</th>
                    <th scope="col">Gambar Pop Up</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($popups as $popup) : ?>
                    <tr>
                        <td><?= $popup['title']; ?></td>
                        <td><?= $popup['value'] ?? '-'; ?></td>
                        <td>
                            <form action="<?= route_to('backend.popups.delete', $popup['popup_id']); ?>" method="POST">
                                <input type="hidden" name="_method" value="DELETE">
                                <a href="<?= route_to('backend.popups.edit', base64_encode($popup['popup_id'])); ?>" class="btn btn-sm btn-outline-warning">Ubah</a>
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('apakah anda yakin?');">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="col-4">
        <img src="<?= base_url('img/' . $currentActivePopup['value']); ?>" alt="" width="100%">
        <form action="<?= route_to('backend.popups.statuses.update'); ?>" method="POST">
            <?= csrf_field(); ?>
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="oldActivePopup" value="<?= base64_encode($currentActivePopup['popup_id']); ?>">
            <!-- Active Pop Up Dropdown -->
            <div class="mb-3">
                <label for="status" class="form-label fw-bold">Pop Up Active</label>
                <select name="status" class="form-select <?= $validation->hasError('status') ? 'is-invalid' : ''; ?>">
                    <?php foreach ($popups as $popup) : ?>
                        <option value="<?= base64_encode($popup['popup_id']); ?>" <?= old('status', $currentActivePopup['popup_id']) == $popup['popup_id'] ? 'selected' : ''; ?>><?= $popup['title']; ?></option>
                    <?php endforeach ?>
                </select>
                <div class="invalid-feedback">
                    <?= $validation->getError('status'); ?>
                </div>
            </div>

            <button type="submit" class="btn btn-sm btn-outline-primary">Ubah</button>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    const titleField = $('#title');
    const imageField = $('#image');

    const previewImg = () => {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('.img-preview');
        const fileImage = new FileReader();

        fileImage.readAsDataURL(image.files[0]);
        fileImage.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }

    const store = () => {
        const form = $('#addPopupForm')[0];
        const data = new FormData(form);
        const url = siteUrl + '<?= $storeUrl; ?>';

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    alert(response.message);
                    $('tbody').prepend(`
                        <tr>
                            <td>${response.data.title}</td>
                            <td>${response.data.value}</td>
                            <td>${response.data.actions ?? '-'}</td>
                        </tr>
                        `);
                    $('#addPopupForm').trigger('reset');
                    $(`[name="image"]`).prev().attr('src', '');
                } else {
                    response.input_error.forEach(error => {
                        $(`[name="${error.input_name}"]`).addClass('is-invalid');
                        $(`[name="${error.input_name}"]`).next().text(error.error_message);
                        if (error.input_name == 'image') $(`[name="${error.input_name}"]`).prev().attr('src', '');
                    });
                }
            }
        });
    }
</script>
<?= $this->endSection(); ?>