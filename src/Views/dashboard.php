<!-- Header -->
<?php ob_start(); ?>
<h2>Dashboard Overview</h2>
<?php $headerTitle = ob_get_clean(); ?>
<?php require __DIR__ . '/components/common/page-header.php'; ?>

<!-- Dashboard Content Container (Clean canvas for custom modules) -->
<div class="dashboard-container">
  <div class="welcome-card">
    <h1>Welcome, <?= e($userName) ?>!</h1>
    <p>You have successfully logged in as a <strong><?= ucfirst(e($userRole)) ?></strong>.</p>
    <p style="color: #64748b; margin-top: 10px; font-size: 0.9rem;">
      This is your clean dashboard canvas. You can now build student/teacher panels here step-by-step.
    </p>
  </div>

  <!-- Custom Dashboard Panels can be added here -->
</div>
