<?php
$layoutUser = is_array($user ?? null) ? $user : [];
$notifications = is_array($avizu_notif ?? null) ? $avizu_notif : [];
$anunsiuCount = count($notifications);
?>
<nav class="navbar navbar-expand navbar-light navbar-bg custom-header">
    <a class="sidebar-toggle js-sidebar-toggle">
        <i class="hamburger align-self-center"></i>
    </a>

    <div class="navbar-collapse collapse">
        <div class="header-right ms-auto">
            <!-- Bell Icon / Anunsiu Dropdown -->
            <div class="relative">
                <div class="nav-icon-custom transition-standard" id="bellDropdownTrigger" title="Anunsiu" style="cursor: pointer;">
                    <i class="align-middle" data-feather="bell"></i>
                    <?php if ($anunsiuCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
                    <?php endif; ?>
                </div>

                <!-- Bell Dropdown Menu -->
                <div class="dropdown-custom dropdown-animate-in" id="bellDropdown" style="min-width: 320px;">
                    <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                        <p class="text-sm font-medium text-dark mb-0">Anunsiu</p>
                        <span class="badge bg-primary-light text-primary" style="font-size: 10px;"><?= $anunsiuCount ?> Foun</span>
                    </div>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <?php if (empty($notifications)): ?>
                            <div class="p-4 text-center">
                                <p class="text-xs text-muted mb-0">La iha anunsiu foun.</p>
                            </div>
                    <?php else: ?>
                            <?php foreach ($notifications as $a): ?>
                                <div class="dropdown-item-custom border-bottom" style="flex-direction: column; align-items: flex-start; gap: 4px; padding: 12px 16px;">
                                    <p class="text-sm font-medium text-dark mb-0"><?= esc($a['titulu']) ?></p>
                                    <p class="text-xs text-muted mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= esc(strip_tags($a['konteudu'])) ?></p>
                                    <span class="text-xs text-muted" style="font-size: 10px;"><?= date('d M Y', strtotime($a['data_publikasaun'])) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if (session()->get('role_name') === 'administrador'): ?>
                        <a href="<?= base_url('administrador/avizu') ?>" class="dropdown-item-custom text-center justify-content-center py-2 border-top">
                            <span class="text-xs font-medium text-primary">Haree Anunsiu Hotu</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Profile & Dropdown -->
            <div class="relative">
                <div class="profile-trigger transition-standard" id="profileDropdownTrigger">
                    <?php if (!empty($layoutUser['foto_perfil'])): ?>
                        <img src="<?= base_url('uploads/perfil/' . $layoutUser['foto_perfil']) ?>" class="avatar-custom" alt="<?= esc($layoutUser['fullname'] ?? 'User'); ?>" />
<?php else: ?>
                        <div class="avatar-custom avatar-placeholder">
                            <i data-feather="user" class="text-secondary" style="width: 18px; height: 18px;"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Dropdown Menu -->
                <div class="dropdown-custom dropdown-animate-in" id="profileDropdown">
                    <div class="px-4 py-3 border-bottom">
                        <p class="text-sm font-medium text-dark mb-0"><?= esc($layoutUser['fullname'] ?? 'User'); ?></p>
                        <p class="text-xs text-muted mb-0"><?= esc($layoutUser['username'] ?? 'User'); ?></p>
                    </div>
                    <form action="<?= base_url('logout') ?>" method="post" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="dropdown-item-custom danger w-100 border-0 bg-transparent">
                            <i data-feather="log-out"></i>
                            <span>Sai</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile Dropdown
        const profileTrigger = document.getElementById('profileDropdownTrigger');
        const profileDropdown = document.getElementById('profileDropdown');

        // Bell Dropdown
        const bellTrigger = document.getElementById('bellDropdownTrigger');
        const bellDropdown = document.getElementById('bellDropdown');

        if (profileTrigger && profileDropdown) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                if (bellDropdown) bellDropdown.classList.remove('show');
                profileDropdown.classList.toggle('show');
            });
        }

        if (bellTrigger && bellDropdown) {
            bellTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                if (profileDropdown) profileDropdown.classList.remove('show');
                bellDropdown.classList.toggle('show');
            });
        }

        document.addEventListener('click', function(e) {
            if (profileDropdown && profileTrigger && !profileDropdown.contains(e.target) && !profileTrigger.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
            if (bellDropdown && bellTrigger && !bellDropdown.contains(e.target) && !bellTrigger.contains(e.target)) {
                bellDropdown.classList.remove('show');
            }
        });
        
        // Refresh feather icons for new elements
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
