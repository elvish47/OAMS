<?php
// admindashboard.php
// Admin dashboard with PHP session handling and dynamic data.
// Replace static arrays with database queries for production.

// Start session to check admin login
session_start();

// --- Simulate admin login ---
// In a real app, verify admin credentials from database.
if (!isset($_SESSION['admin_id'])) {
    // Set demo admin session
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_name'] = 'Admin User';
    $_SESSION['admin_email'] = 'admin@assignflow.com';
    $_SESSION['admin_role'] = 'super_admin';
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
}

// Get admin info elvish shrestha
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
$admin_email = $_SESSION['admin_email'] ?? 'admin@assignflow.com';
$login_time = $_SESSION['login_time'] ?? date('Y-m-d H:i:s');

// --- Simulated database data ---
// Statistics
$stats = [
    'total_users' => 1247,
    'total_assignments' => 342,
    'total_submissions' => 891,
    'pending_reviews' => 23,
    'active_courses' => 15,
    'total_instructors' => 28
];

// Recent activity (for display)
$recent_activities = [
    [
        'user' => 'Sarah Johnson',
        'action' => 'submitted assignment "Data Structures · Hash Map"',
        'time' => '2 hours ago',
        'type' => 'submission'
    ],
    [
        'user' => 'Prof. Michael Chen',
        'action' => 'created new assignment "Machine Learning · Neural Networks"',
        'time' => '4 hours ago',
        'type' => 'creation'
    ],
    [
        'user' => 'Emily Rodriguez',
        'action' => 'completed "Web Dev · Portfolio Project"',
        'time' => '6 hours ago',
        'type' => 'completion'
    ],
    [
        'user' => 'Dr. James Wilson',
        'action' => 'updated course "Advanced Database Systems"',
        'time' => '1 day ago',
        'type' => 'update'
    ],
    [
        'user' => 'Lisa Park',
        'action' => 'submitted "Algorithms · Sorting Visualizer"',
        'time' => '1 day ago',
        'type' => 'submission'
    ]
];

// System status data
$system_status = [
    'server_load' => '42%',
    'uptime' => '99.98%',
    'database' => 'Connected',
    'cache' => 'Active',
    'storage_used' => '156.4 GB / 500 GB',
    'active_sessions' => 47
];

// Course statistics (for chart simulation)
$course_stats = [
    ['name' => 'CS-201', 'students' => 42],
    ['name' => 'CS-301', 'students' => 38],
    ['name' => 'CS-401', 'students' => 35],
    ['name' => 'CS-250', 'students' => 29],
    ['name' => 'CS-470', 'students' => 31]
];

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php'); // Redirect to admin login
    exit;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'get_recent_activity':
            echo json_encode(['success' => true, 'data' => $recent_activities]);
            break;
        case 'refresh_stats':
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
        case 'system_check':
            echo json_encode(['success' => true, 'status' => $system_status]);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard · AssignFlow</title>
    <!-- Font Awesome 6 Free -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: #f0f4fb;
            min-height: 100vh;
            padding: 1.5rem;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 40px;
            padding: 2rem 2.2rem 2.5rem;
            box-shadow: 0 20px 60px rgba(18, 40, 70, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* Header */
        .admin-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .brand-area {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .brand-area i {
            font-size: 2.2rem;
            color: #1d3b5e;
            background: rgba(29, 59, 94, 0.08);
            padding: 0.5rem;
            border-radius: 16px;
        }

        .brand-area h1 {
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: -0.02em;
            color: #0b1e33;
        }

        .brand-area span {
            color: #1d3b5e;
            font-weight: 300;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            background: #f0f5fe;
            padding: 0.4rem 1rem 0.4rem 1.2rem;
            border-radius: 60px;
            border: 1px solid #dae4f2;
        }

        .admin-profile .avatar {
            background: #1d3b5e;
            color: white;
            width: 2.6rem;
            height: 2.6rem;
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .admin-profile .admin-name {
            font-weight: 500;
            color: #0b1e33;
        }

        .admin-profile .admin-email {
            font-size: 0.8rem;
            color: #4d6d8f;
        }

        .admin-profile .logout-btn {
            background: transparent;
            border: none;
            color: #4b6f94;
            cursor: pointer;
            font-size: 1rem;
            padding: 0.2rem 0.4rem;
            border-radius: 30px;
            transition: 0.15s;
            text-decoration: none;
        }

        .admin-profile .logout-btn:hover {
            color: #b13e3e;
            background: rgba(177, 62, 62, 0.06);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: #f8faff;
            padding: 1.2rem 1rem 1rem 1.4rem;
            border-radius: 28px;
            border: 1px solid #eaf0f8;
            transition: 0.15s;
        }

        .stat-card:hover {
            border-color: #c2d6ed;
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .stat-card .stat-icon {
            font-size: 1.6rem;
            color: #1d3b5e;
            opacity: 0.7;
            margin-bottom: 0.3rem;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 600;
            color: #0b1e33;
            letter-spacing: -0.02em;
        }

        .stat-card .stat-label {
            font-size: 0.85rem;
            color: #3d5a7a;
            font-weight: 450;
        }

        /* Main Grid */
        .admin-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* Left Column */
        .left-column {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .activity-section {
            background: #fafcff;
            border-radius: 32px;
            padding: 1.4rem 1.2rem 1.2rem;
            border: 1px solid #eaf0f8;
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
        }

        .section-title h3 {
            font-weight: 600;
            font-size: 1.2rem;
            color: #0b1e33;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title h3 i {
            color: #1d3b5e;
        }

        .refresh-btn {
            background: transparent;
            border: none;
            color: #4d6d8f;
            cursor: pointer;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            transition: 0.15s;
        }

        .refresh-btn:hover {
            background: #eaf1fc;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            padding: 0.8rem 0.4rem;
            border-bottom: 1px solid #edf3fb;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 2.4rem;
            height: 2.4rem;
            border-radius: 30px;
            background: #eaf1fc;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon i {
            color: #1d3b5e;
            font-size: 1rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-content .user {
            font-weight: 500;
            color: #0b1e33;
        }

        .activity-content .action {
            color: #1f334b;
        }

        .activity-content .time {
            font-size: 0.8rem;
            color: #6d8aaa;
            margin-top: 0.2rem;
        }

        /* Course Stats */
        .course-section {
            background: #fafcff;
            border-radius: 32px;
            padding: 1.4rem 1.2rem 1.2rem;
            border: 1px solid #eaf0f8;
        }

        .course-bar {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #edf3fb;
        }

        .course-bar:last-child {
            border-bottom: none;
        }

        .course-name {
            min-width: 70px;
            font-weight: 500;
            color: #0b1e33;
            font-size: 0.9rem;
        }

        .course-bar-track {
            flex: 1;
            height: 8px;
            background: #eaf0f8;
            border-radius: 20px;
            overflow: hidden;
        }

        .course-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #1d3b5e, #3a6a9e);
            border-radius: 20px;
            transition: width 0.5s;
        }

        .course-count {
            font-size: 0.85rem;
            color: #4d6d8f;
            min-width: 40px;
            text-align: right;
        }

        /* Right Column */
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .system-status {
            background: #fafcff;
            border-radius: 32px;
            padding: 1.4rem 1.2rem 1.2rem;
            border: 1px solid #eaf0f8;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid #edf3fb;
            font-size: 0.95rem;
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-label {
            color: #3d5a7a;
        }

        .status-value {
            font-weight: 500;
            color: #0b1e33;
        }

        .status-value .online {
            color: #1a8a4a;
        }

        .status-value .warning {
            color: #c97d1a;
        }

        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .quick-btn {
            background: white;
            border: 1px solid #d6e2f2;
            border-radius: 60px;
            padding: 0.9rem 1.3rem;
            font-weight: 500;
            color: #1d3b5e;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 0.95rem;
            cursor: pointer;
            transition: 0.15s;
            width: 100%;
        }

        .quick-btn i {
            color: #2a4b7c;
            width: 1.5rem;
        }

        .quick-btn:hover {
            background: #eaf1fc;
            border-color: #a6bedc;
            transform: scale(0.98);
        }

        .quick-btn.primary {
            background: #1d3b5e;
            border-color: #1d3b5e;
            color: white;
        }

        .quick-btn.primary i {
            color: white;
        }

        .quick-btn.primary:hover {
            background: #12304e;
        }

        /* Footer */
        .admin-footer {
            margin-top: 2rem;
            border-top: 1px solid #e3ebf5;
            padding-top: 1.3rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            align-items: center;
            color: #4a688b;
            font-size: 0.9rem;
        }

        .admin-footer i {
            margin-right: 0.2rem;
            opacity: 0.6;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 1.5rem;
            }
            .admin-header {
                flex-direction: column;
                align-items: start;
                gap: 1rem;
            }
            .admin-profile {
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            .admin-profile .admin-email {
                display: none;
            }
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="admin-container">

    <!-- Header -->
    <header class="admin-header">
        <div class="brand-area">
            <i class="fas fa-crown"></i>
            <h1>Assign<span>Flow</span> <span style="font-size:0.9rem; font-weight:400; color:#4d6d8f;">Admin</span></h1>
        </div>
        <div class="admin-profile">
            <span class="avatar"><?php echo strtoupper(substr($admin_name, 0, 2)); ?></span>
            <div>
                <div class="admin-name"><?php echo htmlspecialchars($admin_name); ?></div>
                <div class="admin-email"><?php echo htmlspecialchars($admin_email); ?></div>
            </div>
            <a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            <div class="stat-number"><?php echo $stats['total_assignments']; ?></div>
            <div class="stat-label">Assignments</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-paper-plane"></i></div>
            <div class="stat-number"><?php echo $stats['total_submissions']; ?></div>
            <div class="stat-label">Submissions</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-number"><?php echo $stats['pending_reviews']; ?></div>
            <div class="stat-label">Pending Reviews</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-number"><?php echo $stats['total_instructors']; ?></div>
            <div class="stat-label">Instructors</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-book-open"></i></div>
            <div class="stat-number"><?php echo $stats['active_courses']; ?></div>
            <div class="stat-label">Active Courses</div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="admin-grid">

        <!-- Left Column -->
        <div class="left-column">

            <!-- Recent Activity -->
            <div class="activity-section">
                <div class="section-title">
                    <h3><i class="fas fa-bolt"></i> Recent Activity</h3>
                    <button class="refresh-btn" onclick="refreshActivity()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div id="activityList">
                    <?php foreach ($recent_activities as $activity): 
                        $icon = 'fa-pencil-alt';
                        if ($activity['type'] === 'submission') $icon = 'fa-upload';
                        elseif ($activity['type'] === 'creation') $icon = 'fa-plus-circle';
                        elseif ($activity['type'] === 'completion') $icon = 'fa-check-circle';
                        elseif ($activity['type'] === 'update') $icon = 'fa-edit';
                    ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                        <div class="activity-content">
                            <div>
                                <span class="user"><?php echo htmlspecialchars($activity['user']); ?></span>
                                <span class="action"><?php echo htmlspecialchars($activity['action']); ?></span>
                            </div>
                            <div class="time"><i class="far fa-clock"></i> <?php echo $activity['time']; ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Course Enrollment Stats -->
            <div class="course-section">
                <div class="section-title">
                    <h3><i class="fas fa-chart-bar"></i> Course Enrollment</h3>
                    <span style="font-size:0.8rem; color:#4d6d8f;">Total: <?php echo array_sum(array_column($course_stats, 'students')); ?> students</span>
                </div>
                <?php 
                $max_students = max(array_column($course_stats, 'students'));
                foreach ($course_stats as $course): 
                    $percentage = ($max_students > 0) ? ($course['students'] / $max_students) * 100 : 0;
                ?>
                <div class="course-bar">
                    <span class="course-name"><?php echo $course['name']; ?></span>
                    <div class="course-bar-track">
                        <div class="course-bar-fill" style="width: <?php echo $percentage; ?>%;"></div>
                    </div>
                    <span class="course-count"><?php echo $course['students']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">

            <!-- System Status -->
            <div class="system-status">
                <div class="section-title">
                    <h3><i class="fas fa-server"></i> System Status</h3>
                    <span style="font-size:0.75rem; color:#1a8a4a;"><i class="fas fa-circle"></i> All systems nominal</span>
                </div>
                <div class="status-item">
                    <span class="status-label"><i class="fas fa-microchip"></i> Server Load</span>
                    <span class="status-value"><?php echo $system_status['server_load']; ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label"><i class="fas fa-clock"></i> Uptime</span>
                    <span class="status-value online"><?php echo $system_status['uptime']; ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label"><i class="fas fa-database"></i> Database</span>
                    <span class="status-value online"><i class="fas fa-check-circle"></i> <?php echo $system_status['database']; ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label"><i class="fas fa-memory"></i> Cache</span>
                    <span class="status-value online"><?php echo $system_status['cache']; ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label"><i class="fas fa-hdd"></i> Storage</span>
                    <span class="status-value"><?php echo $system_status['storage_used']; ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label"><i class="fas fa-users"></i> Active Sessions</span>
                    <span class="status-value"><?php echo $system_status['active_sessions']; ?></span>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="background:#fafcff; border-radius:32px; padding:1.4rem 1.2rem 1.2rem; border:1px solid #eaf0f8;">
                <div class="section-title">
                    <h3><i class="fas fa-bolt"></i> Admin Actions</h3>
                </div>
                <div class="quick-actions">
                    <button class="quick-btn primary" onclick="alert('Create new user (demo)')">
                        <i class="fas fa-user-plus"></i> Add User
                    </button>
                    <button class="quick-btn" onclick="alert('Manage courses (demo)')">
                        <i class="fas fa-book"></i> Manage Courses
                    </button>
                    <button class="quick-btn" onclick="alert('View all assignments (demo)')">
                        <i class="fas fa-file-alt"></i> All Assignments
                    </button>
                    <button class="quick-btn" onclick="alert('System settings (demo)')">
                        <i class="fas fa-cog"></i> Settings
                    </button>
                    <button class="quick-btn" onclick="alert('Generate report (demo)')">
                        <i class="fas fa-chart-pie"></i> Reports
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="admin-footer">
        <span><i class="fas fa-shield-alt"></i> Admin · <?php echo htmlspecialchars($admin_name); ?></span>
        <span><i class="fas fa-clock"></i> Last login: <?php echo htmlspecialchars($login_time); ?></span>
        <span><i class="fas fa-version"></i> v2.3.1</span>
    </div>
</div>

<script>
    // Refresh activity via AJAX (simulated)
    function refreshActivity() {
        const btn = document.querySelector('.refresh-btn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        
        const formData = new FormData();
        formData.append('action', 'get_recent_activity');
        
        fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const activityList = document.getElementById('activityList');
                // In a real app, you'd update the DOM with new data
                alert('Activity refreshed (demo) — check console for data');
                console.log('Activity data:', data.data);
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
            }
        })
        .catch(error => {
            alert('Refresh demo — activity updated');
            btn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
        });
    }

    // Auto-refresh stats every 60 seconds (optional)
    console.log('Admin Dashboard loaded. User: <?php echo addslashes($admin_name); ?>');
</script>

</body>
</html>