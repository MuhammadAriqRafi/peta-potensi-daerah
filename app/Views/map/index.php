<?= $this->extend('layout/template'); ?>

<?= $this->section('css'); ?>
<style>
    #map {
        height: 240px;
    }
</style>

<!-- Summernote -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>
<?= $this->endSection(); ?>

<?= $this->section('modal'); ?>
<!-- Put this part before </body> tag -->
<input type="checkbox" id="mapModal" class="modal-toggle" />
<div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box sm:p-8">
        <h3 id="mapModalLabel" class="font-bold text-2xl sm:text-4xl mb-6">Tambah Map</h3>

        <!-- Popup Form -->
        <form action="#" id="mapForm" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <!-- Title Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Title</span>
                <input type="text" class="input input-bordered w-full max-w-xs my-2" name="title" />
                <div id="error-title" class="badge badge-error hidden"></div>
            </div>
            <!-- Date Publish Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Date Publish</span>
                <input type="date" class="input input-bordered w-full max-w-xs my-2" name="date_publish" value="<?= (old('date_publish', date("Y-m-d"))); ?>" min="1900-01-01" max="<?= date("Y-12-31"); ?>" />
                <div id="error-date_publish" class="badge badge-error hidden"></div>
            </div>
            <!-- Kategori Dropdown -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold mb-2">Kategori</span>
                <select name="category" class="select select-bordered w-full max-w-xs my-2">
                    <option value="" hidden>Pilih Kategori</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category['category_id']; ?>" <?= old('category') == $category['category_id'] ? 'selected' : ''; ?>><?= $category['title']; ?></option>
                    <?php endforeach ?>
                </select>
                <div id="error-category" class="badge badge-error hidden"></div>
            </div>
            <!-- Kecamatan Dropdown -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold mb-2">Kecamatan</span>
                <select name="kecamatan" id="kecamatan" class="select select-bordered w-full max-w-xs my-2">
                    <option value="" hidden>Pilih Kecamatan</option>
                </select>
                <div id="error-kecamatan" class="badge badge-error hidden"></div>
            </div>
            <!-- Map Location -->
            <div id="map" class="mb-3"></div>
            <!-- Latitude Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Latitude</span>
                <input type="text" class="input input-bordered w-full max-w-xs my-2" name="latitude" id="latitude" />
                <div id="error-latitude" class="badge badge-error hidden"></div>
            </div>
            <!-- Longitude Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Longitude</span>
                <input type="text" class="input input-bordered w-full max-w-xs my-2" name="longitude" id="longitude" />
                <div id="error-longitude" class="badge badge-error hidden"></div>
            </div>
            <!-- Description Textarea -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold mb-2">Description</span>
                <textarea id="summernote" name="description"><?= old('description'); ?></textarea>
                <div id="error-description" class="badge badge-error hidden mt-2"></div>
            </div>
            <!-- Status Radio Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Status</span>
                <div class="flex items-center gap-4 my-2">
                    <?php foreach ($statuses as $status) : ?>
                        <div class="flex items-center gap-4">
                            <input type="radio" name="status" class="radio" value="<?= $status; ?>" />
                            <label for="<?= $status; ?>"><?= ucfirst($status); ?></label>
                        </div>
                    <?php endforeach ?>
                </div>
                <div id="error-status" class="badge badge-error hidden"></div>
            </div>
            <!-- Cover Input File -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Cover</span>
                <img src="#" height="100" class="img-thumbnail img-preview mb-2">
                <input type="file" id="cover" class="input input-bordered w-full max-w-xs my-2" name="cover" onchange="previewImg()" accept="image/jpg, image/jpeg, image/png" />
                <div id="error-cover" class="badge badge-error hidden"></div>
            </div>
            <!-- Video Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Video</span>
                <input type="text" class="input input-bordered w-full max-w-xs my-2" name="youtube" />
                <div id="error-youtube" class="badge badge-error hidden"></div>
            </div>
            <!-- Address Textarea -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Address</span>
                <textarea class="textarea textarea-bordered my-2" name="address"></textarea>
                <div id="error-address" class="badge badge-error hidden"></div>
            </div>


            <!-- Modal Action Buttons -->
            <div class="modal-action">
                <label for="mapModal" class="btn btn-error">Batal</label>
                <label id="mapFormActionBtn" class="btn btn-primary" onclick="save()">Tambah</label>
            </div>
        </form>
        <!-- End of Popup Form -->
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<!-- The button to open modal -->
<label for="mapModal" class="btn modal-button" onclick="create()">Tambah Data</label>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>
<table class="table table-zebra w-full" id="mapTable">
    <thead>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Author</th>
            <th>Date Publish</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    <?php

    $latitude = json_encode(old('latitude', -5.2749));
    $longitude = json_encode(old('longitude', 105.6882));

    ?>

    const mapTable = 'mapTable';
    const mapModal = 'mapModal';
    const mapFormActionBtn = 'mapFormActionBtn';
    const mapModalLabel = 'mapModalLabel';
    const mapForm = 'mapForm';
    const tableId = 'mapTable';

    const latitude = $('#latitude')[0];
    const longitude = $('#longitude')[0];
    const kecamatanSelect = $('#kecamatan')[0];
    let urlkecamatan = 'https://ibnux.github.io/data-indonesia/kecamatan/1807.json';
    let map = L.map('map').setView([<?= $latitude; ?>, <?= $longitude; ?>], 13);

    // Helper
    const previewImg = () => {
        const image = document.querySelector('#cover');
        const imgPreview = document.querySelector('.img-preview');
        const fileImage = new FileReader();

        fileImage.readAsDataURL(image.files[0]);
        fileImage.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }

    const setFormState = (state, id = null) => {
        const capitalizedStateFirstLetter = state.charAt(0).toUpperCase() + state.slice(1);

        resetInvalidClass($(`#${mapForm}`));

        if (capitalizedStateFirstLetter == 'Tambah') {
            // ? Set form action for 'Tambah'
            $(`#${mapForm}`).attr('action', siteUrl + '<?= $storeUrl; ?>');

            // ? If form current state is already 'Tambah'
            if ($('input[name="_method"]').length < 1) {
                return true;
            } else {
                $('input[name="_method"]').remove();
                $(`#${mapFormActionBtn}`).text(capitalizedStateFirstLetter);
                $(`#${mapModalLabel}`).text(`${capitalizedStateFirstLetter} Tentang Aplikasi`);
                $(`#${mapForm}`).trigger('reset');
                $('#summernote').summernote('reset');
            }
        } else if (capitalizedStateFirstLetter == 'Ubah') {
            // ? Set form action for 'Ubah'
            $(`#${mapForm}`).attr('action', siteUrl + '<?= $updateUrl; ?>' + id);

            // ? If form current state is already 'Ubah'
            if ($('input[name="_method"]').length > 1) {
                return true;
            } else {
                $(`#${mapForm}`).prepend(`
                    <input type="hidden" name="_method" value="PATCH">
                `);
                $(`#${mapFormActionBtn}`).text(capitalizedStateFirstLetter);
                $(`#${mapModalLabel}`).text(`${capitalizedStateFirstLetter} Tentang Aplikasi`);
            }
        }
    }

    const displayError = (inputError) => {
        inputError.forEach(error => {
            let input = $(`[name=${error.input_name}]`);

            if (input.attr('type') != 'radio') input.val('');

            $(`#error-${error.input_name}`).removeClass('hidden');
            $(`#error-${error.input_name}`).text(error.error_message);

            switch (error.input_name) {
                case 'description':
                    $('#summernote').summernote('reset');
                    break;
                case 'status':
                    input.addClass('radio-error');
                    break;
                default:
                    if (input.prop('tagName').toLowerCase() == 'textarea') input.addClass('textarea-error');
                    else if (input.prop('tagName').toLowerCase() == 'select') input.addClass('select-error');
                    else input.addClass('input-error');
                    break;
            }
        });
    }

    // CRUD
    const create = () => {
        setFormState('Tambah');
    }

    const store = (data) => {
        const url = $(`#${mapForm}`).attr('action');

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
                    reload(tableId);
                    $(`#${mapForm}`).trigger('reset');
                    $('#summernote').summernote('reset');
                    $('.img-preview').attr('src', '#');
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            }
        });
    }

    const edit = (id) => {
        const url = siteUrl + '<?= $editUrl; ?>' + id;

        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function(response) {
                setFormState('Ubah', id);
                const others = JSON.parse(response.others);

                // ? Filling the form
                $('input[name="title"]').val(response.title);
                $('input[name="youtube"]').val(others.youtube);
                $('input[name="latitude"]').val(others.latitude);
                $('textarea[name="address"]').val(others.address);
                $('input[name="longitude"]').val(others.longitude);
                $('#summernote').summernote('code', others.description);
                $('input[name="date_publish"]').val(response.date_publish);
                $(`option[value="${response.kecamatan}"]`).prop('selected', true);
                $(`option[value="${btoa(response.category_id)}"]`).prop('selected', true);
                $(`input[name="status"][value="${response.status}"]`).prop('checked', true);
                if (response.image) $('.img-preview').attr('src', baseUrl + `/img/${response.image}`);

                $(`#${mapModal}`).prop('checked', true);
            }
        });
    };

    const update = (data) => {
        const url = $(`#${mapForm}`).attr('action');

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
                    reload(tableId);
                    $('#summernote').summernote('reset');
                    $(`#${mapModal}`).prop('checked', false);
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            }
        });
    }

    const save = () => {
        const form = $(`#${mapForm}`)[0];
        const data = new FormData(form);

        if ($('input[name="_method"]').length > 0) update(data);
        else store(data);
    }

    const destroy = (id) => {
        if (confirm('Apakah anda yakin?')) {
            let url = siteUrl + '<?= $destroyUrl; ?>' + id;

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _method: 'DELETE'
                },
                dataType: "json",
                success: function(response) {
                    if (response.status) alert(response.message);
                    reload(tableId);
                },
            });
        }
    }

    $(document).ready(function() {
        const table = createDataTable(mapTable, siteUrl + '<?= $indexUrl; ?>', [{
                name: 'title',
                data: 'title'
            },
            {
                name: 'category',
                data: 'category'
            },
            {
                name: 'author',
                data: 'author'
            },
            {
                name: 'date_publish',
                data: 'date_publish'
            },
            {
                name: 'status',
                data: 'status'
            },
            {
                data: 'post_id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return editDeleteBtn(data);
                }
            },
        ]);

        // ? Fetch kecamatan data from https://ibnux.github.io/data-indonesia/kecamatan/1807.json
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

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            draggable: true
        }).addTo(map);

        let marker = L.marker(new L.LatLng(<?= $latitude; ?>, <?= $longitude; ?>), {
            draggable: true
        }).addTo(map);

        marker.on('dragend', function(e) {
            latitude.value = marker.getLatLng().lat;
            longitude.value = marker.getLatLng().lng;
        });

        $('#summernote').summernote({
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
    });
</script>
<?= $this->endSection(); ?>