<?= $this->extend('layout/template'); ?>

<?= $this->section('css'); ?>
<!-- Summernote -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<?= $this->endSection(); ?>

<?= $this->section('modal'); ?>
<!-- Put this part before </body> tag -->
<input type="checkbox" id="popupModal" class="modal-toggle" />
<div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Congratulations random Internet user!</h3>
        <p class="py-4">You've been selected for a chance to get one year of subscription to use Wikipedia for free!</p>
        <div class="modal-action">
            <label for="popupModal" class="btn">Yay!</label>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<!-- The button to open modal -->
<label for="popupModal" class="btn modal-button">Tambah Data</label>

<!-- Modal -->
<div class="modal fade" id="addProfileModal" tabindex="-1" aria-labelledby="addProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProfileModalLabel">Tambah Tentang Aplikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" id="addProfileForm">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <!-- Title Input -->
                    <div class="mb-3" onclick="resetInvalidClass(this)">
                        <label for="title" class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <!-- Date Publish Date Input -->
                    <div class="mb-3" onclick="resetInvalidClass(this)">
                        <label for="date_publish" class="form-label fw-bold">Date Publish</label>
                        <input type="date" name="date_publish" class="form-control" min="1900-01-01" max="<?= date("Y-12-31"); ?>" value="<?= date('Y-m-d'); ?>">
                        <div class="invalid-feedback"></div>
                    </div>
                    <!-- Content Textarea -->
                    <div class="mb-3" onclick="resetInvalidClass(this)">
                        <label for="content" class="form-label fw-bold">Content</label>
                        <textarea id="summernote" name="content"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <!-- Status Radio Input -->
                    <div class="mb-3" onclick="resetInvalidClass(this)">
                        <label for="status" class="form-label fw-bold">Status</label><br>
                        <?php foreach ($statuses as $status) : ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="<?= $status; ?>">
                                <label class="form-check-label" for="<?= $status; ?>"><?= ucfirst($status); ?></label>
                            </div>
                        <?php endforeach ?>
                        <small class="text-danger d-block"></small>
                    </div>
                    <!-- Description Textarea -->
                    <div class="mb-3" onclick="resetInvalidClass(this)">
                        <label for="description" class="form-label fw-bold">Description</label><br>
                        <textarea class="form-control" name="description" cols="30" rows="5"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="store()">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
    const destroy = (id, context = '') => {
        if (confirm('Apakah anda yakin?')) {
            let url = urlFormatter(`<?= site_url(route_to('backend.posts.delete', ':id', ':context')); ?>`, id, context);

            $.ajax({
                type: "DELETE",
                url: url,
                dataType: "json",
                success: function(response) {
                    if (response.status == 1) alert(response.message);
                    reload();
                }
            });
        }
    }

    const store = () => {
        let url = '<?= site_url(route_to('backend.profiles.store.ajax')); ?>'

        $.ajax({
            type: "POST",
            url: url,
            cache: false,
            data: $('#addProfileForm').serialize(),
            success: function(response) {
                console.log(response);
                if (response.status) {
                    alert(response.message);
                    reload();
                    $('#summernote').summernote('reset');
                    $('#addProfileModal').modal('toggle');
                    $('#addProfileForm').trigger('reset');
                } else {
                    if (response.input_error) {
                        response.input_error.forEach(error => {
                            let input = $(`[name=${error.input_name}]`);

                            if (input.attr('type') != 'radio') input.val('');

                            switch (error.input_name) {
                                case 'content':
                                    input.next().addClass('is-invalid');
                                    input.next().next().text(error.error_message);
                                    $('#summernote').summernote('reset');
                                    break;
                                case 'status':
                                    input.addClass('is-invalid');
                                    input
                                        .parent()
                                        .parent()
                                        .find('.text-danger').text(error.error_message);
                                    break;
                                default:
                                    input.addClass('is-invalid')
                                    input.next().text(error.error_message);
                                    break;
                            }
                        });
                    }
                }
            },
        });
    }

    // ? Summernote
    $('#summernote').summernote({
        placeholder: 'Hello stand alone ui',
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
        let table = createDataTable('profileTable', '<?= site_url(route_to('backend.profiles.index.ajax')); ?>');
    });
</script>
<?= $this->endSection(); ?>