<?= $this->extend('layout/template'); ?>

<?= $this->section('css'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<div class="btn-group">
    <a href="<?= route_to('backend.maps.create'); ?>" class="btn btn-sm btn-outline-primary">Tambah Data</a>
    <a href="<?= route_to('backend.maps.categories.index'); ?>" class="btn btn-sm btn-outline-primary">Category</a>
</div>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>
<table class="table" id="mapTable">
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
                <td>
                    <form action="<?= route_to('backend.posts.delete', $map['post_id'], $map['post_type']); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <a href="<?= route_to('backend.maps.edit', base64_encode($map['post_id'])); ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah anda yakin?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#mapTable').DataTable({
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All'],
            ]
        });
    });
</script>
<?= $this->endSection(); ?>