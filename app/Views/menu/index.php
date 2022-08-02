<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>

<div class="flex w-full justify-between">
    <div class="overflow-x-auto">
        <h4 class="text-2xl font-semibold mb-4">Struktur</h4>

        <table>
            <thead>
                <?php foreach ($menus as $menu) : ?>
                    <tr>
                        <td>
                            <button class="btn btn-error btn-sm mr-4" onclick="destroy('<?= $menu['menu_id']; ?>')">Delete</button>
                        </td>
                        <td id="<?= $menu['menu_id'] ?>" onclick="fetch(this)" class="cursor-pointer" onMouseOver="this.style.color='salmon'" onMouseOut="this.style.color='black'">
                            <span><?= $menu['title']; ?></span>
                            <progress class="progress hidden w-8 ml-4"></progress>
                        </td>
                    </tr>
                <?php endforeach ?>
            </thead>
        </table>
    </div>

    <div class="basis-2/5">
        <h4 class="text-2xl font-semibold mb-4" id="formTitle">Tambah Data</h4>
        <form method="POST" id="menuForm">
            <!-- Title Input -->
            <div class="mb-3" onclick="resetInvalidClass(this)">
                <label for="title" class="label">Title</label>
                <input type="text" class="input input-bordered w-full max-w-xs" name="title" id="title" autofocus />
                <div id="error-title" class="badge badge-error mt-2 hidden"></div>
            </div>
            <!-- Url Input -->
            <div class="mb-3" onclick="resetInvalidClass(this)">
                <label for="url" class="label">Url</label>
                <input type="text" class="input input-bordered w-full max-w-xs" name="url" id="url" autofocus />
                <div id="error-url" class="badge badge-error mt-2 hidden"></div>
            </div>
            <!-- Status Radio Input -->
            <div class="mb-3" onclick="resetInvalidClass(this)">
                <label for="status" class="form-label fw-bold">Status</label><br>
                <?php foreach ($targets as $key => $target) : ?>
                    <div class="flex items-center gap-2">
                        <input class="radio" type="radio" name="target" value="<?= $target; ?>">
                        <label class="label" for="<?= $key; ?>"><?= ucfirst($key); ?></label>
                    </div>
                <?php endforeach ?>
                <div id="error-target" class="badge badge-error mt-2 hidden"></div>
            </div>

            <div class="modal-footer d-flex justify-content-start">
                <button type="button" id="formSubmitBtn" class="btn btn-primary mt-4" onclick="saveAndUpdate()">Tambah</button>
            </div>
        </form>
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

    // Helper
    const hideEditForm = () => {
        // ? Reset validation error in the form
        resetInvalidClass($('#menuForm'));

        formTitle.text('Tambah Data');
        formSubmitBtn.text('Tambah');
        formSubmitBtn.prev().remove();
        titleField.val('');
        urlField.val('');
        $('input[type="hidden"]').remove();
        $(`input:radio`).prop('checked', false);
    }

    const displayError = (inputError) => {
        inputError.forEach(error => {
            $(`#error-${error.input_name}`).text(error.error_message);
            $(`#error-${error.input_name}`).removeClass('hidden');
            $(`[name="${error.input_name}"]`).addClass('input-error');
        });
    }

    // CRUD
    const fetch = (element) => {
        const url = siteUrl + '<?= $editUrl; ?>' + $(element).attr('id');
        const elementSpinner = $(element).find('.progress');

        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            beforeSend: function() {
                elementSpinner.removeClass('hidden');
            },
            success: function(response) {
                // ? Check if hidden input for id exist
                if ($('#id').length < 1) {
                    $('#menuForm').prepend(`
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="id" id="id" value="${response.menu_id}">
                    `);
                } else {
                    $('#id').val(response.menu_id);
                }

                // ? Reset validation error in the form
                resetInvalidClass($('#menuForm'));

                if (formTitle.text() != 'Edit Data') {
                    formTitle.text('Edit Data');
                    formSubmitBtn.text('Ubah');
                    formSubmitBtn.before(`<button type="button" class="btn btn-error mr-4" onclick="hideEditForm()">Cancel</button>`)
                    $('#menuForm').find('.is-invalid').removeClass('is-invalid');
                }

                urlField.val(response.url);
                titleField.val(response.title).focus();
                $(`input:radio[value="${response.target}"][name='target']`).prop('checked', true);
            },
            complete: function() {
                elementSpinner.addClass('hidden');
            }
        });
    }

    const store = (data) => {
        let url = siteUrl + '<?= $storeUrl; ?>';

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
                    $('#menuForm').trigger('reset');
                    alert(response.message);
                    $(`thead`).prepend(`
                        <tr>    
                            <td>
                                <button class="btn btn-error btn-sm mr-4" onclick="destroy('${response.data.menu_id}')">Delete</button>
                            </td>
                            <td id="${response.data.menu_id}" onclick="fetch(this)" class="cursor-pointer" onMouseOver="this.style.color='salmon'" onMouseOut="this.style.color='black'">
                                <span>${response.data.title}</span>
                                <progress class="progress hidden w-8 ml-4"></progress>
                            </td>
                        </tr>
                    `);
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            },
            complete: function() {
                $('#formSubmitBtn').text('Tambah');
            }
        });
    }

    const update = (data) => {
        const id = $('#id').val();
        const url = siteUrl + '<?= $updateUrl; ?>' + id;
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
                if (response.status) {
                    alert(response.message);
                    $(document).find(`[id="${id}"]`).find('span').text(response.data.title);
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            },
            complete: function() {
                $('#formSubmitBtn').text('Ubah');
            }
        });
    }

    const saveAndUpdate = () => {
        const form = $('#menuForm')[0];
        const data = new FormData(form);

        if ($('#id').length < 1) {
            store(data);
        } else {
            update(data);
        }
    }

    const destroy = (id) => {
        if (confirm('Apakah anda yakin?')) {
            const url = siteUrl + '<?= $destroyUrl; ?>' + id;

            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                success: function(response) {
                    if (response.status) $(document).find(`[id="${id}"]`).parent().remove();
                    hideEditForm();
                    alert(response.message);
                }
            });
        }
    }
</script>
<?= $this->endSection(); ?>