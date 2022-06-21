<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<!-- Flash Data -->
<?php if (session()->getFlashdata()) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif ?>

<table>
    <thead>
        <?php foreach ($menus as $menu) : ?>
            <tr>
                <?php $id = base64_encode($menu['menu_id']); ?>
                <td id="<?= $id ?>" onclick="editMenu(this)" style="cursor: pointer;"><?= $menu['title']; ?></td>
            </tr>
        <?php endforeach ?>
    </thead>
</table>

<br><br>

<form action="<?= route_to('backend.menus.update', base64_encode($menus[0]['menu_id'])); ?>" method="POST" id="editMenuForm">
    <?= csrf_field(); ?>
    <input type="hidden" name="_method" value="PATCH">
    <div class="modal-body">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control <?= $validation->hasError('title') ? 'is-invalid' : ''; ?>" id="title" name="title" autofocus value="<?= old('title', $menus[0]['title']); ?>">
            <div class="invalid-feedback">
                <?= $validation->getError('title'); ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="url" class="form-label">Url</label>
            <input type="text" class="form-control <?= $validation->hasError('url') ? 'is-invalid' : ''; ?>" id="url" name="url" autofocus value="<?= old('url', $menus[0]['url']); ?>">
            <div class="invalid-feedback">
                <?= $validation->getError('url'); ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Ubah</button>
    </div>
</form>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    const editMenuForm = document.getElementById('editMenuForm');
    const inputField = document.getElementById('title');

    const editMenu = (element) => {
        let id = element.getAttribute('id');
        let elementContent = element.innerText;

        editMenuForm.setAttribute('action', `<?= route_to('backend.menus.index') ?>/${id}`);
        inputField.setAttribute('value', elementContent);
    }
</script>
<?= $this->endSection(); ?>