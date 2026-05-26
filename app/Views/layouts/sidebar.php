<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand d-flex align-items-center gap-3" href="<?= base_url(); ?> ">
            <img src="https://timor-leste.gov.tl/wp-content/themes/timor/images/logo.png" alt="Logo" class="rounded-circle shadow-sm" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid var(--border-light);">
            <span class="align-middle"><strong>SI - Maucatar</strong></span>
        </a>
        <ul class="sidebar-nav">
            <?php 
                $current_uri = uri_string();
            ?>
            <?php foreach ($MenuCategory as $mCategory) : 
                if (strtoupper($mCategory['menu_category']) == 'SELF SERVICE' && strtolower(session()->get('role_name')) == 'administrador') continue;
            ?>
                <li class="sidebar-header">
                    <?= $mCategory['menu_category']; ?>
                </li>
                <?php
                $Menu = getMenu($mCategory['menuCategoryID'], $user['role_id']);
                foreach ($Menu as $menu) :
                    if ($menu['parent'] == 0) :
                        // Exact match or contains the URL
                        $activeClass = ($current_uri == $menu['url'] || strpos($current_uri, $menu['url']) !== false) ? 'active' : '';
                ?>
                        <li class="sidebar-item <?= $activeClass ?>">
                            <a class="sidebar-link" href="<?= base_url($menu['url']); ?> ">
                                <i class="align-middle" data-feather="<?= $menu['icon']; ?>"></i> <span class="align-middle"><?= $menu['title']; ?></span>
                            </a>
                        </li>
                    <?php
                    else :
                        $SubMenu =  getSubMenu($menu['menu_id'], $user['role_id']);
                        // Check if any submenu is active to keep parent open
                        $parentActive = false;
                        foreach($SubMenu as $sm) {
                            if (strpos($current_uri, $sm['url']) !== false) {
                                $parentActive = true;
                                break;
                            }
                        }
                    ?>
                        <li class="sidebar-item <?= $parentActive ? 'active' : ''; ?>">
                            <a data-bs-target="#<?= $menu['url'] ?>" data-bs-toggle="collapse" class="sidebar-link <?= $parentActive ? '' : 'collapsed' ?>" aria-expanded="<?= $parentActive ? 'true' : 'false' ?>">
                                <i class="align-middle" data-feather="<?= $menu['icon']; ?>"></i> <span class="align-middle"><?= $menu['title']; ?></span>
                            </a>
                            <ul id="<?= $menu['url'] ?>" class="sidebar-dropdown list-unstyled collapse <?= $parentActive ? 'show' : ''; ?> " data-bs-parent="#sidebar">
                                <?php foreach ($SubMenu as $subMenu) : ?>
                                    <li class="sidebar-item <?= (strpos($current_uri, $subMenu['url']) !== false) ? 'active' : ''; ?>">
                                        <a class="sidebar-link" href="<?= base_url($menu['url'] . '/' . $subMenu['url']); ?>">
                                            <?= $subMenu['title']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                <?php
                    endif;
                endforeach;
                ?>
            <?php endforeach; ?>

            <?php if (strtolower(session()->get('role_name')) == 'administrador') : ?>
                <li class="sidebar-header">
                    Analitika & Relatóriu
                </li>
                <li class="sidebar-item <?= (isset($subsegment) && $subsegment == 'relatoriu') ? 'active' : '' ?>">
                    <a class="sidebar-link" href="<?= base_url('administrador/relatoriu'); ?> ">
                        <i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Relatóriu</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>