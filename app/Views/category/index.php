<?= $this->extend('layout/template'); ?>

<?= $this->section('modal'); ?>
<!-- Put this part before </body> tag -->
<input type="checkbox" id="categoryModal" class="modal-toggle" />
<div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box sm:p-8">
        <h3 id="categoryModalLabel" class="font-bold text-2xl sm:text-4xl mb-6">Tambah Category</h3>

        <!-- Category Form -->
        <form action="#" id="categoryForm">
            <?= csrf_field(); ?>
        </form>
        <!-- End of Category Form -->
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('toolbar'); ?>
<!-- The button to open modal -->
<label for="categoryModal" class="btn modal-button" onclick="create()">Tambah Data</label>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<table class="table table-zebra w-full" id="categoryTable">
    <thead>
        <tr>
            <th>Title</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <!-- <?php foreach ($categories as $category) : ?>
            <tr>
                <td><?= $category['title']; ?></td>
                <td>
                    <form action="<?= route_to('backend.maps.categories.destroy', $category['category_id']); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <a href="<?= route_to('backend.maps.categories.edit', base64_encode($category['category_id'])); ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach ?> -->
    </tbody>
</table>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('js/ajaxUtilities.js') ?>"></script>
<script>
    const tableId = 'categoryTable';
    const categoryForm = 'categoryForm';
    const categoryModal = 'categoryModal';
    const categoryModalLabel = 'categoryModalLabel';
    const categoryFormActionBtn = 'categoryFormActionBtn';

    // Helper
    const displayError = (inputError) => {
        inputError.forEach(error => {
            $(`input[name="${error.input_name}"]`).addClass('input-error');
            $(`#error-${error.input_name}`).removeClass('hidden');
            $(`#error-${error.input_name}`).text(error.error_message);
        });
    }

    const setFormState = (state, id = null) => {
        const capitalizedStateFirstLetter = state.charAt(0).toUpperCase() + state.slice(1);

        resetInvalidClass($(`#${categoryForm}`));
        if (capitalizedStateFirstLetter == 'Tambah') {
            // ? Set form action for 'Tambah'
            $(`#${categoryForm}`).attr('action', siteUrl + '<?= $storeUrl; ?>');

            // ? If form current state is already 'Tambah'
            if ($('input[name="_method"]').length < 1) return true;

            $('input[name="_method"]').remove();
            $('.img-preview').attr('src', '#');
            $(`#${categoryFormActionBtn}`).text(capitalizedStateFirstLetter);
            $(`#${categoryModalLabel}`).text(`${capitalizedStateFirstLetter} Category`);
            $(`#${categoryForm}`).trigger('reset');
        } else if (capitalizedStateFirstLetter == 'Ubah') {
            // ? Set form action for 'Ubah'
            $(`#${categoryForm}`).attr('action', siteUrl + '<?= $updateUrl; ?>' + id);

            // ? If form current state is already 'Ubah'
            if ($('input[name="_method"]').length > 1) return true;

            $(`#${categoryForm}`).prepend(`
                    <input type="hidden" name="_method" value="PATCH">
                `);
            $(`#${categoryFormActionBtn}`).text(capitalizedStateFirstLetter);
            $(`#${categoryModalLabel}`).text(`${capitalizedStateFirstLetter} Category`);
        }
    }

    const previewImg = () => {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('.img-preview');
        const filePhoto = new FileReader();
        filePhoto.readAsDataURL(image.files[0]);
        filePhoto.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }

    // CRUD
    const create = () => {
        setFormState('Tambah');
        $(`#${categoryModal}`).prop('checked', false);
    }

    const store = (data) => {
        const url = $(`#${categoryForm}`).attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    reload(tableId);
                    $(`#${categoryForm}`).trigger('reset');
                    $('.img-preview').attr('src', '#');
                    alert(response.message);
                    $(`#${categoryModal}`).prop('checked', false);
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            }
        })
    };

    const edit = (id) => {
        const url = siteUrl + '<?= $editUrl ?>' + id;

        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function(response) {
                setFormState('Ubah', id);

                $('input[name="title"]').val(response.title);
                $('textarea[name="description"]').val(response.description);
                if (response.image) $('.img-preview').attr('src', baseUrl + `/img/${response.image}`);

                $(`#${categoryModal}`).prop('checked', true);
            }
        });
    }

    const update = (data) => {
        const url = $(`#${categoryForm}`).attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    alert(response.message);
                    reload(tableId);
                    $('input[name="image"]').val();
                } else {
                    if (response.input_error) displayError(response.input_error);
                }
            }
        });
    }

    const save = () => {
        const form = $(`#${categoryForm}`)[0];
        const data = new FormData(form);

        if ($('input[name="_method"]').length > 0) update(data);
        else store(data);
    }

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
                    alert(response.message);
                    if (response.status) reload(tableId);
                }
            });
        }
    }

    $(document).ready(function() {
        const table = createDataTable(tableId, siteUrl + '<?= $indexUrl ?>', [{
                name: 'title',
                data: 'title'
            },
            {
                data: 'category_id',
                orderable: false,
                searchabel: false,
                render: function(data) {
                    return editDeleteBtn(data);
                }
            }
        ]);

        $(`#${categoryForm}`).append(textInputComponent('Title', 'title'));
        $(`#${categoryForm}`).append(textareaComponent('Description', 'description'));
        $(`#${categoryForm}`).append(fileInputComponent('Image', 'image'));

        // ? Modal Action Buttons
        $(`#${categoryForm}`).append(`
            <div class="modal-action">
                <label for="${categoryModal}" class="btn btn-error">Batal</label>
                <label id="${categoryFormActionBtn}" class="btn btn-primary" onclick="save()">Tambah</label>
            </div>
        `)
    });
</script>
<?= $this->endSection(); ?>