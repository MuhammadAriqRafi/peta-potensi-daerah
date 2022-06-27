<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<?php foreach ($visitors as $key => $visitor) : ?>
    <h1><?= $key . ' ' . $visitor; ?></h1>
<?php endforeach ?>
<?= $this->endSection(); ?>