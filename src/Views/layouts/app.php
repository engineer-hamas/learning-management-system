<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'LMS Portal' ?></title>
  <!-- Premium Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
</head>

<body class="dashboard-body">
  <script>
    // Prevent sidebar layout shift on page load
    (function() {
      const collapsed = localStorage.getItem('sidebar-collapsed') === 'true';
      if (collapsed) {
        document.body.classList.add('sidebar-pre-collapsed');
      }
    })();
  </script>
  <div class="dashboard-layout">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <button type="button" id="sidebar-toggle" class="sidebar-toggle-btn" title="Toggle Sidebar">
        <span class="toggle-icon">◀</span>
      </button>
      <div class="sidebar-logo">
        <div class="logo-container">
          <h3><span class="logo-icon">📚</span> <span class="logo-text">LMS Portal</span></h3>
        </div>
      </div>
      <nav class="sidebar-menu">
        <a href="/dashboard" class="menu-item <?= (strpos($_SERVER['REQUEST_URI'] ?? '', '/dashboard') !== false) ? 'active' : '' ?>" data-tooltip="Dashboard">
          <span class="icon">🏠</span> <span class="menu-text">Dashboard</span>
        </a>
        <?php if (($userRole ?? '') !== 'admin'): ?>
        <a href="/enrollments" class="menu-item <?= (strpos($_SERVER['REQUEST_URI'] ?? '', '/enrollments') !== false || strpos($_SERVER['REQUEST_URI'] ?? '', '/course') !== false) ? 'active' : '' ?>" data-tooltip="<?= e($userRole ?? 'student') === 'teacher' ? 'Enrollments' : 'Courses' ?>">
          <span class="icon">🎓</span> <span class="menu-text"><?= e($userRole ?? 'student') === 'teacher' ? 'Enrollments' : 'Courses' ?></span>
        </a>
        <?php endif; ?>
        <?php if (($userRole ?? '') === 'admin'): ?>
        <a href="/admin" class="menu-item <?= (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin') !== false) ? 'active' : '' ?>" data-tooltip="Admin Panel">
          <span class="icon">🛡️</span> <span class="menu-text">Admin Panel</span>
        </a>
        <?php endif; ?>
        <?php if (($userRole ?? '') !== 'admin'): ?>
        <a href="/settings" class="menu-item <?= (strpos($_SERVER['REQUEST_URI'] ?? '', '/settings') !== false) ? 'active' : '' ?>" data-tooltip="Settings">
          <span class="icon">⚙️</span> <span class="menu-text">Settings</span>
        </a>
        <?php endif; ?>
      </nav>
      <div class="sidebar-footer">
        <a href="/logout" class="menu-item logout" data-tooltip="Logout">
          <span class="icon">🚪</span> <span class="menu-text">Logout</span>
        </a>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <?= $content ?>
    </main>
  </div>

  <!-- Interactive Sidebar Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const sidebar = document.querySelector('.sidebar');
      const toggleBtn = document.getElementById('sidebar-toggle');

      // Apply dynamic class if stored collapsed, and clear temporary loader state
      const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
      if (isCollapsed) {
        sidebar.classList.add('collapsed');
      }
      document.body.classList.remove('sidebar-pre-collapsed');

      if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
          sidebar.classList.toggle('collapsed');
          const nowCollapsed = sidebar.classList.contains('collapsed');
          localStorage.setItem('sidebar-collapsed', nowCollapsed);
        });
      }
    });
  </script>
  <script src="/js/components.js"></script>
</body>

</html>
