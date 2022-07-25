<?= $this->extend('layout/template'); ?>

<!-- // TODO: Fix the image input file view in the form -->

<?= $this->section('modal'); ?>
<!-- Put this part before </body> tag -->
<input type="checkbox" id="popupModal" onclick="create()" class="modal-toggle" />
<div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box sm:p-8">
        <h3 id="popupModalLabel" class="font-bold text-2xl sm:text-4xl mb-6">Tambah Pop Up</h3>

        <!-- Popup Form -->
        <form id="popupForm" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <!-- Title Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Judul</span>
                <input type="text" class="input input-bordered w-full max-w-xs my-2" name="title" />
                <div id="error-title" class="badge badge-error hidden"></div>
            </div>
            <!-- Image Input File -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Image</span>
                <img src="#" height="100" class="img-thumbnail img-preview mb-2">
                <input type="file" id="image" class="input input-bordered w-full max-w-xs my-2" name="image" onchange="previewImg()" accept="image/jpg, image/jpeg, image/png" />
                <div id="error-image" class="badge badge-error hidden"></div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="modal-action">
                <label for="popupModal" class="btn btn-error">Batal</label>
                <label class="btn btn-primary" onclick="save()">Tambah</label>
            </div>
        </form>
        <!-- End of Popup Form -->
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<!-- The button to open modal -->
<label for="popupModal" class="btn modal-button">Tambah Data</label>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="flex w-100 justify-between flex-wrap">
    <div class="w-full sm:w-7/12">
        <?= $this->include('layout/flashMessageAlert'); ?>

        <table class="table table-striped w-full" id="popupTable">
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
                            <button class="btn btn-sm btn-secondary" onclick="edit('<?= $popup['popup_id']; ?>')">Ubah</button>
                            <button class="btn btn-sm btn-error" onclick="destroy('<?= $popup['popup_id']; ?>')">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="divider lg:divider-horizontal sm:w-1/12"></div>

    <div class="w-full sm:w-4/12">
        <img id="activePopupImage" class="img-thumbnail mb-3" height="100">
        <form method="POST" id="updateActivePopupForm">
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="oldActivePopup">
            <!-- Active Pop Up Dropdown -->
            <div class="flex flex-col">
                <label for="id" class="font-semibold">Pop Up Active</label>
                <select name="id" class="select select-bordered w-full max-w-xs sm:max-w-none my-4"></select>
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
            $(`[name="${error.input_name}"]`).addClass('input-error');
            $(`[name="${error.input_name}"]`).next().text(error.error_message);
            $(`[name="${error.input_name}"]`).next().removeClass('hidden');
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
        $('.modal-action .btn-primary').text('Tambah');

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
            const url = siteUrl + '<?= $destroyUrl; ?>' + id;
            const oldActivePopupInput = $('input[name="oldActivePopup"]');

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _method: "DELETE"
                },
                dataType: "json",
                success: function(response) {
                    $(`option[value="${id}"]`).remove();

                    // ? Delete the pop up active option for the deleted popup and reset the previewImg
                    if (id == oldActivePopupInput.val()) {
                        $('#activePopupImage').attr('src', '#');
                        oldActivePopupInput.val('');
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
        const url = siteUrl + '<?= $editUrl; ?>' + id;

        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function(response) {
                // ? Reset the form
                $('#popupForm').find('.badge-error').text('');
                $('#popupForm').find('.badge-error').addClass('hidden');
                $('#popupForm').find('.input-error').removeClass('input-error');

                $('[name="title"]').val(response.title);
                $('[name="image"]').prev().attr('src', baseUrl + `/img/${response.value}`);

                if (!isPopupFormInUpdateState()) {
                    $('#popupForm').prepend(`
                    <input type="hidden" name="oldImage" value="${response.value}">
                    <input type="hidden" name="id" value="${response.popup_id}">
                `);
                } else {
                    $('input[name="oldImage"]').val(response.value);
                    $('input[name="id"]').val(response.popup_id);
                };

                $('#popupModalLabel').text('Ubah Pop Up');
                $('.modal-action .btn-primary').text('Ubah');
                $('#popupModal').prop('checked', true);
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
                    $('#popupModal').prop('checked', false);
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

        // TODO: Implement server side functionality to the datatable
        let table = $('#popupTable').DataTable({
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 99999],
                [10, 25, 50, 'All'],
            ],
            dom: '<"overflow-x-hidden"<"flex flex-wrap gap-4 justify-center sm:justify-between items-center mb-5"lf><t><"flex justify-between items-center mt-5"ip>>'
        });
    });
</script>
<?= $this->endSection(); ?>