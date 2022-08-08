<?= $this->extend('layout/template'); ?>

<?= $this->section('css'); ?>
<!-- Summernote -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<?= $this->endSection(); ?>

<?= $this->section('modal'); ?>
<!-- Put this part before </body> tag -->
<input type="checkbox" id="profileModal" class="modal-toggle" />
<div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box sm:p-8">
        <h3 id="profileModalLabel" class="font-bold text-2xl sm:text-4xl mb-6">Tambah Tentang Aplikasi</h3>

        <!-- Popup Form -->
        <form action="#" id="profileForm">
            <?= csrf_field(); ?>
        </form>
        <!-- End of Popup Form -->
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<!-- The button to open modal -->
<label for="profileModal" class="btn modal-button" onclick="create()">Tambah Data</label>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>
<div class="overflow-x-auto">
    <table class="table table-zebra w-full" id="profileTable">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Date Publish</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    const tableId = 'profileTable';
    const profileForm = 'profileForm';
    const profileModal = 'profileModal';
    const profileModalLabel = 'profileModalLabel';
    const profileFormActionBtn = 'profileFormActionBtn';

    // Helper
    const displayError = (inputError) => {
        inputError.forEach(error => {
            let input = $(`[name=${error.input_name}]`);

            if (input.attr('type') != 'radio') input.val('');

            $(`#error-${error.input_name}`).removeClass('hidden');
            $(`#error-${error.input_name}`).text(error.error_message);

            switch (error.input_name) {
                case 'content':
                    $('#summernote').summernote('reset');
                    break;
                case 'status':
                    input.addClass('radio-error');
                    break;
                case 'content':
                case 'description':
                    input.addClass('textarea-error');
                    break;
                default:
                    input.addClass('input-error');
                    break;
            }
        });
    }

    const setFormState = (state, id = null) => {
        const capitalizedStateFirstLetter = state.charAt(0).toUpperCase() + state.slice(1);

        resetInvalidClass($(`#${profileForm}`));

        if (capitalizedStateFirstLetter == 'Tambah') {
            // ? Set form action for 'Tambah'
            $(`#${profileForm}`).attr('action', siteUrl + '<?= $storeUrl; ?>');

            // ? If form current state is already 'Tambah'
            if ($('input[name="_method"]').length < 1) {
                return true;
            } else {
                $('input[name="_method"]').remove();
                $(`#${profileFormActionBtn}`).text(capitalizedStateFirstLetter);
                $(`#${profileModalLabel}`).text(`${capitalizedStateFirstLetter} Tentang Aplikasi`);
                $(`#${profileForm}`).trigger('reset');
                $('#summernote').summernote('reset');
            }
        } else if (capitalizedStateFirstLetter == 'Ubah') {
            // ? Set form action for 'Ubah'
            $(`#${profileForm}`).attr('action', siteUrl + '<?= $updateUrl; ?>' + id);

            // ? If form current state is already 'Ubah'
            if ($('input[name="_method"]').length > 1) {
                return true;
            } else {
                $(`#${profileForm}`).prepend(`
                    <input type="hidden" name="_method" value="PATCH">
                `);
                $(`#${profileFormActionBtn}`).text(capitalizedStateFirstLetter);
                $(`#${profileModalLabel}`).text(`${capitalizedStateFirstLetter} Tentang Aplikasi`);
            }
        }
    }

    // CRUD
    const destroy = (id) => {
        if (confirm('Apakah anda yakin?')) {
            const url = siteUrl + '<?= $destroyUrl; ?>' + id;

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _method: "DELETE"
                },
                dataType: "json",
                success: function(response) {
                    if (response.status) alert(response.message);
                    reload(tableId);
                }
            });
        }
    }

    const create = () => {
        setFormState('Tambah');
    }

    const store = (data) => {
        const url = $(`#${profileForm}`).attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status) {
                    alert(response.message);
                    $('#summernote').summernote('reset');
                    $(`#${profileForm}`).trigger('reset');
                    reload(tableId);
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            },
        });
    }

    const edit = (id) => {
        const url = siteUrl + '<?= $editUrl; ?>' + id;

        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function(response) {
                setFormState('Ubah', response.post_id);

                $('input[name="title"]').val(response.title);
                $(`input[value="${response.status}"]`).prop('checked', true);
                $('input[name="date_publish"]').val(response.date_publish);
                $('#summernote').summernote('code', response.content);
                $('textarea[name="description"]').val(response.description);

                $(`#${profileModal}`).prop('checked', true);
            }
        });
    }

    const update = (data) => {
        const url = $(`#${profileForm}`).attr('action');

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
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            }
        });
    }

    const save = () => {
        const form = $(`#${profileForm}`)[0];
        const data = new FormData(form);

        if ($('input[name="_method"]').length > 0) update(data);
        else store(data);
    }

    $(document).ready(function() {
        // ? DataTables
        const table = createDataTable('profileTable', siteUrl + '<?= $indexUrl; ?>', [{
                name: 'title',
                data: 'title'
            },
            {
                name: 'author',
                data: 'author'
            },
            {
                name: 'DATE(date_publish)',
                data: 'DATE(date_publish)'
            },
            {
                name: 'status',
                data: 'status'
            },
            {
                data: 'post_id',
                searchable: false,
                orderable: false,
                render: function(data) {
                    return editDeleteBtn(data);
                }
            },
        ]);

        // ? Form Inputs
        let status = <?= json_encode($statuses) ?>;

        $(`#${profileForm}`).append(textInputComponent('Title', 'title'));
        $(`#${profileForm}`).append(dateInputComponent('Date Publish', 'date_publish'));
        $(`#${profileForm}`).append(textareaComponent('Content', 'content', true));
        $(`#${profileForm}`).append(selectInputComponent('Status', 'status', status));
        $(`#${profileForm}`).append(textareaComponent('Description', 'description'));

        // ? Modal Action Buttons
        $(`#${profileForm}`).append(`
            <div class="modal-action">
                <label for="${profileModal}" class="btn btn-error">Batal</label>
                <label id="${profileFormActionBtn}" class="btn btn-primary" onclick="save()">Tambah</label>
            </div>
        `)

        // ? Summernote
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