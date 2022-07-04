<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<table class="table">
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
                        <!-- TODO: Create read view and set status to read -->
                        <a href="<?= route_to('backend.guestbooks.show', base64_encode($guestbook['guestbook_id'])); ?>" class="btn btn-sm btn-outline-primary">Read</a>
                        <input type="hidden" name="_method" value="DELETE">
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
                    <button type="button" class="btn btn-sm btn-secondary"><?= $guestbook['status']; ?></button>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?= $this->endSection(); ?>