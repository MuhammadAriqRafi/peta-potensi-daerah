<?= $this->extend('layout/template'); ?>

<?= $this->section('css'); ?>
<!-- Summernote -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<form action="<?= route_to('backend.profiles.update', $profile['post_id']); ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <input type="hidden" name="_method" value="PATCH">
    <!-- Title Input -->
    <div class="mb-3">
        <label for="title" class="form-label fw-bold">Title</label>
        <input type="text" name="title" class="form-control <?= ($validation->hasError('title') ? 'is-invalid' : ''); ?>" value="<?= (old('title', $profile['title'])); ?>">
        <?php if ($validation->hasError('title')) : ?>
            <div class="invalid-feedback">
                <?= $validation->getError('title'); ?>
            </div>
        <?php endif ?>
    </div>
    <!-- Date Publish Date Input -->
    <div class="mb-3">
        <label for="date_publish" class="form-label fw-bold">Date Publish</label>
        <input type="date" name="date_publish" class="form-control <?= ($validation->hasError('date_publish') ? 'is-invalid' : ''); ?>" value="<?= (old('date_publish', $profile['date_publish'])); ?>" min="1900-01-01" max="<?= date("Y-12-31"); ?>">
        <?php if ($validation->hasError('date_publish')) : ?>
            <div class="invalid-feedback">
                <?= $validation->getError('date_publish'); ?>
            </div>
        <?php endif ?>
    </div>
    <!-- Content Textarea -->
    <div class="mb-3">
        <label for="content" class="form-label fw-bold">Content</label>
        <textarea id="summernote" name="content"><?= old('content', $profile['content']); ?></textarea>
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
                <input class="form-check-input" type="radio" name="status" value="<?= $status; ?>" <?= $profile['status'] == $status ? 'checked' : ''; ?>>
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
        <textarea class="form-control <?= ($validation->hasError('description') ? 'is-invalid' : ''); ?>" name="description" cols="30" rows="5"><?= old('description', $profile['description']); ?></textarea>
        <?php if ($validation->hasError('description')) : ?>
            <div class="invalid-feedback">
                <?= $validation->getError('description'); ?>
            </div>
        <?php endif ?>
    </div>

    <button type="submit" class="btn btn-primary my-4">Ubah</button>
    <a href="<?= route_to('backend.profiles.index'); ?>" class="btn btn-danger">Batal</a>
</form>
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