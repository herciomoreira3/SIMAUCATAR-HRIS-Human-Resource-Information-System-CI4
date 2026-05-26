<nav class="navbar navbar-expand navbar-light navbar-bg custom-header">
    <a class="sidebar-toggle js-sidebar-toggle">
        <i class="hamburger align-self-center"></i>
    </a>

    <div class="navbar-collapse collapse">
        <div class="header-right ms-auto">
            <!-- Bell Icon / Avizu Dropdown -->
            <div class="relative">
                <div class="nav-icon-custom transition-standard" id="bellDropdownTrigger" title="Avizu" style="cursor: pointer;">
                    <i class="align-middle" data-feather="bell"></i>
                    <?php if (count($avizu_notif) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
                    <?php endif; ?>
                </div>

                <!-- Bell Dropdown Menu -->
                <div class="dropdown-custom dropdown-animate-in" id="bellDropdown" style="min-width: 320px;">
                    <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                        <p class="text-sm font-medium text-dark mb-0">Avizu / Notifikasaun</p>
                        <span class="badge bg-primary-light text-primary" style="font-size: 10px;"><?= count($avizu_notif) ?> Foun</span>
                    </div>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <?php if (empty($avizu_notif)): ?>
                            <div class="p-4 text-center">
                                <p class="text-xs text-muted mb-0">La iha avizu foun.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($avizu_notif as $a): ?>
                                <div class="dropdown-item-custom border-bottom" style="flex-direction: column; align-items: flex-start; gap: 4px; padding: 12px 16px;">
                                    <p class="text-sm font-medium text-dark mb-0"><?= $a['titulu'] ?></p>
                                    <p class="text-xs text-muted mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= strip_tags($a['konteudu']) ?></p>
                                    <span class="text-xs text-muted" style="font-size: 10px;"><?= date('d M Y', strtotime($a['data_publikasaun'])) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if (session()->get('role') == 1): // Admin only ?>
                        <a href="<?= base_url('administrador/avizu') ?>" class="dropdown-item-custom text-center justify-content-center py-2 border-top">
                            <span class="text-xs font-medium text-primary">View All Announcements</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Profile & Dropdown -->
            <div class="relative">
                <div class="profile-trigger transition-standard" id="profileDropdownTrigger">
                    <?php if (!empty($user['foto_perfil'])): ?>
                        <img src="<?= base_url('uploads/perfil/' . $user['foto_perfil']) ?>" class="avatar-custom" alt="<?= $user['fullname']; ?>" />
<?php else: ?>
                        <div class="avatar-custom avatar-placeholder">
                            <i data-feather="user" class="text-secondary" style="width: 18px; height: 18px;"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Dropdown Menu -->
                <div class="dropdown-custom dropdown-animate-in" id="profileDropdown">
                    <div class="px-4 py-3 border-bottom">
                        <p class="text-sm font-medium text-dark mb-0"><?= $user['fullname']; ?></p>
                        <p class="text-xs text-muted mb-0"><?= $user['username'] ?? 'User'; ?></p>
                    </div>
                    <a href="<?= base_url('logout') ?>" class="dropdown-item-custom danger">
                        <i data-feather="log-out"></i>
                        <span>Log out</span>
                    </a>
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

        profileTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            bellDropdown.classList.remove('show');
            profileDropdown.classList.toggle('show');
        });

        // Bell Dropdown
        const bellTrigger = document.getElementById('bellDropdownTrigger');
        const bellDropdown = document.getElementById('bellDropdown');

        bellTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.remove('show');
            bellDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileTrigger.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
            if (!bellDropdown.contains(e.target) && !bellTrigger.contains(e.target)) {
                bellDropdown.classList.remove('show');
            }
        });
        
        // Refresh feather icons for new elements
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>