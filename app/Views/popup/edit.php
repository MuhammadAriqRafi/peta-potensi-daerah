<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<form>
    <div class="mb-3">
        <label for="title" class="form-label">Email address</label>
        <input type="text" class="form-control" id="title" value="<?= ($popup['title'] ?? old('title')); ?>">
    </div>
    <div class="mb-3">
        <img src="#" class="img-preview">
        <label for="value" class="form-label">Gambar Pop Up</label>
        <input class="form-control" type="file" id="value" name="value" onclick="previewImg()">
    </div>
    <button type="submit" class="btn btn-primary mt-4">Submit</button>
</form>

<script>
    function previewImg() {
        const photo = document.querySelector('#value');
        const imgPreview = document.querySelector('.img-preview');
        const filePhoto = new FileReader();
        filePhoto.readAsDataURL(photo.files[0]);
        filePhoto.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }
</script>
<?= $this->endSection(); ?>