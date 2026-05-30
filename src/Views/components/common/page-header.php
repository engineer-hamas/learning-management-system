<header class="page-header">
    <div class="page-header__title">
        <?= $headerTitle ?? '<h2 class="text-title">LMS Portal</h2>' ?>
    </div>
    <div class="page-header__actions">
        <span style="font-weight: 500; color: var(--color-text-muted);">
            <?= ucfirst(e($userRole ?? 'student')) ?>
        </span>
        <div class="header-avatar-container">
            <?php if (!empty($userAvatar) && file_exists(dirname(dirname(__DIR__)) . '/public' . $userAvatar)): ?>
                <img src="<?= e($userAvatar) ?>" alt="<?= e($userName ?? 'User') ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    <?= strtoupper(substr(e($userName ?? 'U'), 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>
        <span style="font-weight: 600; color: var(--color-text-main); margin-right: 15px;"><?= e($userName ?? 'User') ?></span>
        
        <!-- Only show logout button if we are not already on the login page or using a custom header -->
        <?php if(!isset($hideLogout)): ?>
        <form action="/logout" method="POST" style="margin:0;">
            <button type="submit" class="btn btn--destructive">Logout</button>
        </form>
        <?php endif; ?>
    </div>
</header>
