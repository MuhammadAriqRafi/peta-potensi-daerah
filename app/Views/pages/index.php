<?= $this->extend('layout/frontendTemplate'); ?>

<?= $this->section('content'); ?>
<div class="flex justify-center items-center h-screen">
    <div class="flex flex-col w-6/12">
        <div class="flex justify-between items-center">
            <h1 class="text-4xl font-bold">Beranda</h1>
            <a href="<?= route_to('login.index'); ?>" class="btn btn-primary h-min">Login</a>
        </div>

        <div>
            <h3 class="mt-10 mb-6 text-2xl font-semibold">Kontak Kami</h3>
            <form id="guestbookForm">
                <?= csrf_field(); ?>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/formUtilities.js') ?>"></script>
<script src="<?= base_url('js/ajaxUtilities.js') ?>"></script>
<script>
    const guestbookForm = 'guestbookForm';

    const store = () => {
        const url = siteUrl + '<?= $storeUrl ?>';
        const form = $(`#${guestbookForm}`)[0];
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
                    $(`#${guestbookForm}`).trigger('reset');
                } else {
                    if (response.input_error) {
                        response.input_error.forEach(error => {
                            $(`input[name="${error.input_name}"]`).addClass('input-error');
                            $(`textarea[name="${error.input_name}"]`).addClass('textarea-error');
                            $(`#error-${error.input_name}`).removeClass('hidden');
                            $(`#error-${error.input_name}`).text(error.error_message);
                        });
                    }
                }
            }
        });
    }

    $(document).ready(function() {
        $(`#${guestbookForm}`).append(textInputComponent('Nama', 'name'));
        $(`#${guestbookForm}`).append(textInputComponent('Email', 'email', 'email'));
        $(`#${guestbookForm}`).append(textInputComponent('Judul', 'title'));
        $(`#${guestbookForm}`).append(textareaComponent('Pesan', 'messages'));
        $(`#${guestbookForm}`).append(`<button type="button" class="btn btn-primary mt-6" onclick="store()">Submit</button>`);
    });
</script>
<?= $this->endSection(); ?>