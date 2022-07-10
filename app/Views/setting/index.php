<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<table class="table">
    <tbody>
        <?php foreach ($settings as $setting) : ?>
            <tr>
                <th><?= $setting['keyword']; ?></th>
                <td onclick="transformInput(this)"><?= $setting['value']; ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    const update = (id, element) => {
        let url = urlFormatter('<?= site_url(route_to('backend.settings.update.ajax', ':id')); ?>', id);

        $.ajax({
            type: "PATCH",
            url: url,
            data: $(element).val().serialize(),
            dataType: "json",
            success: function(response) {
                console.log(response);
            }
        });
    }

    const transformInput = (element) => {
        let currentElement = $(element);
        let currentElementText = $(element).text();

        if (currentElement.children().length < 1) {
            currentElement.text('');
            currentElement.append(`
                <form method="POST">
                    <input type="hidden" name="id">
                    <input id="settingInput" type="text" class="form-control" name="${currentElement.prev().text()}">
                    <div class="btn-group mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="update()">Ubah</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="resetInput(this, '${currentElementText}')">Batal</button>
                    </div>
                </form>    
            `);
        }
    }

    // TODO: Cannot re open the input

    const resetInput = (element, defaultValue) => {
        $(element).parent().parent().parent().html(`<p class="m-0">${defaultValue}</p>`);
    }
</script>
<!-- <a href="<?= route_to('backend.settings.edit', base64_encode($setting['setting_id'])); ?>" class="btn btn-sm btn-outline-warning">Edit</a> -->
<?= $this->endSection(); ?>