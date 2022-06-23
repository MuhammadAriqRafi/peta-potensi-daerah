<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<table class="table">
    <tbody>
        <?php foreach ($settings as $setting) : ?>
            <tr>
                <th><?= $setting['keyword']; ?></th>
                <td><?= $setting['value']; ?></td>
                <td>
                    <a href="<?= route_to('backend.settings.edit', base64_encode($setting['setting_id'])); ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection(); ?>