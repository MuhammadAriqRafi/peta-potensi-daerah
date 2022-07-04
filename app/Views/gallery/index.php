<?= $this->extend('layout/template'); ?>

<?= $this->section('css'); ?>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

<script>
    Dropzone.options.fotoTempat = {
        paramName: 'image',
        autoProcessQueue: false,
        addRemoveLinks: true,
        parallelUploads: 5,
        maxFileSize: 2048,
        acceptedFiles: 'image/jpg, image/jpeg, image/png',

        init: function() {
            let myDropzone = this;

            document.getElementById('dropzoneBtn').addEventListener('click', function() {
                myDropzone.processQueue();
            })

            this.on('complete', function(file) {
                myDropzone.removeFile(file);
            })

            this.on('successmultiple', function(response) {
                console.log(response);
            })
        }
    };
</script>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<form action="<?= route_to('backend.maps.galleries.store', $post_id); ?>" class="dropzone" method="POST" id="foto-tempat">
    <input type="hidden" name="post_id" value="<?= $post_id; ?>">
</form>
<button id="dropzoneBtn" class="btn btn-outline-info my-4">Submit</button>

<?php if (count($fotos) > 0) : ?>
    <table class="table">
        <thead>
            <th>Sort</th>
            <th>Image</th>
            <th>Action</th>
        </thead>
        <tbody>
            <?php foreach ($fotos as $foto) : ?>
                <tr style="vertical-align: middle;">
                    <td><?= $foto['sort'] ?></td>
                    <td>
                        <img src="<?= base_url('img/' . $foto['filename']); ?>" alt="Gallery" width="150">
                    </td>
                    <td>
                        <a href="#" class="btn btn-sm btn-outline-danger">Delete</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php else : ?>
    <p class="fw-bold text-danger">Galeri foto masih kosong</p>
<?php endif ?>

<a href="<?= route_to('backend.maps.edit', $post_id); ?>" class="btn btn-danger my-4">Back</a>
<?= $this->endSection(); ?>