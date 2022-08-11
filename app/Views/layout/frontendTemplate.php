<!DOCTYPE html>
<html lang="en" data-theme="lemonade">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Potensi Daerah</title>

    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!-- Custom styles for this template -->
    <link href="<?= base_url('css/dashboard.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('css/app.css'); ?>" rel="stylesheet">

    <!-- Storing Utility Js Variables -->
    <script>
        let siteUrl = '<?= site_url(); ?>';
        let baseUrl = '<?= base_url(); ?>';
    </script>
</head>

<body>
    <?php
    checkSessionId();
    ?>

    <?= $this->renderSection('content'); ?>

    <?= $this->renderSection('script'); ?>
</body>

</html>