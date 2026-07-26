<?php
$roleName = strtolower((string) session()->get('role_name'));
$dashboardUrl = $roleName === 'administrador' ? 'administrador/dashboard' : ($roleName === 'funsionariu' ? 'funsionariu/dashboard' : 'dashboard');
$currentUri = uri_string();
?>
<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand d-flex align-items-center gap-3" href="<?= base_url($dashboardUrl); ?>">
            <img src="https://timor-leste.gov.tl/wp-content/themes/timor/images/logo.png" alt="Logo" class="rounded-circle shadow-sm" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid var(--border-light);">
            <span class="align-middle"><strong>SI - Maucatar</strong></span>
        </a>
        <ul class="sidebar-nav">
            <?php foreach (($navigation ?? []) as $category) : ?>
                <?php if (in_array(strtoupper($category['menu_category']), ['SELF SERVICE', 'SERVISU RASIK'], true) && $roleName === 'administrador') continue; ?>
                <li class="sidebar-header"><?= esc($category['menu_category']); ?></li>
                <?php foreach ($category['menus'] as $menu) : ?>
                    <?php $submenus = $menu['submenus'] ?? []; ?>
                    <?php if ((int) $menu['parent'] === 0) : ?>
                        <li class="sidebar-item <?= ($currentUri === $menu['url'] || strpos($currentUri, $menu['url']) !== false) ? 'active' : ''; ?>">
                            <a class="sidebar-link" href="<?= base_url($menu['url']); ?>">
                                <i class="align-middle" data-feather="<?= esc($menu['icon']); ?>"></i> <span class="align-middle"><?= esc($menu['title']); ?></span>
                            </a>
                        </li>
                    <?php else : ?>
                        <?php $parentActive = array_reduce($submenus, static fn (bool $active, array $submenu): bool => $active || strpos($currentUri, $submenu['url']) !== false, false); ?>
                        <li class="sidebar-item <?= $parentActive ? 'active' : ''; ?>">
                            <a data-bs-target="#<?= esc($menu['url']); ?>" data-bs-toggle="collapse" class="sidebar-link <?= $parentActive ? '' : 'collapsed'; ?>" aria-expanded="<?= $parentActive ? 'true' : 'false'; ?>">
                                <i class="align-middle" data-feather="<?= esc($menu['icon']); ?>"></i> <span class="align-middle"><?= esc($menu['title']); ?></span>
                            </a>
                            <ul id="<?= esc($menu['url']); ?>" class="sidebar-dropdown list-unstyled collapse <?= $parentActive ? 'show' : ''; ?>" data-bs-parent="#sidebar">
                                <?php foreach ($submenus as $submenu) : ?>
                                    <li class="sidebar-item <?= strpos($currentUri, $submenu['url']) !== false ? 'active' : ''; ?>">
                                        <a class="sidebar-link" href="<?= base_url($menu['url'] . '/' . $submenu['url']); ?>"><?= esc($submenu['title']); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <?php if ($roleName === 'administrador') : ?>
                <li class="sidebar-header">Analitika &amp; Relatóriu</li>
                <li class="sidebar-item <?= (isset($subsegment) && $subsegment === 'relatoriu') ? 'active' : ''; ?>">
                    <a class="sidebar-link" href="<?= base_url('administrador/relatoriu'); ?>"><i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Relatóriu</span></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
