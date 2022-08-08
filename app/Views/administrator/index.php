<?= $this->extend('layout/template'); ?>

<?= $this->section('modal'); ?>
<!-- Put this part before </body> tag -->
<input type="checkbox" id="administratorModal" class="modal-toggle" />
<div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box sm:p-8">
        <h3 id="administratorModalLabel" class="font-bold text-2xl sm:text-4xl mb-6"></h3>

        <!-- Popup Form -->
        <form action="#" id="administratorForm"></form>
        <!-- End of Popup Form -->
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<!-- The button to open modal -->
<label for="administratorModal" class="btn modal-button" onclick="create()">Tambah Data</label>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>

<div class="overflow-x-auto">
    <table class="table table-zebra w-full" id="administratorTable">
        <thead>
            <th>NIK</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Actions</th>
        </thead>
    </table>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    const tableId = 'administratorTable';
    const administratorForm = 'administratorForm';
    const administratorModalLabel = 'administratorModalLabel';
    const administratorFormActionBtn = 'administratorFormActionBtn';

    // Helper
    const displayError = (inputError) => {
        inputError.forEach(error => {
            const input = $(`[name=${error.input_name}]`);

            $(`#error-${error.input_name}`).removeClass('hidden');
            $(`#error-${error.input_name}`).text(error.error_message);
            input.addClass('input-error');
        });
    }

    const isFormInUpdateState = () => {
        if ($('input[name="id"]').length > 0) return true;
        return false;
    }

    // CRUD
    const create = () => {
        $('[name="nik"]').val('');
        $('[name="nama"]').val('');
        $('[name="username"]').val('');

        resetInvalidClass($(`#${administratorForm}`));
        if (isFormInUpdateState()) {
            $('input[name="id"]').remove();
        }

        $(`#${administratorModalLabel}`).text('Tambah Administrator');
        $(`#${administratorFormActionBtn}`).text('Tambah');
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
                    $(`#${administratorForm}`).trigger('reset');
                    alert(response.message);
                    reload(tableId);
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            }
        });
    }

    const edit = (id) => {
        const url = siteUrl + '<?= $editUrl ?>' + id;

        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function(response) {
                resetInvalidClass($(`#${administratorForm}`));
                $('[name="nik"]').val(response.nik);
                $('[name="nama"]').val(response.nama);
                $('[name="username"]').val(response.username);

                if (isFormInUpdateState()) {
                    $('input[name="id"]').val(response.admin_id);
                } else {
                    $(`#${administratorForm}`).prepend(`<input type="hidden" name="id" value="${response.admin_id}">`);
                    $('input[name="id"]').val(response.admin_id);
                }

                $(`#${administratorFormActionBtn}`).text('Ubah');
                $(`#${administratorModalLabel}`).text('Ubah Administrator');
                $('#administratorModal').prop('checked', true);
            }
        });
    }

    const update = (id, data) => {
        const url = siteUrl + '<?= $updateUrl; ?>' + id;
        data.append('_method', 'PATCH');
        data.set('id', atob(id));

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                console.log(response);
                if (response.status) {
                    alert(response.message);
                    reload(tableId);
                    $(`#${administratorForm}`).trigger('reset');
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            }
        });
    }

    const destroy = (id) => {
        if (confirm('Apakah anda yakin?')) {
            const url = siteUrl + '<?= $destroyUrl ?>' + id;

            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                success: function(response) {
                    alert(response.message);
                    reload(tableId);
                }
            });
        }
    }

    const save = () => {
        const form = $(`#${administratorForm}`)[0];
        const data = new FormData(form);

        if (isFormInUpdateState()) {
            const id = $('input[name="id"]').val();
            update(id, data);
        } else store(data);
    }

    $(document).ready(function() {
        const table = createDataTable('administratorTable', siteUrl + '<?= $indexUrl; ?>', [{
                name: 'nik',
                data: 'nik'
            },
            {
                name: 'nama',
                data: 'nama'
            },
            {
                name: 'username',
                data: 'username'
            },
            {
                searchable: false,
                orderable: false,
                data: 'admin_id',
                render: function(data) {
                    return editDeleteBtn(data);
                }
            },
        ]);

        // Form Inputs
        $(`#${administratorForm}`).append(textInputComponent('NIK', 'nik'));
        $(`#${administratorForm}`).append(textInputComponent('Nama', 'nama'));
        $(`#${administratorForm}`).append(textInputComponent('Username', 'username'));
        $(`#${administratorForm}`).append(textInputComponent('Password', 'password', 'password'));
        $(`#${administratorForm}`).append(textInputComponent('Password Confirm', 'passconf', 'passconf'));

        // Modal Action Buttons
        $(`#${administratorForm}`).append(`
            <div class="modal-action">
                <label for="administratorModal" class="btn btn-error">Batal</label>
                <label id="${administratorFormActionBtn}" class="btn btn-primary" onclick="save()"></label>
            </div>
        `);
    });
</script>
<?= $this->endSection(); ?>