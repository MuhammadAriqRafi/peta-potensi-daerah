<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Peta Potensi Daerah</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>

<body>
    <?= $this->include('layout/flashMessageAlert'); ?>

    <div class="container d-flex align-items-center" style="height: 100vh;">
        <div class="container">
            <div class=" row d-flex justify-content-center">
                <main class="col-md-4">
                    <h1 class="mb-3">Login</h1>
                    <form action="<?= route_to('login.authenticate'); ?>" method="POST">
                        <?= csrf_field(); ?>

                        <!-- Username Input -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control <?= ($validation->hasError('username') ? 'is-invalid' : ''); ?>" value="<?= (old('username')); ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('username'); ?>
                            </div>
                        </div>
                        <!-- Title Input -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control <?= ($validation->hasError('password') ? 'is-invalid' : ''); ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('password'); ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </main>
            </div>
        </div>
    </div>
</body>

</html>