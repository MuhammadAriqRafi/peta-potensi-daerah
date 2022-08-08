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
        <form method="POST" id="menuForm"></form>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    const menuForm = 'menuForm';
    const menuModalLabel = 'menuModalLabel';
    const menuFormActionBtn = 'menuFormActionBtn';

    // Helper
    const hideEditForm = () => {
        // ? Reset validation error in the form
        resetInvalidClass($(`#${menuForm}`));

        $('#formTitle').text('Tambah Data');
        $(`#${menuFormActionBtn}`).text('Tambah');
        $(`#${menuFormActionBtn}`).prev().remove();
        $('#title').val('');
        $('#url').val('');
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
                    $(`#${menuForm}`).prepend(`
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="id" id="id" value="${response.menu_id}">
                    `);
                } else {
                    $('#id').val(response.menu_id);
                }

                // ? Reset validation error in the form
                resetInvalidClass($('#menuForm'));

                if ($('#formTitle').text() != 'Edit Data') {
                    $('#formTitle').text('Edit Data');
                    $(`#${menuFormActionBtn}`).text('Ubah');
                    $(`#${menuFormActionBtn}`).before(`<button type="button" class="btn btn-error mr-4" onclick="hideEditForm()">Cancel</button>`)
                    $(`#${menuForm}`).find('.is-invalid').removeClass('is-invalid');
                }

                $('#url').val(response.url);
                $('#title').val(response.title).focus();
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
                $(`#${menuFormActionBtn}`).text('Loading...');
            },
            success: function(response) {
                if (response.status) {
                    $(`#${menuForm}`).trigger('reset');
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
                $(`#${menuFormActionBtn}`).text('Tambah');
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
                $(`#${menuFormActionBtn}`).text('Loading...');
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
                $(`#${menuFormActionBtn}`).text('Ubah');
            }
        });
    }

    const saveAndUpdate = () => {
        const form = $(`#${menuForm}`)[0];
        const data = new FormData(form);

        if ($('#id').length < 1) store(data);
        else update(data);
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

    $(document).ready(function() {
        let targets = <?= json_encode($targets) ?>;

        $(`#${menuForm}`).append(textInputComponent('Title', 'title', 'text', 'id="title" autofocus'));
        $(`#${menuForm}`).append(textInputComponent('Url', 'url', 'text', 'id="url" autofocus'));
        $(`#${menuForm}`).append(selectInputComponent('Target', 'target', targets));

        $(`#${menuForm}`).append(`
            <div class="modal-footer d-flex justify-content-start">
                <button type="button" id="menuFormActionBtn" class="btn btn-primary mt-4" onclick="saveAndUpdate()">Tambah</button>
            </div>
        `);
    })
</script>
<?= $this->endSection(); ?>