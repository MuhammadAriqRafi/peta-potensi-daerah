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
        <div id="mapModalHeader" class="flex justify-between mb-10">
            <h3 id="mapModalLabel" class="font-bold text-2xl sm:text-4xl">Tambah Map</h3>
        </div>

        <!-- Popup Form -->
        <form action="#" id="mapForm" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <div id="map" class="mb-3"></div>
            <!-- Kecamatan Dropdown -->
            <!-- <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold mb-2">Kecamatan</span>
                <select name="kecamatan" id="kecamatan" class="select select-bordered w-full max-w-xs my-2">
                    <option value="" hidden>Pilih Kecamatan</option>
                </select>
                <div id="error-kecamatan" class="badge badge-error hidden"></div>
            </div> -->
        </form>
        <!-- End of Popup Form -->

        <!-- Gallery -->
        <div id="indexGalleryContainer"></div>
        <!-- End of Gallery -->
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<div class="btn-group">
    <a href="<?= route_to('backend.maps.categories.index') ?>" class="btn btn-info mr-2">Category</a>
    <!-- The button to open modal -->
    <label for="mapModal" class="btn modal-button" onclick="create()">Tambah Data</label>
</div>
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
    const mapModalContentContainer = 'modal-box';
    const mapModalHeader = 'mapModalHeader';
    const mapFormActionBtn = 'mapFormActionBtn';
    const mapModalLabel = 'mapModalLabel';
    const mapForm = 'mapForm';
    const tableId = 'mapTable';

    const indexGalleryBtn = 'indexGalleryBtn';
    const indexGalleryBackBtn = 'indexGalleryBackBtn';
    const indexGalleryContainer = 'indexGalleryContainer';

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
                $(`#${mapModalLabel}`).text(`${capitalizedStateFirstLetter} Map`);
                $(`#${mapForm}`).trigger('reset');
                $('#summernote').summernote('reset');
                $(`#${indexGalleryBtn}`).remove();
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

                // ? Append galeri foto button
                $(`#${indexGalleryBtn}`).remove();
                $(`#${mapModalHeader}`).append(renderIndexGalleryBtn(id));
                $(`#${mapModalHeader}`).append(renderIndexGalleryBackBtn(id));
                $(`#${indexGalleryBackBtn}`).hide();

                $(`#${mapFormActionBtn}`).text(capitalizedStateFirstLetter);
                $(`#${mapModalLabel}`).text(`${capitalizedStateFirstLetter} Map`);
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

    const renderFormFields = () => {
        const categories = <?= json_encode($categories) ?>;
        const statuses = <?= json_encode($statuses) ?>;

        // ? Fetch kecamatan data from https://ibnux.github.io/data-indonesia/kecamatan/1807.json
        const kecamatan = fetch(urlkecamatan)
            .then(response => response.json())
            .then(data => {
                data.forEach(kecamatan => delete kecamatan.id);
                $(`#${mapForm}`).append(dropdownComponent('Kecamatan', 'kecamatan', data));
            })
            .catch(function(error) {
                console.log(error);
            });

        // ! FIXME: Kecamatan is in the bottom due to its async behaviour, and the map should be declared in html, it should be in js, need fixing

        $(`#${mapForm}`).append(textInputComponent('Title', 'title'));
        $(`#${mapForm}`).append(dateInputComponent('Date Publish', 'date_publish'));
        $(`#${mapForm}`).append(dropdownComponent('Kategori', 'category', categories));
        $(`#${mapForm}`).append(textInputComponent('Latitude', 'latitude', 'text', 'id="latitude"'));
        $(`#${mapForm}`).append(textInputComponent('Longitude', 'longitude', 'text', 'id="longitude"'));
        $(`#${mapForm}`).append(textareaComponent('Description', 'description', true));
        $(`#${mapForm}`).append(selectInputComponent('Status', 'status', statuses));
        $(`#${mapForm}`).append(fileInputComponent('Cover', 'cover'));
        $(`#${mapForm}`).append(textInputComponent('Video', 'youtube'));
        $(`#${mapForm}`).append(textInputComponent('Address', 'address'));

        // ? Modal Action Buttons
        $(`#${mapForm}`).append(`
            <div class="modal-action">
                <label for="${mapModal}" class="btn btn-error">Batal</label>
                <label id="${mapFormActionBtn}" class="btn btn-primary" onclick="save()">Tambah</label>
            </div>
        `);
    }

    const renderLeafletMap = () => {
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
    }

    // Map CRUD
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

    // Gallery CRUD
    const indexGallery = (mapId) => {
        const url = siteUrl + '<?= $galleryIndexUrl ?>' + mapId;

        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function(response) {
                console.log(response);
                renderGallery(response);
            }
        });
    }

    const renderIndexGalleryBtn = (mapId) => {
        return `<button id="${indexGalleryBtn}" class="btn btn-primary" onclick="indexGallery('${mapId}')">Galeri Foto</button>`;
    }

    const renderIndexGalleryBackBtn = (mapId) => {
        return `<button id="${indexGalleryBackBtn}" class="btn btn-primary" onclick="closeIndexGallery()">Kembali</button>`;
    }

    const renderGallery = (galleryData) => {
        let gallery = '';

        $(`#${indexGalleryBtn}`).hide();
        $(`#${indexGalleryBackBtn}`).show();

        galleryData.forEach(data => {
            gallery += `
                <div class="flex gap-x-20 items-center mb-2">
                    <img src="${baseUrl}/img/${data.filename}" width="200" alt="">
                    <a href="#" class="btn btn-sm btn-error" onclick="destroy('${data.foto_tempat_id}')">Delete</a>
                </div>
            `;
        });

        // ! The indexGalleryContainer always re-rendered, need fixing
        $(`#${indexGalleryContainer}`).append(`${gallery}`);
        $(`#${indexGalleryContainer}`).show();
        $(`#${mapForm}`).hide();
    }

    const closeIndexGallery = () => {
        $(`#${indexGalleryContainer}`).hide();
        $(`#${mapForm}`).show();
        $(`#${indexGalleryBackBtn}`).hide();
        $(`#${indexGalleryBtn}`).show();
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

        renderLeafletMap();
        renderFormFields();

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