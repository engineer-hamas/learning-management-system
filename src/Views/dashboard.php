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
    <div class="dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h3>📚 LMS Portal</h3>
            </div>
            <nav class="sidebar-menu">
                <a href="/dashboard" class="menu-item active">
                    <span class="icon">🏠</span> Dashboard
                </a>
                <!-- Future features will be added here step-by-step -->
            </nav>
            <div class="sidebar-footer">
                <a href="/logout" class="menu-item logout">
                    <span class="icon">🚪</span> Logout
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
</body>
</html>
