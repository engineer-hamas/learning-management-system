<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LMS Portal</title>
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
                <a href="/dashboard" class="menu-item active" data-tooltip="Dashboard">
                    <span class="icon">🏠</span> <span class="menu-text">Dashboard</span>
                </a>
                <a href="/settings" class="menu-item" data-tooltip="Settings">
                    <span class="icon">⚙️</span> <span class="menu-text">Settings</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="/logout" class="menu-item logout" data-tooltip="Logout">
                    <span class="icon">🚪</span> <span class="menu-text">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-title">
                    <h2>Dashboard Overview</h2>
                </div>
                <div class="header-profile">
                    <span class="role-badge badge-<?= e($userRole) ?>">
                        <?= ucfirst(e($userRole)) ?>
                    </span>
                    <div class="header-avatar-container">
                        <?php if (!empty($userAvatar) && file_exists(dirname(dirname(__DIR__)) . '/public' . $userAvatar)): ?>
                            <img src="<?= e($userAvatar) ?>" alt="<?= e($userName) ?>" class="header-avatar">
                        <?php else: ?>
                            <div class="header-avatar-fallback <?= e($userRole) === 'teacher' ? 'fallback-teacher' : 'fallback-student' ?>">
                                <?= strtoupper(substr(e($userName), 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <span class="profile-name"><?= e($userName) ?></span>
                </div>
            </header>

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
</body>
</html>
