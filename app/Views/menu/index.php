<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6 p-0">
            <table>
                <thead>
                    <?php foreach ($menus as $menu) : ?>
                        <tr>
                            <?php $id = base64_encode($menu['menu_id']); ?>

                            <td id="<?= $id ?>" onclick="fetch(this)" style="cursor: pointer;" onMouseOver="this.style.color='salmon'" onMouseOut="this.style.color='black'">
                                <span><?= $menu['title']; ?></span>
                                <div class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </thead>
            </table>
        </div>

        <div class="col-sm-6 p-0">
            <h4 class="mb-3" id="formTitle">Tambah Data</h4>
            <form method="POST" id="editMenuForm">
                <!-- Title Input -->
                <div class="mb-3" onclick="resetInvalidClass(this)">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" id="title" name="title" autofocus>
                    <div class="invalid-feedback"></div>
                </div>
                <!-- Url Input -->
                <div class="mb-3" onclick="resetInvalidClass(this)">
                    <label for="url" class="form-label fw-bold">Url</label>
                    <input type="text" class="form-control" id="url" name="url">
                    <div class="invalid-feedback"></div>
                </div>
                <!-- Status Radio Input -->
                <div class="mb-3" onclick="resetInvalidClass(this)">
                    <label for="status" class="form-label fw-bold">Status</label><br>
                    <?php foreach ($targets as $key => $target) : ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="target" value="<?= $target; ?>">
                            <label class="form-check-label" for="<?= $key; ?>"><?= ucfirst($key); ?></label>
                        </div>
                    <?php endforeach ?>
                    <small class="text-danger d-block"></small>
                </div>
                <div class="modal-footer d-flex justify-content-start">
                    <button type="button" id="formSubmitBtn" class="btn btn-primary" onclick="saveAndUpdate()">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    const urlField = $('#url');
    const titleField = $('#title');
    const formTitle = $('#formTitle');
    const formSubmitBtn = $('#formSubmitBtn');
    let elementTitleBeforeUpdate;

    const hideEditForm = () => {
        formTitle.text('Tambah Data');
        formSubmitBtn.text('Tambah');
        formSubmitBtn.prev().remove();
        titleField.val('');
        urlField.val('');
        $('input[type="hidden"]').remove();
        $(`input:radio`).prop('checked', false);
    }

    const fetch = (element) => {
        const url = '<?= site_url(route_to('backend.menus.show.ajax')); ?>';
        const elementSpinner = $(element).find('.spinner-border');
        elementTitleBeforeUpdate = $(element).find('span').first().text();

        $.ajax({
            type: "POST",
            url: url,
            data: {
                id: $(element).attr('id')
            },
            dataType: "json",
            beforeSend: function() {
                elementSpinner.removeClass('d-none');
            },
            success: function(response) {
                // ? Check if input hidden for id exist
                if ($('#id').length < 1) {
                    $('#editMenuForm').prepend(`
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="id" id="id" value="${response.menu_id}">
                    `);
                } else {
                    $('#id').val(response.menu_id);
                }

                if (formTitle.text() != 'Edit Data') {
                    formTitle.text('Edit Data');
                    formSubmitBtn.text('Ubah');
                    formSubmitBtn.before(`<button type="button" class="btn btn-danger me-2" onclick="hideEditForm()">Cancel</button>`)
                    $('#editMenuForm').find('.is-invalid').removeClass('is-invalid');
                }

                urlField.val(response.url);
                titleField.val(response.title).focus();
                $(`input:radio[value="${response.target}"][name='target']`).prop('checked', true);
                elementSpinner.addClass('d-none');
            }
        });
    }

    const store = (data) => {
        let url = '<?= site_url(route_to('backend.menus.store.ajax')); ?>';

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            processData: false,
            contentType: false,
            dataType: "json",
            beforeSend: function() {
                $('#formSubmitBtn').text('Loading...');
            },
            success: function(response) {
                if (response.status) {
                    alert(response.message);
                    $(`thead`).prepend(`
                        <tr>    
                            <td id="${response.data.menu_id}" onclick="fetch(this)" style="cursor: pointer;" onMouseOver="this.style.color='salmon'" onMouseOut="this.style.color='black'">
                                <span>${response.data.title}</span>
                                <div class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    `);
                } else {
                    response.input_error.forEach(error => {
                        $(`[name="${error.input_name}"]`).addClass('is-invalid');
                        $(`[name="${error.input_name}"]`).next().text(error.error_message);
                    });
                }
                $('#formSubmitBtn').text('Tambah');
            }
        });
    }

    const update = (data) => {
        const id = $('#id').val();
        const url = urlFormatter('<?= site_url(route_to('backend.menus.update.ajax', ':id')); ?>', id);
        let oldTitle = $('#title').val();

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#formSubmitBtn').text('Loading...');
            },
            success: function(response) {
                console.log(response);
                if (response.status) {
                    alert(response.message);
                    $(`td span:contains(${elementTitleBeforeUpdate})`).text(response.data.title);
                } else {
                    response.input_error.forEach(error => {
                        $(`[name="${error.input_name}"]`).addClass('is-invalid');
                        $(`[name="${error.input_name}"]`).next().text(error.error_message);
                    });
                }
                $('#formSubmitBtn').text('Ubah');
            }
        });
    }

    const saveAndUpdate = () => {
        const form = $('#editMenuForm')[0];
        const data = new FormData(form);

        if ($('#id').length < 1) {
            store(data);
        } else {
            update(data);
        }
    }
</script>
<?= $this->endSection(); ?>