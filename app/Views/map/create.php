<?= $this->extend('layout/template'); ?>

<?= $this->section('css'); ?>
<style>
    #map {
        height: 240px;
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>
<?= $this->endSection(); ?>


<?= $this->section('content'); ?>
<form action="<?= route_to('backend.maps.store'); ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" class="form-control <?= ($validation->hasError('title') ? 'is-invalid' : ''); ?>" id="title" value="<?= (old('title')); ?>">
        <?php if ($validation->hasError('title')) : ?>
            <div class="invalid-feedback">
                <?= $validation->getError('title'); ?>
            </div>
        <?php endif ?>
    </div>
    <div class="mb-3">
        <label for="kecamatan" class="form-label">Kecamatan</label>
        <select id="kecamatan" class="form-select">
            <option selected>Pilih Kecamatan</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="category" class="form-label">Kategori</label>
        <select id="category" class="form-select">
            <?php foreach ($categories as $category) : ?>
                <option value="<?= $category['category_id']; ?>"><?= $category['title']; ?></option>
            <?php endforeach ?>
            <option selected>Pilih Kategori</option>
        </select>
    </div>
    <div id="map"></div>
    <div class="mb-3">
        <label for="latitude" class="form-label">Latitude</label>
        <input type="text" name="latitude" class="form-control <?= ($validation->hasError('latitude') ? 'is-invalid' : ''); ?>" id="latitude" value="<?= (old('latitude')); ?>">
        <?php if ($validation->hasError('latitude')) : ?>
            <div class="invalid-feedback">
                <?= $validation->getError('latitude'); ?>
            </div>
        <?php endif ?>
    </div>
    <div class="mb-3">
        <label for="longitude" class="form-label">Longitude</label>
        <input type="text" name="longitude" class="form-control <?= ($validation->hasError('longitude') ? 'is-invalid' : ''); ?>" id="longitude" value="<?= (old('longitude')); ?>">
        <?php if ($validation->hasError('longitude')) : ?>
            <div class="invalid-feedback">
                <?= $validation->getError('longitude'); ?>
            </div>
        <?php endif ?>
    </div>
    // TODO: Make radiobutton for status
    <div class="mb-3">
        <label for="cover" class="form-label">Cover</label><br>
        <img src="#" height="100" class="img-thumbnail mb-3 img-preview">
        <input class="form-control <?= $validation->hasError('cover') ? 'is-invalid' : ''; ?>" type="file" id="cover" name="cover" onchange="previewImg()">
        <div class="invalid-feedback">
            <?= $validation->getError('cover'); ?>
        </div>
    </div>
    <div class="mb-3">
        <label for="video" class="form-label">Video</label>
        <input type="text" name="video" class="form-control <?= ($validation->hasError('video') ? 'is-invalid' : ''); ?>" id="video" value="<?= (old('video')); ?>">
        <?php if ($validation->hasError('video')) : ?>
            <div class="invalid-feedback">
                <?= $validation->getError('video'); ?>
            </div>
        <?php endif ?>
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label><br>
        <textarea class="form-control <?= ($validation->hasError('address') ? 'is-invalid' : ''); ?>" name="address" cols="30" rows="10"><?= old('address'); ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary my-4">Ubah</button>
    <a href="<?= route_to('backend.popups.index'); ?>" class="btn btn-danger my-4">Cancel</a>
</form>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    const kecamatanSelect = document.getElementById('kecamatan');
    let kecamatanold = '';
    let urlkecamatan = 'https://ibnux.github.io/data-indonesia/kecamatan/1807.json';

    fetch(urlkecamatan)
        .then(response => response.json())
        .then(function(data) {
            return data.map(function(kecamatan) {
                let option = document.createElement("option");
                option.text = kecamatan.nama;
                option.value = kecamatan.nama;
                kecamatanSelect.add(option);
            });
        })
        .catch(function(error) {
            console.log(error);
        });

    function previewImg() {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('.img-preview');
        const fileImage = new FileReader();

        fileImage.readAsDataURL(image.files[0]);
        fileImage.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }

    let map = L.map('map').setView([51.505, -0.09], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);
</script>
<?= $this->endSection(); ?>