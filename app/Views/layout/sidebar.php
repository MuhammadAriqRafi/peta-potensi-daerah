<?php $uri = service('uri') ?>

<li>
    <a class="<?= $uri->getSegment(2) === '' ? 'active font-bold' : ''; ?>" href="<?= route_to('backend.dashboard.index'); ?>">Dashboard</a>
</li>
<li>
    <a class="<?= $uri->getSegment(2) === 'profiles' ? 'active font-bold' : ''; ?>" href="<?= route_to('backend.profiles.index'); ?>">Tentang Aplikasi</a>
</li>
<li>
    <a class="<?= $uri->getSegment(2) === 'maps' ? 'active font-bold' : ''; ?>" href="<?= route_to('backend.maps.index'); ?>">Map Settings</a>
</li>
<li>
    <a class="<?= $uri->getSegment(2) === 'administrators' ? 'active font-bold' : ''; ?>" href="<?= route_to('backend.administrators.index'); ?>">Administrator</a>
</li>
<li>
    <a class="<?= $uri->getSegment(2) === 'menus' ? 'active font-bold' : ''; ?>" href="<?= route_to('backend.menus.index'); ?>">Menu Manager</a>
</li>
<li>
    <a class="<?= $uri->getSegment(2) === 'settings' ? 'active font-bold' : ''; ?>" href="<?= route_to('backend.settings.index'); ?>">Settings</a>
</li>
<li>
    <a class="<?= $uri->getSegment(2) === 'popups' ? 'active font-bold' : ''; ?>" href="<?= route_to('backend.popups.index'); ?>">Popup Manager</a>
</li>