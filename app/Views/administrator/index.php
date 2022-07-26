<?= $this->extend('layout/template'); ?>

<?= $this->section('modal'); ?>
<!-- Put this part before </body> tag -->
<input type="checkbox" id="administratorModal" class="modal-toggle" />
<div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box sm:p-8">
        <h3 class="font-bold text-2xl sm:text-4xl mb-6">Tambah Tentang Aplikasi</h3>

        <!-- Popup Form -->
        <form action="#" id="administratorForm">
            <?= csrf_field(); ?>
            <!-- NIK Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">NIK</span>
                <input type="text" class="input input-bordered w-full max-w-xs my-2" name="nik" />
                <div id="error-nik" class="badge badge-error hidden"></div>
            </div>
            <!-- Nama Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Nama</span>
                <input type="text" class="input input-bordered w-full max-w-xs my-2" name="nama" />
                <div id="error-nama" class="badge badge-error hidden"></div>
            </div>
            <!-- Username Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Username</span>
                <input type="text" class="input input-bordered w-full max-w-xs my-2" name="username" />
                <div id="error-username" class="badge badge-error hidden"></div>
            </div>
            <!-- Password Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Password</span>
                <input type="password" class="input input-bordered w-full max-w-xs my-2" name="password" />
                <div id="error-password" class="badge badge-error hidden"></div>
            </div>
            <!-- Password Confirm Input -->
            <div class="form-control mb-4" onclick="resetInvalidClass(this)">
                <span class="label-text font-bold">Password Confirm</span>
                <input type="password" class="input input-bordered w-full max-w-xs my-2" name="passconf" />
                <div id="error-passconf" class="badge badge-error hidden"></div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="modal-action">
                <label for="administratorModal" class="btn btn-error">Batal</label>
                <label class="btn btn-primary" onclick="store()">Tambah</label>
            </div>
        </form>
        <!-- End of Popup Form -->
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<!-- The button to open modal -->
<label for="administratorModal" class="btn modal-button">Tambah Data</label>
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
    // Helper
    const displayError = (inputError) => {
        inputError.forEach(error => {
            const input = $(`[name=${error.input_name}]`);

            $(`#error-${error.input_name}`).removeClass('hidden');
            $(`#error-${error.input_name}`).text(error.error_message);
            input.addClass('input-error');
        });
    }

    // CRUD
    const store = () => {
        const url = siteUrl + '<?= $storeUrl; ?>';
        const form = $('#administratorForm')[0];
        const data = new FormData(form);

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
                    $('administratorForm').trigger('reset');
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            }
        });
    }

    $(document).ready(function() {
        const table = createDataTable('administratorTable', siteUrl + '<?= $indexUrl; ?>');
    });
</script>
<?= $this->endSection(); ?>