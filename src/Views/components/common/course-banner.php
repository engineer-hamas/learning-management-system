<div class="course-banner course-banner--<?= e($userRole ?? 'student') ?> <?= $bannerExtraClass ?? '' ?>">
    <h1 class="course-banner__title <?= $bannerTitleClass ?? '' ?>" style="<?= $bannerTitleStyle ?? 'margin: 0 0 10px 0; font-size: 2.5rem;' ?>">
        <?= $bannerTitle ?? e($course['name']) ?>
    </h1>
    <p class="course-banner__subtitle <?= $bannerSubtitleClass ?? '' ?>" style="<?= $bannerSubtitleStyle ?? 'margin: 0; font-size: 1.1rem; opacity: 0.9;' ?>">
        <?= $bannerSubtitle ?? '<strong>Course Code:</strong> ' . e($course['code']) ?>
    </p>
    <?php if (!empty($course['description']) && empty($hideDescription)): ?>
        <p class="course-banner__description" style="margin: 15px 0 0 0; font-size: 1rem; opacity: 0.8; max-width: 800px;">
            <?= e($course['description']) ?>
        </p>
    <?php endif; ?>
</div>
