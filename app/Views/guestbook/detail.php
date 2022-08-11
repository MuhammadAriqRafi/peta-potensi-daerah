<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<?php
helper('guestbook');
setStatusRead($guestbook['guestbook_id']);
?>

<!-- Message Header -->
<div class="flex justify-between mb-6">
    <div class="flex gap-x-4">
        <span>From :</span>
        <div>
            <p class="font-bold"><?= $guestbook['name']; ?></p>
            <small><?= $guestbook['email']; ?></small>
        </div>
    </div>
    <div><?= date('d-m-Y', strtotime($guestbook['date_create'])); ?></div>
</div>

<!-- Divider -->
<hr>

<!-- Message Content -->
<p class="mt-5"><?= $guestbook['messages']; ?></p>

<a href="<?= route_to('backend.guestbooks.index'); ?>" class="btn btn-error mt-10">Back</a>
<?= $this->endSection(); ?>