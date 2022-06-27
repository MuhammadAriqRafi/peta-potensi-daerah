<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>

<form action="<?= route_to('backend.maps.categories.update', $category['category_id']); ?>" method="POST" enctype="multipart/form-data" class="pb-4">
    <?= csrf_field(); ?>
    <input type="hidden" name="_method" value="PATCH">
    <input type="hidden" name="oldImage" value="<?= $category['image']; ?>">

    <!-- Title Input -->
    <div class="mb-3">
        <label for="title">Title</label>
        <input type="text" name="title" class="form-control <?= $validation->hasError('title') ? 'is-invalid' : ''; ?>" value="<?= old('title', $category['title']); ?>">
        <div class="invalid-feedback">
            <?= $validation->getError('title'); ?>
        </div>
    </div>
    <!-- Description Textarea -->
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" cols="30" rows="10" class="form-control <?= $validation->hasError('description') ? 'is-invalid' : ''; ?>"><?= old('description', $category['description']); ?></textarea>
        <div class="invalid-feedback">
            <?= $validation->getError('description'); ?>
        </div>
    </div>
    <!-- Image File Input -->
    <div class="mb-5">
        <label for="image" class="form-label">Image</label><br>
        <img src="<?= base_url('img/' . $category['image']); ?>" height="100" class="img-thumbnail mb-3 img-preview">
        <input class="form-control <?= $validation->hasError('image') ? 'is-invalid' : ''; ?>" type="file" id="image" name="image" onchange="previewImg()">
        <div class="invalid-feedback">
            <?= $validation->getError('image'); ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Ubah</button>
    <a href="<?= route_to('backend.maps.categories.index'); ?>" class="btn btn-danger">Batal</a>
</form>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    function previewImg() {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('.img-preview');
        const filePhoto = new FileReader();
        filePhoto.readAsDataURL(image.files[0]);
        filePhoto.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }
</script>
<?= $this->endSection(); ?>