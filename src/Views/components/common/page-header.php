<header class="dashboard-header">
    <div class="header-title">
        <?= $headerTitle ?? '<h2>LMS Portal</h2>' ?>
    </div>
    <div class="header-profile">
        <span class="role-badge badge-<?= e($userRole ?? 'student') ?>">
            <?= ucfirst(e($userRole ?? 'student')) ?>
        </span>
        <div class="header-avatar-container">
            <?php if (!empty($userAvatar) && file_exists(dirname(dirname(__DIR__)) . '/public' . $userAvatar)): ?>
                <img src="<?= e($userAvatar) ?>" alt="<?= e($userName ?? 'User') ?>" class="header-avatar">
            <?php else: ?>
                <div class="header-avatar-fallback <?= e($userRole ?? 'student') === 'teacher' ? 'fallback-teacher' : 'fallback-student' ?>">
                    <?= strtoupper(substr(e($userName ?? 'U'), 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>
        <span class="profile-name"><?= e($userName ?? 'User') ?></span>
    </div>
</header>
