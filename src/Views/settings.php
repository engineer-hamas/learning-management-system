<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - LMS Portal</title>
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
                <a href="/dashboard" class="menu-item" data-tooltip="Dashboard">
                    <span class="icon">🏠</span> <span class="menu-text">Dashboard</span>
                </a>
                <a href="/enrollments" class="menu-item" data-tooltip="<?= e($userRole) === 'teacher' ? 'Enrollments' : 'Courses' ?>">
                    <span class="icon">🎓</span> <span class="menu-text"><?= e($userRole) === 'teacher' ? 'Enrollments' : 'Courses' ?></span>
                </a>
                <a href="/settings" class="menu-item active" data-tooltip="Settings">
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
                    <h2>Account Settings</h2>
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

            <!-- Settings Workspace Container -->
            <div class="dashboard-container">
                <!-- Session Alerts -->
                <?php if (!empty($success)): ?>
                    <div class="settings-alert alert-success">
                        <span class="alert-icon">✅</span>
                        <div class="alert-content"><?= e($success) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="settings-alert alert-danger">
                        <span class="alert-icon">⚠️</span>
                        <div class="alert-content"><?= e($error) ?></div>
                    </div>
                <?php endif; ?>

                <div class="settings-grid">
                    <!-- Left Sidebar Profile Column -->
                    <div class="settings-profile-card glass-card">
                        <form id="avatar-form" action="/settings/profile" method="POST" enctype="multipart/form-data" class="avatar-upload-form">
                            <!-- Circular Upload Container -->
                            <div class="avatar-upload-wrapper">
                                <div class="avatar-preview-circle" id="avatar-click-zone">
                                    <?php if (!empty($userAvatar) && file_exists(dirname(dirname(__DIR__)) . '/public' . $userAvatar)): ?>
                                        <img src="<?= e($userAvatar) ?>" alt="<?= e($userName) ?>" id="profile-preview-img">
                                    <?php else: ?>
                                        <div class="profile-preview-fallback <?= e($userRole) === 'teacher' ? 'fallback-teacher' : 'fallback-student' ?>" id="profile-preview-fallback">
                                            <?= strtoupper(substr(e($userName), 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="avatar-hover-overlay">
                                        <span class="camera-icon">📷</span>
                                        <span class="overlay-text">Upload Image</span>
                                    </div>
                                </div>
                                <input type="file" name="avatar" id="avatar-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                            </div>
                        </form>

                        <div class="profile-card-details">
                            <h3><?= e($userName) ?></h3>
                            <p class="profile-email"><?= e($userEmail) ?></p>
                            <span class="role-badge badge-<?= e($userRole) ?>"><?= ucfirst(e($userRole)) ?></span>
                        </div>

                        <!-- Settings Vertical Tab Menu Navigation -->
                        <div class="settings-tabs-menu">
                            <button class="tab-menu-item <?= $activeTab === 'general' ? 'active' : '' ?>" onclick="switchTab('general')">
                                <span class="tab-icon">👤</span> General Info
                            </button>
                            <button class="tab-menu-item <?= ($activeTab === 'teacher-profile' || $activeTab === 'student-profile') ? 'active' : '' ?>" onclick="switchTab('role')">
                                <span class="tab-icon">🎓</span> <?= e($userRole) === 'teacher' ? 'Professional Profile' : 'Academic Profile' ?>
                            </button>
                            <button class="tab-menu-item <?= $activeTab === 'security' ? 'active' : '' ?>" onclick="switchTab('security')">
                                <span class="tab-icon">🔒</span> Security & Password
                            </button>
                            <button class="tab-menu-item <?= $activeTab === 'notifications' ? 'active' : '' ?>" onclick="switchTab('notifications')">
                                <span class="tab-icon">🔔</span> Notification Preferences
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Settings Form Fields Panel -->
                    <div class="settings-forms-container glass-card">
                        
                        <!-- 1. GENERAL INFO FORM -->
                        <div class="settings-tab-pane <?= $activeTab === 'general' ? 'active' : '' ?>" id="tab-general">
                            <div class="tab-pane-header">
                                <h2>General Information</h2>
                                <p>Manage your basic account identity, contact, and bio details.</p>
                            </div>
                            
                            <form action="/settings/profile" method="POST" enctype="multipart/form-data" class="settings-form">
                                <input type="hidden" name="is_general_profile" value="1">
                                <!-- Hidden mirror input to capture the selected file during save -->
                                <input type="file" name="avatar" class="avatar-mirror-input" style="display: none;">

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="name-input">Full Name *</label>
                                        <input type="text" name="name" id="name-input" class="form-input" value="<?= e($userName) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email-input">Email Address *</label>
                                        <input type="email" name="email" id="email-input" class="form-input" value="<?= e($userEmail) ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phone-input">Phone Number</label>
                                    <input type="text" name="phone" id="phone-input" class="form-input" placeholder="+1 (555) 000-0000" value="<?= e($user['phone']) ?>">
                                </div>

                                <div class="form-group">
                                    <label for="bio-textarea">Personal Biography</label>
                                    <textarea name="bio" id="bio-textarea" class="form-input" rows="4" placeholder="Tell us a little bit about yourself..."><?= e($user['bio']) ?></textarea>
                                </div>

                                <div class="form-submit-row">
                                    <button type="submit" class="btn btn-save">Save General Info</button>
                                </div>
                            </form>
                        </div>

                        <!-- 2. ROLE SPECIFIC PROFILE FORM -->
                        <div class="settings-tab-pane <?= ($activeTab === 'teacher-profile' || $activeTab === 'student-profile') ? 'active' : '' ?>" id="tab-role">
                            
                            <?php if (e($userRole) === 'teacher'): ?>
                                <!-- TEACHER VIEW -->
                                <div class="tab-pane-header">
                                    <h2>Professional Profile Settings</h2>
                                    <p>Configure your academic title, department, specialization and classes taught.</p>
                                </div>

                                <form action="/settings/profile" method="POST" class="settings-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="title-select">Academic Title</label>
                                            <select name="title" id="title-select" class="form-input">
                                                <option value="" <?= empty($user['title']) ? 'selected' : '' ?>>Select title...</option>
                                                <option value="Lecturer" <?= $user['title'] === 'Lecturer' ? 'selected' : '' ?>>Lecturer</option>
                                                <option value="Senior Lecturer" <?= $user['title'] === 'Senior Lecturer' ? 'selected' : '' ?>>Senior Lecturer</option>
                                                <option value="Assistant Professor" <?= $user['title'] === 'Assistant Professor' ? 'selected' : '' ?>>Assistant Professor</option>
                                                <option value="Associate Professor" <?= $user['title'] === 'Associate Professor' ? 'selected' : '' ?>>Associate Professor</option>
                                                <option value="Professor" <?= $user['title'] === 'Professor' ? 'selected' : '' ?>>Professor</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="dept-select">Primary Academic Department</label>
                                            <select name="department_id" id="dept-select" class="form-input">
                                                <option value="" <?= empty($user['department_id']) ? 'selected' : '' ?>>Select department...</option>
                                                <?php foreach ($departments as $dep): ?>
                                                    <option value="<?= e($dep['id']) ?>" <?= (int)$user['department_id'] === (int)$dep['id'] ? 'selected' : '' ?>>
                                                        <?= e($dep['name']) ?> (<?= e($dep['code']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="hours-input">Office Hours</label>
                                            <input type="text" name="office_hours" id="hours-input" class="form-input" placeholder="e.g. Mon/Wed 2:00 PM - 4:00 PM" value="<?= e($user['office_hours']) ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="spec-input">Academic Specialization</label>
                                            <input type="text" name="specialization" id="spec-input" class="form-input" placeholder="e.g. Machine Learning, Literature" value="<?= e($user['specialization']) ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Subjects Instructed (Many-to-Many)</label>
                                        <p class="field-help-text">Search courses by name or code and add them to your profile. Changes are synced dynamically.</p>
                                        
                                        <!-- Search Autocomplete Wrapper -->
                                        <div class="subject-search-wrapper" id="teacher-search-wrapper">
                                            <div class="subject-search-input-container">
                                                <span class="search-icon">🔍</span>
                                                <input type="text" class="subject-search-input" placeholder="Search courses by name or code... e.g. CS-101" autocomplete="off">
                                            </div>
                                            <!-- Floating Results Dropdown -->
                                            <div class="search-results-dropdown"></div>
                                        </div>

                                        <!-- Selected Badges Container -->
                                        <div class="selected-badges-container" id="teacher-badges-container">
                                            <?php if (empty($userSubjectIds)): ?>
                                                <div class="badges-placeholder">No courses added yet. Use the search bar above to lookup and add courses.</div>
                                            <?php else: ?>
                                                <?php foreach ($subjects as $sub): ?>
                                                    <?php if (in_array((int)$sub['id'], $userSubjectIds)): ?>
                                                        <div class="subject-badge" data-id="<?= e($sub['id']) ?>">
                                                            <span class="badge-code"><?= e($sub['code']) ?></span>
                                                            <span class="badge-name"><?= e($sub['name']) ?></span>
                                                            <span class="badge-remove">&times;</span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Hidden container where active checkboxes will be appended so they submit normally -->
                                        <div class="hidden-inputs-container" id="teacher-hidden-container" style="display: none;">
                                            <?php foreach ($userSubjectIds as $subId): ?>
                                                <input type="checkbox" name="subjects[]" value="<?= e($subId) ?>" checked>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="form-submit-row">
                                        <button type="submit" class="btn btn-save">Save Professional Profile</button>
                                    </div>
                                </form>

                            <?php else: ?>
                                <!-- STUDENT VIEW -->
                                <div class="tab-pane-header">
                                    <h2>Academic Profile Settings</h2>
                                    <p>Configure your student parameters, dynamic major/department, and class enrollment.</p>
                                </div>

                                <form action="/settings/profile" method="POST" class="settings-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="student-id-input">Student Registration ID</label>
                                            <input type="text" name="student_id" id="student-id-input" class="form-input" placeholder="e.g. STU-2026-0042" value="<?= e($user['student_id']) ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="major-select">Academic Major (Department)</label>
                                            <select name="department_id" id="major-select" class="form-input">
                                                <option value="" <?= empty($user['department_id']) ? 'selected' : '' ?>>Select Major...</option>
                                                <?php foreach ($departments as $dep): ?>
                                                    <option value="<?= e($dep['id']) ?>" <?= (int)$user['department_id'] === (int)$dep['id'] ? 'selected' : '' ?>>
                                                        <?= e($dep['name']) ?> (<?= e($dep['code']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="grad-select">Expected Graduation</label>
                                            <select name="expected_graduation" id="grad-select" class="form-input">
                                                <option value="" <?= empty($user['expected_graduation']) ? 'selected' : '' ?>>Select Sem...</option>
                                                <option value="Fall 2026" <?= $user['expected_graduation'] === 'Fall 2026' ? 'selected' : '' ?>>Fall 2026</option>
                                                <option value="Spring 2027" <?= $user['expected_graduation'] === 'Spring 2027' ? 'selected' : '' ?>>Spring 2027</option>
                                                <option value="Fall 2027" <?= $user['expected_graduation'] === 'Fall 2027' ? 'selected' : '' ?>>Fall 2027</option>
                                                <option value="Spring 2028" <?= $user['expected_graduation'] === 'Spring 2028' ? 'selected' : '' ?>>Spring 2028</option>
                                                <option value="Fall 2028" <?= $user['expected_graduation'] === 'Fall 2028' ? 'selected' : '' ?>>Fall 2028</option>
                                                <option value="Spring 2029" <?= $user['expected_graduation'] === 'Spring 2029' ? 'selected' : '' ?>>Spring 2029</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="style-select">Learning / Study Style</label>
                                            <select name="study_style" id="style-select" class="form-input">
                                                <option value="" <?= empty($user['study_style']) ? 'selected' : '' ?>>Select learning style...</option>
                                                <option value="Visual Learner" <?= $user['study_style'] === 'Visual Learner' ? 'selected' : '' ?>>Visual Learner</option>
                                                <option value="Auditory Learner" <?= $user['study_style'] === 'Auditory Learner' ? 'selected' : '' ?>>Auditory Learner</option>
                                                <option value="Kinesthetic/Hands-on" <?= $user['study_style'] === 'Kinesthetic/Hands-on' ? 'selected' : '' ?>>Kinesthetic/Hands-on</option>
                                                <option value="Reading & Writing" <?= $user['study_style'] === 'Reading & Writing' ? 'selected' : '' ?>>Reading & Writing</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Enrolled Subjects (Many-to-Many)</label>
                                        <p class="field-help-text">Search courses by name or code and add them to your profile. Changes are synced dynamically.</p>
                                        
                                        <!-- Search Autocomplete Wrapper -->
                                        <div class="subject-search-wrapper" id="student-search-wrapper">
                                            <div class="subject-search-input-container">
                                                <span class="search-icon">🔍</span>
                                                <input type="text" class="subject-search-input" placeholder="Search courses by name or code... e.g. CS-101" autocomplete="off">
                                            </div>
                                            <!-- Floating Results Dropdown -->
                                            <div class="search-results-dropdown"></div>
                                        </div>

                                        <!-- Selected Badges Container -->
                                        <div class="selected-badges-container" id="student-badges-container">
                                            <?php if (empty($userSubjectIds)): ?>
                                                <div class="badges-placeholder">No courses enrolled yet. Use the search bar above to lookup and add courses.</div>
                                            <?php else: ?>
                                                <?php foreach ($subjects as $sub): ?>
                                                    <?php if (in_array((int)$sub['id'], $userSubjectIds)): ?>
                                                        <div class="subject-badge" data-id="<?= e($sub['id']) ?>">
                                                            <span class="badge-code"><?= e($sub['code']) ?></span>
                                                            <span class="badge-name"><?= e($sub['name']) ?></span>
                                                            <span class="badge-remove">&times;</span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Hidden container where active checkboxes will be appended so they submit normally -->
                                        <div class="hidden-inputs-container" id="student-hidden-container" style="display: none;">
                                            <?php foreach ($userSubjectIds as $subId): ?>
                                                <input type="checkbox" name="subjects[]" value="<?= e($subId) ?>" checked>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="form-submit-row">
                                        <button type="submit" class="btn btn-save">Save Academic Profile</button>
                                    </div>
                                </form>
                            <?php endif; ?>

                        </div>

                        <!-- 3. SECURITY & PASSWORD FORM -->
                        <div class="settings-tab-pane <?= $activeTab === 'security' ? 'active' : '' ?>" id="tab-security">
                            <div class="tab-pane-header">
                                <h2>Security Settings</h2>
                                <p>Ensure account security by revising your password credentials regularly.</p>
                            </div>

                            <form action="/settings/password" method="POST" class="settings-form">
                                <div class="form-group">
                                    <label for="curr-pass">Current Password *</label>
                                    <input type="password" name="current_password" id="curr-pass" class="form-input" placeholder="••••••••" required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="new-pass">New Password *</label>
                                        <input type="password" name="new_password" id="new-pass" class="form-input" placeholder="Min. 8 characters" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm-pass">Confirm New Password *</label>
                                        <input type="password" name="confirm_password" id="confirm-pass" class="form-input" placeholder="Min. 8 characters" required>
                                    </div>
                                </div>

                                <div class="form-submit-row">
                                    <button type="submit" class="btn btn-save">Change Password</button>
                                </div>
                            </form>
                        </div>

                        <!-- 4. NOTIFICATION PREFERENCES FORM -->
                        <div class="settings-tab-pane <?= $activeTab === 'notifications' ? 'active' : '' ?>" id="tab-notifications">
                            <div class="tab-pane-header">
                                <h2>Notification Preferences</h2>
                                <p>Manage how, where, and when the LMS Portal communicates alerts and bulletins to you.</p>
                            </div>

                            <form action="/settings/notifications" method="POST" class="settings-form">
                                <div class="notifications-toggles-list">
                                    
                                    <div class="toggle-card">
                                        <div class="toggle-description">
                                            <h4>Email Notifications</h4>
                                            <p>Receive academic transcripts, report card submissions, and registration verification emails.</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" name="notify_email" value="1" <?= (int)$user['notify_email'] === 1 ? 'checked' : '' ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>

                                    <div class="toggle-card">
                                        <div class="toggle-description">
                                            <h4>Classroom Alerts & Broadcasts</h4>
                                            <p>Receive immediate announcements, homework changes, or test alerts published by your instructors.</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" name="notify_class_alerts" value="1" <?= (int)$user['notify_class_alerts'] === 1 ? 'checked' : '' ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>

                                    <div class="toggle-card">
                                        <div class="toggle-description">
                                            <h4>Weekly Progress Digest</h4>
                                            <p>Weekly summaries of grade point averages, class rankings, and academic schedules.</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" name="notify_marketing" value="1" <?= (int)$user['notify_marketing'] === 1 ? 'checked' : '' ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>

                                </div>

                                <div class="form-submit-row" style="margin-top: 32px;">
                                    <button type="submit" class="btn btn-save">Save Notifications Preferences</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Interactive Front-end Scripting -->
    <script>
        // 1. Fluid Vanilla JS Switch Tab Handler
        function switchTab(tabName) {
            // Remove active classes from all tab items
            document.querySelectorAll('.tab-menu-item').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelectorAll('.settings-tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });

            // Add active class to selected tab button
            const buttons = document.querySelectorAll('.tab-menu-item');
            if (tabName === 'general') {
                buttons[0].classList.add('active');
                document.getElementById('tab-general').classList.add('active');
            } else if (tabName === 'role') {
                buttons[1].classList.add('active');
                document.getElementById('tab-role').classList.add('active');
            } else if (tabName === 'security') {
                buttons[2].classList.add('active');
                document.getElementById('tab-security').classList.add('active');
            } else if (tabName === 'notifications') {
                buttons[3].classList.add('active');
                document.getElementById('tab-notifications').classList.add('active');
            }
        }

        // 2. Circular Upload Preview & Triggers
        const clickZone = document.getElementById('avatar-click-zone');
        const fileInput = document.getElementById('avatar-input');
        const previewImg = document.getElementById('profile-preview-img');
        const previewFallback = document.getElementById('profile-preview-fallback');

        clickZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Max size client side indicator (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Profile image size must be smaller than 2MB.');
                    this.value = '';
                    return;
                }

                // Render instant visual preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) {
                        previewImg.src = e.target.result;
                    } else {
                        // Create image element if previously displayingfallback
                        const img = document.createElement('img');
                        img.id = 'profile-preview-img';
                        img.src = e.target.result;
                        img.alt = 'Uploaded Preview';
                        previewFallback.style.display = 'none';
                        clickZone.insertBefore(img, clickZone.firstChild);
                    }

                    // Mirrors file select on the general form so it is uploaded when clicking the main Save btn
                    const mirrors = document.querySelectorAll('.avatar-mirror-input');
                    mirrors.forEach(mirror => {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        mirror.files = dataTransfer.files;
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        // 3. Dynamic Course Autocomplete Lookup Setup
        const allSubjects = <?= json_encode($subjects) ?>;

        function initCourseLookup(wrapperId, badgesContainerId, hiddenContainerId, placeholderText = "No courses added yet.") {
            const wrapper = document.getElementById(wrapperId);
            if (!wrapper) return;

            const input = wrapper.querySelector('.subject-search-input');
            const dropdown = wrapper.querySelector('.search-results-dropdown');
            const badgesContainer = document.getElementById(badgesContainerId);
            const hiddenContainer = document.getElementById(hiddenContainerId);

            // Keep track of selected subject IDs
            let selectedIds = new Set();
            hiddenContainer.querySelectorAll('input[type="checkbox"]').forEach(input => {
                selectedIds.add(parseInt(input.value));
            });

            // Filter and render autocomplete results
            function filterResults(query) {
                dropdown.innerHTML = '';
                if (!query.trim()) {
                    dropdown.style.display = 'none';
                    return;
                }

                const filtered = allSubjects.filter(sub => {
                    // Exclude already selected
                    if (selectedIds.has(parseInt(sub.id))) return false;
                    
                    const codeMatch = sub.code.toLowerCase().includes(query.toLowerCase());
                    const nameMatch = sub.name.toLowerCase().includes(query.toLowerCase());
                    return codeMatch || nameMatch;
                });

                if (filtered.length === 0) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-results-item';
                    noResults.textContent = 'No matching subjects found';
                    dropdown.appendChild(noResults);
                } else {
                    filtered.forEach(sub => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.dataset.id = sub.id;
                        item.innerHTML = `
                            <div class="result-details">
                                <span class="result-code">${escapeHtml(sub.code)}</span>
                                <span class="result-name">${escapeHtml(sub.name)}</span>
                            </div>
                            <div class="result-meta">${escapeHtml(sub.credits)} Credits</div>
                        `;
                        item.addEventListener('click', () => {
                            addSubject(sub);
                            input.value = '';
                            dropdown.style.display = 'none';
                        });
                        dropdown.appendChild(item);
                    });
                }
                dropdown.style.display = 'block';
            }

            // Add a subject as a badge and hidden input
            function addSubject(sub) {
                const id = parseInt(sub.id);
                if (selectedIds.has(id)) return;

                // Remove placeholder if present
                const placeholder = badgesContainer.querySelector('.badges-placeholder');
                if (placeholder) {
                    placeholder.remove();
                }

                selectedIds.add(id);

                // Create badge
                const badge = document.createElement('div');
                badge.className = 'subject-badge';
                badge.dataset.id = id;
                badge.innerHTML = `
                    <span class="badge-code">${escapeHtml(sub.code)}</span>
                    <span class="badge-name">${escapeHtml(sub.name)}</span>
                    <span class="badge-remove">&times;</span>
                `;

                // Hook remove button
                badge.querySelector('.badge-remove').addEventListener('click', () => {
                    removeSubject(id, badge);
                });

                badgesContainer.appendChild(badge);

                // Create hidden input
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'checkbox';
                hiddenInput.name = 'subjects[]';
                hiddenInput.value = id;
                hiddenInput.checked = true;
                hiddenContainer.appendChild(hiddenInput);
            }

            // Remove a subject
            function removeSubject(id, badgeElement) {
                selectedIds.delete(id);
                badgeElement.remove();

                // Remove hidden input
                const hiddenInput = hiddenContainer.querySelector(`input[value="${id}"]`);
                if (hiddenInput) {
                    hiddenInput.remove();
                }

                // Restore placeholder if empty
                if (selectedIds.size === 0) {
                    badgesContainer.innerHTML = `<div class="badges-placeholder">${placeholderText}</div>`;
                }
            }

            // Input change event
            input.addEventListener('input', (e) => {
                filterResults(e.target.value);
            });

            // Show results on focus if there's query
            input.addEventListener('focus', () => {
                if (input.value.trim()) {
                    filterResults(input.value);
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            // Hook up pre-selected badges click handlers
            badgesContainer.querySelectorAll('.subject-badge').forEach(badge => {
                const id = parseInt(badge.dataset.id);
                badge.querySelector('.badge-remove').addEventListener('click', () => {
                    removeSubject(id, badge);
                });
            });
        }

        // Simple HTML escaping helper for JS template strings
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        document.addEventListener('DOMContentLoaded', () => {
            initCourseLookup('teacher-search-wrapper', 'teacher-badges-container', 'teacher-hidden-container', 'No courses added yet. Use the search bar above to lookup and add courses.');
            initCourseLookup('student-search-wrapper', 'student-badges-container', 'student-hidden-container', 'No courses enrolled yet. Use the search bar above to lookup and add courses.');

            // Interactive Sidebar Toggle
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
