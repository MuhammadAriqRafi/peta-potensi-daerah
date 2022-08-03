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
                <label id="popupFormActionBtn" class="btn btn-primary" onclick="save()">Tambah</label>
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

        <table class="table table-zebra w-full" id="popupTable">
            <thead>
                <tr>
                    <th scope="col">Judul</th>
                    <th scope="col">Gambar Pop Up</th>
                    <th scope="col">Aksi</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
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
                <label for="id" class="font-semibold">Pop Up Active (None)</label>
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
    const tableId = 'popupTable';
    const popupForm = 'popupForm';
    const popupModal = 'popupModal';
    const popupModalLabel = 'popupModalLabel';
    const popupFormActionBtn = 'popupFormActionBtn';

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
        $(`#${popupForm}`).trigger('reset');
        $(`[name="image"]`).prev().attr('src', '');
    }

    // CRUD
    const create = () => {
        $(`#${popupModalLabel}`).text('Tambah Pop Up');
        $(`#${popupFormActionBtn}`).text('Tambah');

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
                    reload(tableId);
                    resetForm();
                } else {
                    displayError(response.input_error);
                }
            }
        });
    }

    const destroy = (id) => {
        if (confirm('Apakah anda yakin?')) {
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
                    // ? Delete the pop up active option for the deleted popup and reset the previewImg
                    if (id == oldActivePopupInput.val()) {
                        oldActivePopupInput.val('');
                        $('#activePopupImage').attr('src', '#');
                    }

                    // ? Informing if there is no any active popup
                    if (!response.isActivePopupExist) {
                        $('select[name="id"]').prev().text('Pop Up Active (None)');
                    }

                    alert(response.message);
                    reload(tableId);
                    // TODO: After deleting, the deleted option in select input is not removed, because when datatable reloads, the option for the deleted record still remain, need fixing
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
                resetInvalidClass($(`#${popupForm}`));

                $('[name="title"]').val(response.title);
                $('[name="image"]').prev().attr('src', baseUrl + `/img/${response.value}`);

                if (!isPopupFormInUpdateState()) {
                    $(`#${popupForm}`).prepend(`
                    <input type="hidden" name="id" value="${response.popup_id}">
                    <input type="hidden" name="oldImage" value="${response.value}">
                `);
                } else {
                    $('input[name="id"]').val(response.popup_id);
                    $('input[name="oldImage"]').val(response.value);
                };

                $(`#${popupModalLabel}`).text('Ubah Pop Up');
                $(`#${popupFormActionBtn}`).text('Ubah');
                $(`#${popupModal}`).prop('checked', true);
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
                    reload(tableId);

                    // ? Update the popup active option for the updated popup
                    $(`option[value="${id}"]`).text(response.data.title);

                    // ? Update the popup active image display to the new updated popup image
                    if (id == $('input[name="oldActivePopup"]').val()) {
                        $('#activePopupImage').attr('src', baseUrl + `/img/${response.data.value}`);
                    }

                    resetForm();
                    $(`#${popupModal}`).prop('checked', false);
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
    const updateActivePopup = () => {
        const url = siteUrl + '<?= $updateActivePopupUrl; ?>';
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
                    $(`option[value="${response.data.popup_id}"]`).prop('selected', true);
                    $('#activePopupImage').attr('src', baseUrl + `/img/${response.data.value}`);
                    $('[name="oldActivePopup"]').val(response.data.popup_id);

                    if ($('select[name="id"]').prev().text() == 'Pop Up Active (None)') {
                        $('select[name="id"]').prev().text('Pop Up Active');
                    }
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            }
        });
    }

    $(document).ready(function() {
        const table = createDataTable(tableId, siteUrl + '<?= $indexUrl; ?>', [{
                name: 'title',
                data: 'title'
            },
            {
                name: 'value',
                data: 'value'
            },
            {
                searchable: false,
                orderable: false,
                data: 'popup_id',
                render: function(data) {
                    return editDeleteBtn(data);
                }
            },
            {
                data: 'status',
                visible: false,
                searchable: false,
                orderable: false,

                render: function(data, type, row) {

                    if ($(`option[value="${row.popup_id}"]`).length < 1) {
                        $(`select[name="id"]`).prepend(createNewPopupSelectOption(row.popup_id, row.title, row.status));

                        if (row.status == 'active') {
                            $('select[name="id"]').prev().text('Pop Up Active');
                            $('input[name="oldActivePopup"]').val(row.popup_id);
                            $('#activePopupImage').attr('src', baseUrl + `/img/${row.value}`);
                        }
                    }

                    return true;
                }
            },
        ]);
    });
</script>
<?= $this->endSection(); ?>