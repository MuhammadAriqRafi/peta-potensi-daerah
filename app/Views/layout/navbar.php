<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-secondary active" aria-current="page" href="<?= route_to('home'); ?>">
                    <span data-feather="home"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="<?= route_to('backend.profiles.index'); ?>">
                    <span data-feather="file"></span>
                    Tentang Aplikasi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="<?= route_to('backend.maps.index'); ?>">
                    <span data-feather="file"></span>
                    Map Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="<?= route_to('backend.administrators.index'); ?>">
                    <span data-feather="file"></span>
                    Administrator
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="<?= route_to('backend.menus.index'); ?>">
                    <span data-feather="file"></span>
                    Menu Manager
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="<?= route_to('backend.settings.index'); ?>">
                    <span data-feather="file"></span>
                    Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="<?= route_to('backend.popups.index'); ?>">
                    <span data-feather="file"></span>
                    Popup Manager
                </a>
            </li>
        </ul>
    </div>
</nav>