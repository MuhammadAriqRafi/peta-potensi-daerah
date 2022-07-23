<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<div class="flex justify-between gap-4 flex-wrap sm:flex-nowrap">
    <?php foreach ($visitors as $key => $visitor) : ?>
        <div class="card grow basis-1/4 w-96 bg-primary text-primary-content">
            <div class="card-body">
                <h2 class="card-title font-bold text-base"><?= ucfirst($key); ?></h2>
                <p class="text-3xl font-light"><?= $visitor; ?></p>
            </div>
        </div>
    <?php endforeach ?>
</div>
<?= $this->endSection(); ?>