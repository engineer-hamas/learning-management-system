<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <?= e($success) ?>
    </div>
<?php endif; ?>
