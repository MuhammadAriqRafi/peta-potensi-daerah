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
            <!-- Title Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Title</span>
                <input type="text" class="input input-bordered w-full max-w-xs my-2" name="title" />
                <div id="error-title" class="badge badge-error hidden"></div>
            </div>
            <!-- Date Publish Input Date -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Date Publish</span>
                <input type="date" class="input input-bordered w-full max-w-xs my-2" name="date_publish" min="1900-01-01" max="<?= date("Y-12-31"); ?>" value="<?= date('Y-m-d'); ?>" />
                <div id="error-date_publish" class="badge badge-error hidden">ghost</div>
            </div>
            <!-- Content Textarea -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold mb-2">Content</span>
                <textarea id="summernote" name="content"></textarea>
                <div id="error-content" class="badge badge-error hidden mt-2">ghost</div>
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
                <div id="error-status" class="badge badge-error hidden">ghost</div>
            </div>
            <!-- Description Textarea -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Description</span>
                <textarea class="textarea textarea-bordered my-2" name="description"></textarea>
                <div id="error-description" class="badge badge-error hidden">ghost</div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="modal-action">
                <label for="profileModal" class="btn btn-error">Batal</label>
                <label id="profileFormActionBtn" class="btn btn-primary" onclick="save()">Tambah</label>
            </div>
        </form>
        <!-- End of Popup Form -->
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<!-- The button to open modal -->
<label for="profileModal" class="btn modal-button">Tambah Data</label>
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

    const isFormInUpdateState = () => {
        if ($('input[name="id"]').length > 0) return true;
        return false;
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

    const store = (data) => {
        const url = siteUrl + '<?= $storeUrl; ?>';

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);
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
                console.log(response);
                // TODO: Currently working on tentang aplikasi management (edit functionality)

                if (!isFormInUpdateState()) {
                    $(`#${profileModalLabel}`).text('Ubah Tentang Aplikasi');
                    $(`#${profileFormActionBtn}`).text('Ubah');
                    $(`#${profileForm}`).prepend(`
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="id" value="${response.post_id}">
                    `);
                }

                $(`#${profileModal}`).prop('checked', true);
            }
        });
    }

    const save = () => {
        const form = $(`#${profileForm}`)[0];
        const data = new FormData(form);

        store(data);
    }

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
    });
</script>
<?= $this->endSection(); ?>