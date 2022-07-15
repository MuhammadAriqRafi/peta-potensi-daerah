<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>
<table class="table" id="guestbookTable">
    <thead>
        <th>Action</th>
        <th>Title</th>
        <th>From</th>
        <th>Date</th>
        <th>Status</th>
    </thead>
    <tbody>
        <?php foreach ($guestbooks as $guestbook) : ?>
            <tr>
                <td>
                    <form action="<?= route_to('backend.guestbooks.destroy', $guestbook['guestbook_id']); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <a href="<?= route_to('backend.guestbooks.show', base64_encode($guestbook['guestbook_id'])); ?>" class="btn btn-sm btn-outline-primary">Read</a>
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
                <td><?= $guestbook['title']; ?></td>
                <td>
                    <?= $guestbook['name']; ?><br>
                    <small><?= $guestbook['email']; ?></small>
                </td>
                <td><?= date('d-m-Y', strtotime($guestbook['date_create'])); ?></td>
                <td>
                    <button type="button" class="pe-none btn btn-sm btn-<?= $guestbook['status'] == 'read' ? 'success' : 'secondary'; ?>"><?= $guestbook['status']; ?></button>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function() {
        // ? DataTables
        let table = $('#guestbookTable').DataTable({
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 99999],
                [10, 25, 50, 'All'],
            ],
            // TODO: Build serverSide functionality for datatables
            // ajax: '<?= site_url(route_to('backend.profiles.index.ajax')); ?>',
            // serverSide: true,
            // deferRender: true
        });
    });
</script>
<?= $this->endSection(); ?>