<?= $this->extend('layout/template'); ?>

<?= $this->section('toolbar'); ?>
<a href="<?= route_to('backend.maps.create'); ?>" class="btn btn-sm btn-outline-primary">Tambah Data</a>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Author</th>
            <th>Date Publish</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($maps as $map) : ?>
            <tr>
                <td><?= $map['title']; ?></td>
                <td><?= $map['category']; ?></td>
                <td><?= $map['author']; ?></td>
                <td><?= $map['date_publish']; ?></td>
                <td><?= $map['status']; ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?= $this->endSection(); ?>