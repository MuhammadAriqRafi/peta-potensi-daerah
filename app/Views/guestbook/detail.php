<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid d-flex justify-content-between">
    <div class="d-flex">
        <span>From :</span>
        <div class="ms-3">
            <p class="fw-bold m-0"><?= $guestbook['name']; ?></p>
            <small class="m-0"><?= $guestbook['email']; ?></small>
        </div>
    </div>
    <div><?= date('d-m-Y', strtotime($guestbook['date_create'])); ?></div>
</div>
<hr>

<!-- Message Content -->
<p class="mt-5"><?= $guestbook['messages']; ?></p>

<a href="<?= route_to('backend.guestbooks.index'); ?>" class="btn btn-danger mt-4">Cancel</a>
<?= $this->endSection(); ?>