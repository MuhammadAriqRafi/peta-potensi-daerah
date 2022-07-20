<?= $this->extend('layout/template'); ?>

<?= $this->section('toolbar'); ?>
<!-- Button trigger modal -->
<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#popupModal" onclick="create()">
    Tambah Data
</button>

<!-- Modal -->
<div class="modal fade" id="popupModal" tabindex="-1" aria-labelledby="popupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="popupModalLabel">Tambah Pop Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" enctype="multipart/form-data" id="popupForm">
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
                    <button type="button" class="btn btn-primary" onclick="save()">Tambah</button>
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

        <table class="table" id="popupTable">
            <thead>
                <tr>
                    <th scope="col">Judul</th>
                    <th scope="col">Gambar Pop Up</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($popups as $popup) : ?>
                    <tr id="<?= $popup['popup_id']; ?>">
                        <td><?= $popup['title']; ?></td>
                        <td><?= $popup['value'] ?? '-'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-warning" onclick="edit('<?= $popup['popup_id']; ?>')">Ubah</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="destroy('<?= $popup['popup_id']; ?>')">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="col-4">
        <img id="activePopupImage" class="img-thumbnail mb-3" height="100">
        <form method="POST" id="updateActivePopupForm">
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="oldActivePopup">
            <!-- Active Pop Up Dropdown -->
            <div class="mb-3">
                <label for="id" class="form-label fw-bold">Pop Up Active</label>
                <select name="id" class="form-select"></select>
                <div class="invalid-feedback"></div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="updateActivePopup()">Ubah</button>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    // Helper
    const createNewPopupSelectOption = (id, title, status) => {
        return `<option value="${id}" ${status == 'active' ? 'selected': ''}>${title}</option>`;
    }

    const displayError = (inputError) => {
        inputError.forEach(error => {
            $(`[name="${error.input_name}"]`).addClass('is-invalid');
            $(`[name="${error.input_name}"]`).next().text(error.error_message);
            if (error.input_name == 'image') $(`[name="${error.input_name}"]`).prev().attr('src', '');
        });
    }

    const isPopupFormInUpdateState = () => {
        if ($('input[name="oldImage"]').length > 0) return true;
        return false;
    }

    const previewImg = () => {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('.img-preview');
        const fileImage = new FileReader();

        fileImage.readAsDataURL(image.files[0]);
        fileImage.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }

    const resetForm = () => {
        $('#popupForm').trigger('reset');
        $(`[name="image"]`).prev().attr('src', '');
    }

    // CRUD
    const index = () => {
        const url = siteUrl + '<?= $indexUrl; ?>';

        return fetch(url, {
            method: "GET"
        }).then(response => response.json());
    }

    const create = () => {
        $('#popupModalLabel').text('Tambah Pop Up');
        $('.modal-footer .btn-primary').text('Tambah');

        if (isPopupFormInUpdateState()) {
            $('input[name="title"]').val('');
            $('input[name="image"]').prev().attr('src', '#');
            $('input[name="oldImage"]').remove();
            $('input[name="id"]').remove();
        }
    }

    const store = (data) => {
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
                        <tr id="${response.data.popup_id}">
                            <td>${response.data.title}</td>
                            <td>${response.data.value}</td>
                            <td>${response.data.actions ?? '-'}</td>
                        </tr>
                        `);
                    resetForm();
                    $('select[name="id"]').prepend(createNewPopupSelectOption(response.data.popup_id, response.data.title, response.data.status));
                } else {
                    displayError(response.input_error);
                }
            }
        });
    }

    const destroy = (id) => {
        if (confirm('apakah anda yakin?')) {
            let url = siteUrl + '<?= $destroyUrl; ?>' + id;
            const oldActivePopupInput = $('input[name="oldActivePopup"]');

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _method: "DELETE"
                },
                dataType: "json",
                success: function(response) {
                    // ? Delete the pop up active option for the deleted popup and reset the previewImg
                    if (id == oldActivePopupInput.val()) {
                        oldActivePopupInput.val('');
                        $('#activePopupImage').attr('src', '#');
                        $(`option[value="${id}"]`).remove();
                    }

                    // ? Informing if there is no any active popup
                    if (!response.isActivePopupExist) {
                        $('select[name="id"]').prev().text('Pop Up Active (None)');
                    }

                    alert(response.message);
                    $(`tr[id="${response.idDeletedPopup}"]`).remove();
                }
            });
        }
    }

    const edit = (id) => {
        let url = siteUrl + '<?= $editUrl; ?>' + id;
        $(document).find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function(response) {
                $('[name="title"]').val(response.title);
                $('[name="image"]').prev().attr('src', baseUrl + `/img/${response.value}`);

                if (!isPopupFormInUpdateState()) $('#popupForm').prepend(`
                    <input type="hidden" name="oldImage" value="${response.value}">
                    <input type="hidden" name="id" value="${response.popup_id}">
                `);
                else {
                    $('input[name="oldImage"]').val(response.value);
                    $('input[name="id"]').val(response.popup_id);
                };

                $('#popupModalLabel').text('Ubah Pop Up');
                $('.modal-footer .btn-primary').text('Ubah');
                $('#popupModal').modal('show');
            }
        });
    }

    const update = (id, data) => {
        let url = siteUrl + '<?= $updateUrl; ?>' + id;

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
                    $(`tr[id="${id}"] td`).first().text(response.data.title);
                    $(`tr[id="${id}"] td`).eq(1).text(response.data.value);

                    // ? Update the popup active option for the updated popup
                    $(`option[value="${id}"]`).text(response.data.title);

                    // ? Update the popup active image display to the new updated popup image
                    if (id == $('input[name="oldActivePopup"]').val()) {
                        $('#activePopupImage').attr('src', baseUrl + `/img/${response.data.value}`);
                    }

                    resetForm();
                    $('#popupModal').modal('hide');
                } else {
                    if (response.input_error) {
                        displayError(response.input_error);
                    } else {
                        alert(response.message);
                    }
                }
            }
        });
    }

    const save = () => {
        const form = $('#popupForm')[0];
        const data = new FormData(form);

        if ($('input[name="oldImage"]').length < 1) store(data);
        else {
            let id = $('input[name="id"]').val();
            update(id, data);
        }
    }

    // Active Popup
    const getActivePopup = () => {
        const data = index();
        const oldActivePopup = $('[name="oldActivePopup"]');

        data.then(popups => {
            let isAnyActivePopup = false;

            popups.forEach(popup => {
                $(`select[name="id"]`).prepend(createNewPopupSelectOption(popup.popup_id, popup.title, popup.status));
                if (popup.status == 'active') {
                    isAnyActivePopup = true;
                    oldActivePopup.val(popup.popup_id);
                    $('#activePopupImage').attr('src', baseUrl + `/img/${popup.value}`);
                }
            });

            if (!isAnyActivePopup) $('select[name="id"]').prev().text('Pop Up Active (None)');
        });
    }

    const updateActivePopup = () => {
        let url = siteUrl + '<?= $updateActivePopupUrl; ?>';
        const form = $('#updateActivePopupForm')[0];
        const data = new FormData(form);

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
                    $(`option[value="${response.data.popup_id}"]`).attr('selected', 'selected');
                    $('#activePopupImage').attr('src', baseUrl + `/img/${response.data.value}`);
                    $('[name="oldActivePopup"]').val(response.data.popup_id);

                    if ($('select[name="id"]').prev().text() == 'Pop Up Active (None)') {
                        $('select[name="id"]').prev().text('Pop Up Active');
                    }
                } else {
                    if (response.input_error) {
                        displayError(response.input_error);
                    }
                }
            }
        });
    }

    $(document).ready(function() {
        getActivePopup();

        // let table = $('#popupTable').DataTable({
        //     pageLength: 10,
        //     lengthMenu: [
        //         [10, 25, 50, 99999],
        //         [10, 25, 50, 'All'],
        //     ],
        //     // ajax: '<?= site_url(route_to('backend.profiles.index.ajax')); ?>',
        //     // serverSide: true,
        //     // deferRender: true
        // });
    });
</script>
<?= $this->endSection(); ?>