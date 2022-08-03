<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<table class="table">
    <tbody>
        <?php foreach ($settings as $setting) : ?>
            <?php $id = base64_encode($setting['setting_id']); ?>

            <tr id="<?= $id; ?>" class="break-words">
                <th><?= $setting['keyword']; ?></th>
                <td>
                    <span onclick="showInput(this)"><?= $setting['value']; ?></span>

                    <div class="form-control mt-2 hidden" onclick="resetInvalidClass(this)">
                        <?php if ($setting['keyword'] != 'backsound') : ?>
                            <?php if ($setting['type'] != 'textarea') : ?>
                                <input type="text" class="input input-bordered w-full max-w-xs" name="<?= $setting['keyword']; ?>" value="<?= $setting['value']; ?>">
                            <?php elseif ($setting['type'] == 'textarea') : ?>
                                <textarea name="<?= $setting['keyword']; ?>" class="textarea textarea-bordered max-w-xs"><?= $setting['value']; ?></textarea>
                            <?php endif ?>
                        <?php elseif ($setting['keyword'] == 'backsound') : ?>
                            <input type="file" name="<?= $setting['keyword']; ?>">
                        <?php endif ?>
                        <div class="badge badge-error mt-2 mb-4 hidden"></div>

                        <div class="btn-group mt-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="update('<?= $setting['keyword']; ?>', '<?= $id; ?>')">Ubah</button>
                            <button type="button" class="btn btn-sm btn-error" onclick="hideInput('<?= $setting['keyword']; ?>')">Batal</button>
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
    // Helper
    const showInput = (element) => {
        $(element).next().removeClass('hidden');
    }

    const hideInput = (keyword) => {
        $(`th:contains(${keyword})`).next().find('.form-control').addClass('hidden');
    }

    // CRUD
    const update = (inputName, id) => {
        const url = siteUrl + '<?= $updateUrl; ?>' + id;
        let settingValue = $(`th:contains(${inputName})`).next().find('span');
        let settingInput = $(`[name="${inputName}"]`);
        let data = new FormData();

        data.append('_method', 'PATCH');
        if (inputName != 'backsound') {
            data.append('value', settingInput.val());
        } else {
            data.append('value', settingInput[0].files[0]);
        }

        $.ajax({
            // headers: {
            //     "X-CSRF-TOKEN": $(`meta[name="X-CSRF-TOKEN"]`).attr('content'),
            // },
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
                        settingInput.addClass('input-error');
                        settingInput.next().text(error.error_message);
                        settingInput.next().removeClass('hidden');
                    });
                }
            }
        });
    }
</script>
<?= $this->endSection(); ?>