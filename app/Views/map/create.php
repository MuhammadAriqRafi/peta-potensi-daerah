<?= $this->extend('layout/template'); ?>

<?= $this->section('css'); ?>
<style>
    #map {
        height: 240px;
    }
</style>

<!-- Summernote -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>

<form action="<?= route_to('backend.maps.store'); ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <!-- Title Input -->
    <div class="mb-3">
        <label for="title" class="form-label fw-bold">Title</label>
        <input type="text" name="title" class="form-control <?= ($validation->hasError('title') ? 'is-invalid' : ''); ?>" value="<?= (old('title')); ?>">
        <div class="invalid-feedback">
            <?= $validation->getError('title'); ?>
        </div>
    </div>
    <!-- Date Publish Date Input -->
    <div class="mb-3 col-2">
        <label for="date_publish" class="form-label fw-bold">Date Publish</label>
        <input type="date" name="date_publish" class="form-control <?= ($validation->hasError('date_publish') ? 'is-invalid' : ''); ?>" value="<?= (old('date_publish', date("Y-m-d"))); ?>" min="1900-01-01" max="<?= date("Y-12-31"); ?>">
        <div class="invalid-feedback">
            <?= $validation->getError('date_publish'); ?>
        </div>
    </div>
    <!-- Kategori Dropdown -->
    <div class="mb-3">
        <label for="category" class="form-label fw-bold">Kategori</label>
        <select name="category" class="form-select <?= $validation->hasError('category') ? 'is-invalid' : ''; ?>">
            <option value="" hidden>Pilih Kategori</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?= base64_encode($category['category_id']); ?>" <?= old('category') == base64_encode($category['category_id']) ? 'selected' : ''; ?>><?= $category['title']; ?></option>
            <?php endforeach ?>
        </select>
        <div class="invalid-feedback">
            <?= $validation->getError('category'); ?>
        </div>
    </div>
    <!-- Kecamatan Dropdown -->
    <div class="mb-3">
        <label for="kecamatan" class="form-label fw-bold">Kecamatan</label>
        <select name="kecamatan" id="kecamatan" class="form-select <?= $validation->hasError('kecamatan') ? 'is-invalid' : ''; ?>">
            <!-- Data is fetced using ajax request to ibnux github repo -->
            <option value="" hidden>Pilih Kecamatan</option>
        </select>
        <div class="invalid-feedback">
            <?= $validation->getError('kecamatan'); ?>
        </div>
    </div>
    <!-- Map Location -->
    <div id="map" class="mb-3"></div>
    <!-- Latitude Input -->
    <div class="mb-3">
        <label for="latitude" class="form-label fw-bold">Latitude</label>
        <input type="text" name="latitude" id="latitude" class="form-control <?= ($validation->hasError('latitude') ? 'is-invalid' : ''); ?>" value="<?= (old('latitude')); ?>">
        <div class="invalid-feedback">
            <?= $validation->getError('latitude'); ?>
        </div>
    </div>
    <!-- Longitude Input -->
    <div class="mb-3">
        <label for="longitude" class="form-label fw-bold">Longitude</label>
        <input type="text" name="longitude" id="longitude" class="form-control <?= ($validation->hasError('longitude') ? 'is-invalid' : ''); ?>" value="<?= (old('longitude')); ?>">
        <div class="invalid-feedback">
            <?= $validation->getError('longitude'); ?>
        </div>
    </div>
    <!-- Deskripsi Textarea -->
    <div class="mb-3">
        <label for="description" class="form-label fw-bold">Deskripsi</label>
        <textarea id="summernote" name="description"><?= old('description'); ?></textarea>
        <?php if ($validation->hasError('description')) : ?>
            <p class="text-danger"><?= $validation->getError('description'); ?></p>
        <?php endif ?>
    </div>
    <!-- Status Radio Input -->
    <div class="mb-3">
        <label for="status" class="form-label fw-bold">Status</label><br>
        <?php foreach ($statuses as $status) : ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status" value="<?= $status; ?>" <?= old('status') == $status ? 'checked' : ''; ?>>
                <label class="form-check-label" for="<?= $status; ?>"><?= ucfirst($status); ?></label>
            </div>
        <?php endforeach ?>
        <?php if ($validation->hasError('status')) : ?>
            <p class="text-danger mt-2 fs-6"><?= $validation->getError('status'); ?></p>
        <?php endif ?>
    </div>
    <!-- Cover File Input -->
    <div class="mb-3">
        <label for="cover" class="form-label fw-bold">Cover</label><br>
        <img src="#" height="100" class="img-thumbnail mb-3 img-preview">
        <input class="form-control <?= $validation->hasError('cover') ? 'is-invalid' : ''; ?>" type="file" id="cover" name="cover" onchange="previewImg()">
        <div class="invalid-feedback">
            <?= $validation->getError('cover'); ?>
        </div>
    </div>
    <!-- Video Input -->
    <div class="mb-3">
        <label for="youtube" class="form-label fw-bold">Video</label>
        <input type="text" name="youtube" class="form-control <?= ($validation->hasError('youtube') ? 'is-invalid' : ''); ?>" value="<?= (old('youtube')); ?>">
        <?php if ($validation->hasError('youtube')) : ?>
            <div class="invalid-feedback">
                <?= $validation->getError('youtube'); ?>
            </div>
        <?php endif ?>
    </div>
    <!-- Address Textarea -->
    <div class="mb-3">
        <label for="address" class="form-label fw-bold">Address</label><br>
        <textarea class="form-control <?= ($validation->hasError('address') ? 'is-invalid' : ''); ?>" name="address" cols="30" rows="10"><?= old('address'); ?></textarea>
        <div class="invalid-feedback">
            <?= $validation->getError('address'); ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary my-4">Tambah</button>
    <a href="<?= route_to('backend.maps.index'); ?>" class="btn btn-danger my-4">Cancel</a>
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

    let map = L.map('map').setView([-5.2749, 105.6882], 13);
    const latitude = document.getElementById('latitude');
    const longitude = document.getElementById('longitude');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap',
        draggable: true
    }).addTo(map);

    let marker = L.marker(new L.LatLng(-5.2749, 105.6882), {
        draggable: true
    }).addTo(map);

    marker.on('dragend', function(e) {
        latitude.value = marker.getLatLng().lat;
        longitude.value = marker.getLatLng().lng;
    });

    $('#summernote').summernote({
        placeholder: 'Hello stand alone ui',
        tabsize: 2,
        height: 240,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    function previewImg() {
        const image = document.querySelector('#cover');
        const imgPreview = document.querySelector('.img-preview');
        const fileImage = new FileReader();

        fileImage.readAsDataURL(image.files[0]);
        fileImage.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }
</script>
<?= $this->endSection(); ?>