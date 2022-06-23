<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>

<form action="<?= route_to('backend.popups.update', $popup['popup_id']); ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <input type="hidden" name="_method" value="PATCH">
    <input type="hidden" name="oldImage" value="<?= $popup['value']; ?>">
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" class="form-control <?= ($validation->hasError('title') ? 'is-invalid' : ''); ?>" value="<?= (old('title', $popup['title'])); ?>">
        <div class="invalid-feedback">
            <?= $validation->getError('title'); ?>
        </div>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Gambar Pop Up</label><br>
        <img src="<?= base_url('img/' . $popup['value']); ?>" class="img-preview mb-3 img-thumbnail" width="150">
        <input class="form-control <?= ($validation->hasError('image') ? 'is-invalid' : ''); ?>" type="file" id="image" name="image" onchange="previewImg()">
        <div class="invalid-feedback">
            <?= $validation->getError('image'); ?>
        </div>
    </div>
    <button type="submit" class="btn btn-primary my-4">Ubah</button>
    <a href="<?= route_to('backend.popups.index'); ?>" class="btn btn-danger my-4">Cancel</a>
</form>

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