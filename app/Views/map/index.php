<?= $this->extend('layout/template'); ?>

<?= $this->section('toolbar'); ?>
<div class="btn-group">
    <a href="<?= route_to('backend.maps.create'); ?>" class="btn btn-sm btn-outline-primary">Tambah Data</a>
    <a href="<?= route_to('backend.maps.categories.index'); ?>" class="btn btn-sm btn-outline-primary">Category</a>
</div>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<?= $this->include('layout/flashMessageAlert'); ?>
<table class="table table-zebra w-full" id="mapTable">
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
    </tbody>
</table>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    const store = () => {

    }

    const destroy = (id, context = '') => {
        if (confirm('Apakah anda yakin?')) {
            let url = urlFormatter(`<?= site_url(route_to('backend.posts.delete', ':id', ':context')); ?>`, id, context);

            $.ajax({
                type: "DELETE",
                url: url,
                dataType: "json",
                success: function(response) {
                    if (response.status == 1) alert(response.message);
                },
                complete: function() {
                    // TODO: Consider not reloading the data, instead delete particular row in datatables
                    reload();
                }
            });
        }
    }

    const update = (id) => {}

    // TODO: Build sorting datatable functionality, read the documentation
    $(document).ready(function() {
        let table = createDataTable('mapTable', '<?= site_url(route_to('backend.maps.index.ajax')); ?>');
    });
</script>
<?= $this->endSection(); ?>