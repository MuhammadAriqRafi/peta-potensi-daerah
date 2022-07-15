<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<table class="table">
    <tbody>
        <?php foreach ($settings as $setting) : ?>
            <?php $id = base64_encode($setting['setting_id']); ?>

            <tr id="<?= $id; ?>">
                <th><?= $setting['keyword']; ?></th>
                <td>
                    <span onclick="showInput(this)"><?= $setting['value']; ?></span>

                    <div class="input-field mt-2 d-none" onclick="resetInvalidClass(this)">
                        <?php if ($setting['keyword'] != 'backsound' && $setting['type'] != 'textarea') : ?>
                            <input type="text" class="form-control" name="<?= $setting['keyword']; ?>" value="<?= $setting['value']; ?>">
                        <?php elseif ($setting['type'] == 'textarea') : ?>
                            <textarea name="<?= $setting['keyword']; ?>" class="form-control" cols="30" rows="10"><?= $setting['value']; ?></textarea>
                        <?php elseif ($setting['keyword'] == 'backsound') : ?>
                            <input type="file" class="form-control" name="<?= $setting['keyword']; ?>">
                        <?php endif ?>
                        <div class="invalid-feedback"></div>

                        <div class="btn-group mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="update('<?= $setting['keyword']; ?>', '<?= $id; ?>')">Ubah</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="hideInput('<?= $setting['keyword']; ?>')">Batal</button>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    const update = (inputName, id) => {
        let url = urlFormatter('<?= site_url(route_to('backend.settings.update.ajax', ':id')); ?>', id);
        let settingValue = $(`th:contains(${inputName})`).next().find('span');
        let settingInput = $(`[name="${inputName}"]`);
        let data = new FormData();

        data.append('_method', 'PATCH');
        data.append('id', id);

        if (inputName != 'backsound') {
            data.append('value', settingInput.val());
        } else {
            data.append('value', settingInput[0].files[0]);
        }

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $(`meta[name="X-CSRF-TOKEN"]`).attr('content'),
            },
            type: "POST",
            url: url,
            processData: false,
            contentType: false,
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    alert(response.message);
                    hideInput(inputName);
                    settingValue.text(response.data.value);

                    if (settingInput.attr('type') != 'file') settingInput.val(response.data.value);
                    else if (settingInput.attr('type') == 'file') settingInput.val('');
                } else {
                    response.input_error.forEach(error => {
                        settingInput.addClass('is-invalid');
                        settingInput.next().text(error.error_message);
                    });
                }

            }
        });
    }

    const showInput = (element) => {
        $(element).next().removeClass('d-none');
    }

    const hideInput = (keyword) => {
        $(`th:contains(${keyword})`).next().find('.input-field').addClass('d-none');
    }
</script>
<?= $this->endSection(); ?>