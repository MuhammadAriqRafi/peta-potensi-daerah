<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<table class="table table-zebra w-full" id="guestbookTable">
    <thead>
        <tr>
            <th>Action</th>
            <th>Title</th>
            <th>From</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
</table>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js'); ?>"></script>
<script>
    const tableId = 'guestbookTable';

    // CRUD
    const destroy = (id) => {
        if (confirm('Apakah anda yakin?')) {
            const url = siteUrl + '<?= $destroyUrl ?>' + id;

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _method: 'DELETE'
                },
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        reload(tableId);
                    }
                }
            });
        }
    }

    $(document).ready(function() {
        // ? DataTables
        const table = createDataTable(tableId, siteUrl + '<?= $indexUrl ?>', [{
                data: 'guestbook_id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <td>
                            <a href="${siteUrl + '<?= $showUrl ?>' + data}" class="btn btn-sm btn-primary">Read</a>
                            <a href="#" class="btn btn-sm btn-error" onclick="destroy('${data}')">Delete</a>
                        </td>
                    `;
                }
            },
            {
                name: 'title',
                data: 'title'
            },
            {
                name: 'from',
                render: function(data, type, row) {
                    return `
                        <td>
                            ${row.name}<br>
                            <small>${row.email}</small>
                        </td>
                    `;
                }
            },
            {
                name: 'date_create',
                data: 'date_create'
            },
            {
                name: 'status',
                data: 'status',
                render: function(data) {
                    return `
                        <td>
                            <button type="button" class="pe-none btn btn-sm btn-${data == 'read' ? 'success' : 'secondary'}">${data}</button>
                        </td>
                    `
                }
            },
        ]);
    });
</script>
<?= $this->endSection(); ?>