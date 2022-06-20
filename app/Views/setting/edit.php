<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<!-- Flash Message -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif ?>

<form action="<?= route_to('backend.settings.update', $setting['setting_id']); ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <input type="hidden" name="_method" value="PATCH">
    <div class="mb-3">
        <label for="value" class="form-label">Value</label>

        <?php if ($setting['type'] == 'textarea') : ?>
            <textarea class="form-control <?= ($validation->hasError('value') ? 'is-invalid' : ''); ?>" name="value" cols="30" rows="10"><?= (old('value', $setting['value'])); ?></textarea>
        <?php elseif ($setting['keyword'] == 'backsound') : ?>
            <input class="form-control <?= ($validation->hasError('value') ? 'is-invalid' : ''); ?>" type="file" name="value">
        <?php else : ?>
            <input type="text" name="value" class="form-control <?= ($validation->hasError('value') ? 'is-invalid' : ''); ?>" id="value" value="<?= (old('value', $setting['value'])); ?>">
        <?php endif ?>

        <?php if ($validation->hasError('value')) : ?>
            <div class="invalid-feedback">
                <?= $validation->getError('value'); ?>
            </div>
        <?php endif ?>
    </div>
    <button type="submit" class="btn btn-primary my-4">Ubah</button>
    <a href="<?= route_to('backend.settings.index'); ?>" class="btn btn-danger my-4">Cancel</a>
</form>
<?= $this->endSection(); ?>