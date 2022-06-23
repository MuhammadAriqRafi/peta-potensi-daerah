<?= $this->extend('layout/template'); ?>

<?= $this->section('css'); ?>
<!-- Summernote -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<!-- Button trigger modal -->
<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Tambah Data
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Tentang Aplikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= route_to('backend.profiles.store'); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <!-- Title Input -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control <?= ($validation->hasError('title') ? 'is-invalid' : ''); ?>" value="<?= (old('title')); ?>">
                        <?php if ($validation->hasError('title')) : ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('title'); ?>
                            </div>
                        <?php endif ?>
                    </div>
                    <!-- Date Publish Date Input -->
                    <div class="mb-3">
                        <label for="date_publish" class="form-label fw-bold">Date Publish</label>
                        <input type="date" name="date_publish" class="form-control <?= ($validation->hasError('date_publish') ? 'is-invalid' : ''); ?>" value="<?= (old('date_publish', date("Y-m-d"))); ?>" min="1900-01-01" max="<?= date("Y-12-31"); ?>">
                        <?php if ($validation->hasError('date_publish')) : ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('date_publish'); ?>
                            </div>
                        <?php endif ?>
                    </div>
                    <!-- Content Textarea -->
                    <div class="mb-3">
                        <label for="content" class="form-label fw-bold">Content</label>
                        <textarea id="summernote" name="content"></textarea>
                        <?php if ($validation->hasError('content')) : ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('content'); ?>
                            </div>
                        <?php endif ?>
                    </div>
                    <!-- Status Radio Input -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Status</label><br>
                        <?php foreach ($statuses as $status) : ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="<?= $status; ?>">
                                <label class="form-check-label" for="<?= $status; ?>"><?= ucfirst($status); ?></label>
                            </div>
                        <?php endforeach ?>
                        <?php if ($validation->hasError('status')) : ?>
                            <p class="text-danger mt-2 fs-6"><?= $validation->getError('status'); ?></p>
                        <?php endif ?>
                    </div>
                    <!-- Description Textarea -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label><br>
                        <textarea class="form-control <?= ($validation->hasError('description') ? 'is-invalid' : ''); ?>" name="description" cols="30" rows="5"><?= old('description'); ?></textarea>
                        <?php if ($validation->hasError('description')) : ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('description'); ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>


<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>

<table class="table">
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
        <?php foreach ($profiles as $profile) : ?>
            <tr>
                <td><?= $profile['title']; ?></td>
                <td><?= $profile['author']; ?></td>
                <td><?= $profile['date_publish']; ?></td>
                <td><?= $profile['status']; ?></td>
                <td>
                    <form action="<?= route_to('backend.posts.delete', $profile['post_id'], $profile['post_type']); ?> " method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <a href="<?= route_to('backend.profiles.edit', base64_encode($profile['post_id'])); ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah anda yakin?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
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
</script>
<?= $this->endSection(); ?>