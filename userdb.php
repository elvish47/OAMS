<?php
// userdashboard.php
// This file simulates a user dashboard with PHP session handling and dynamic data.
// For a real system, replace the static arrays with database queries.

// Start session to simulate login state
session_start();

// --- Simulate user login (for demo purposes) ---
// In a real app, you would check credentials against a database.
// For this demo, we set a default user if no session exists.
if (!isset($_SESSION['user_id'])) {
    // Set a demo user session
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Jane Doe';
    $_SESSION['user_role'] = 'instructor'; // or 'student'
    $_SESSION['user_email'] = 'jane.doe@assignflow.com';
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
}

// --- Simulated data (replace with database queries) ---
$user_name = $_SESSION['user_name'] ?? 'Guest';
$user_role = $_SESSION['user_role'] ?? 'instructor';
$user_email = $_SESSION['user_email'] ?? 'guest@assignflow.com';
$login_time = $_SESSION['login_time'] ?? date('Y-m-d H:i:s');

// Assignment data (static for demo)
$assignments = [
    [
        'id' => 1,
        'title' => 'Data Structures · Hash Map',
        'course' => 'CS-201',
        'due_date' => '2026-07-18',
        'status' => 'pending'
    ],
    [
        'id' => 2,
        'title' => 'Algorithms · Sorting Visualizer',
        'course' => 'CS-301',
        'due_date' => '2026-07-22',
        'status' => 'review'
    ],
    [
        'id' => 3,
        'title' => 'Web Dev · Portfolio Project',
        'course' => 'CS-401',
        'due_date' => '2026-07-10',
        'status' => 'done'
    ],
    [
        'id' => 4,
        'title' => 'Database · SQL Design',
        'course' => 'CS-250',
        'due_date' => '2026-08-02',
        'status' => 'pending'
    ],
    [
        'id' => 5,
        'title' => 'AI · Search Algorithms',
        'course' => 'CS-470',
        'due_date' => '2026-07-30',
        'status' => 'review'
    ]
];

// Calculate statistics
$total_assignments = count($assignments);
$pending = 0;
$review = 0;
$done = 0;
$due_this_week = 0;

$today = new DateTime();
$week_end = clone $today;
$week_end->modify('+7 days');

foreach ($assignments as $assignment) {
    switch ($assignment['status']) {
        case 'pending': $pending++; break;
        case 'review': $review++; break;
        case 'done': $done++; break;
    }
    
    $due_date = new DateTime($assignment['due_date']);
    if ($due_date >= $today && $due_date <= $week_end) {
        $due_this_week++;
    }
}

// Upcoming assignments (sorted by date)
$upcoming = array_filter($assignments, function($a) use ($today) {
    return new DateTime($a['due_date']) >= $today;
});
usort($upcoming, function($a, $b) {
    return strtotime($a['due_date']) - strtotime($b['due_date']);
});
$upcoming = array_slice($upcoming, 0, 3); // show top 3

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php'); // redirect to login page (create if needed)
    exit;
}

// Handle AJAX / form actions (simulated)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    switch ($_POST['action']) {
        case 'new_assignment':
            echo json_encode(['success' => true, 'message' => 'New assignment created (demo)']);
            break;
        case 'view_submissions':
            echo json_encode(['success' => true, 'message' => 'Submissions view (demo)']);
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
    <title>User Dashboard · AssignFlow</title>
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
            background: #f2f6fd;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        .dashboard {
            max-width: 1200px;
            width: 100%;
            background: white;
            border-radius: 44px;
            padding: 2rem 2.2rem 2.5rem;
            box-shadow: 0 20px 60px rgba(18, 40, 70, 0.12), 0 8px 24px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.2s;
        }

        .dash-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.2rem;
        }

        .brand-area {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .brand-area i {
            font-size: 2rem;
            color: #1d3b5e;
            background: rgba(29, 59, 94, 0.08);
            padding: 0.45rem;
            border-radius: 16px;
        }

        .brand-area h2 {
            font-weight: 600;
            font-size: 1.8rem;
            letter-spacing: -0.02em;
            color: #0b1e33;
        }

        .brand-area span {
            color: #1d3b5e;
            font-weight: 300;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            background: #f0f5fe;
            padding: 0.4rem 1rem 0.4rem 1.2rem;
            border-radius: 60px;
            border: 1px solid #dae4f2;
        }

        .user-profile i {
            font-size: 1.2rem;
            color: #1d3b5e;
        }

        .user-profile .avatar {
            background: #1d3b5e;
            color: white;
            width: 2.4rem;
            height: 2.4rem;
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .user-profile .user-name {
            font-weight: 500;
            color: #0b1e33;
        }

        .user-profile .logout-btn {
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

        .user-profile .logout-btn:hover {
            color: #b13e3e;
            background: rgba(177, 62, 62, 0.06);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2.8rem;
        }

        .stat-card {
            background: #f8faff;
            padding: 1.2rem 1rem 1rem 1.4rem;
            border-radius: 28px;
            border: 1px solid #eaf0f8;
            transition: 0.15s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.01);
        }

        .stat-card:hover {
            border-color: #c2d6ed;
            background: white;
        }

        .stat-card .stat-icon {
            font-size: 1.8rem;
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
            font-size: 0.9rem;
            color: #3d5a7a;
            font-weight: 450;
        }

        .dash-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 2rem;
            margin-bottom: 1.8rem;
        }

        .assignments-section {
            background: #fafcff;
            border-radius: 32px;
            padding: 1.4rem 1.2rem 1.2rem;
            border: 1px solid #eaf0f8;
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.4rem;
            flex-wrap: wrap;
        }

        .section-title h3 {
            font-weight: 600;
            font-size: 1.25rem;
            color: #0b1e33;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title h3 i {
            color: #1d3b5e;
            font-size: 1.1rem;
        }

        .section-title a {
            color: #1d3b5e;
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            cursor: pointer;
        }

        .section-title a:hover {
            border-bottom-color: #1d3b5e;
        }

        .assignment-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 0.6rem 0.9rem 0.8rem;
            border-bottom: 1px solid #e9eff7;
            transition: 0.1s;
            border-radius: 12px;
            cursor: pointer;
        }

        .assignment-item:hover {
            background: #f0f6ff;
        }

        .assignment-item:last-child {
            border-bottom: none;
        }

        .assignment-info {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .assignment-info .title {
            font-weight: 500;
            color: #0b1e33;
        }

        .assignment-info .meta {
            font-size: 0.8rem;
            color: #4d6d8f;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .assignment-info .meta i {
            font-size: 0.7rem;
            color: #5f7f9f;
        }

        .assignment-status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.9rem;
            border-radius: 60px;
            background: #e2ecfa;
            color: #1d3b5e;
            white-space: nowrap;
        }

        .status-done {
            background: #d7ecdb;
            color: #1a6d3b;
        }

        .status-pending {
            background: #fff1d6;
            color: #a8671a;
        }

        .status-review {
            background: #dae6f7;
            color: #20518a;
        }

        .right-panel {
            display: flex;
            flex-direction: column;
            gap: 1.8rem;
        }

        .upcoming-card {
            background: #fafcff;
            border-radius: 32px;
            padding: 1.4rem 1.2rem 1.2rem;
            border: 1px solid #eaf0f8;
        }

        .upcoming-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.7rem 0.2rem;
            border-bottom: 1px solid #edf3fb;
        }

        .upcoming-item:last-child {
            border-bottom: none;
        }

        .upcoming-item .badge-date {
            background: #e2ebf7;
            border-radius: 30px;
            padding: 0.2rem 0.9rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: #1d3b5e;
            min-width: 60px;
            text-align: center;
        }

        .upcoming-item .info {
            font-size: 0.95rem;
            font-weight: 470;
            color: #0b1e33;
        }

        .upcoming-item .info small {
            font-weight: 400;
            color: #4d6d8f;
            font-size: 0.8rem;
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.7rem;
            margin-top: 0.2rem;
        }

        .quick-btn {
            background: white;
            border: 1px solid #d6e2f2;
            border-radius: 60px;
            padding: 0.7rem 1.3rem;
            font-weight: 500;
            color: #1d3b5e;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.9rem;
            cursor: pointer;
            transition: 0.15s;
            flex: 1 0 auto;
            justify-content: center;
        }

        .quick-btn i {
            color: #2a4b7c;
        }

        .quick-btn:hover {
            background: #eaf1fc;
            border-color: #a6bedc;
            transform: scale(0.97);
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

        .dash-footer {
            margin-top: 2.5rem;
            border-top: 1px solid #e3ebf5;
            padding-top: 1.3rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            align-items: center;
            color: #4a688b;
            font-size: 0.9rem;
        }

        .dash-footer i {
            margin-right: 0.2rem;
            opacity: 0.6;
        }

        @media (max-width: 820px) {
            .dash-grid {
                grid-template-columns: 1fr;
                gap: 1.8rem;
            }
            .dashboard {
                padding: 1.5rem;
            }
            .user-profile .user-name {
                display: none;
            }
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 500px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .dash-header {
                flex-direction: column;
                align-items: start;
                gap: 1rem;
            }
            .user-profile {
                width: 100%;
                justify-content: space-between;
            }
            .quick-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="dashboard">

    <!-- Header -->
    <header class="dash-header">
        <div class="brand-area">
            <i class="fas fa-pencil-alt"></i>
            <h2>Assign<span>Flow</span></h2>
        </div>
        <div class="user-profile">
            <i class="fas fa-bell" style="opacity:0.7;"></i>
            <span class="avatar"><?php echo strtoupper(substr($user_name, 0, 2)); ?></span>
            <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
            <a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            <div class="stat-number"><?php echo $total_assignments; ?></div>
            <div class="stat-label">Total assignments</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-number"><?php echo $pending; ?></div>
            <div class="stat-label">Pending review</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-number"><?php echo $done; ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-number"><?php echo $due_this_week; ?></div>
            <div class="stat-label">Due this week</div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="dash-grid">

        <!-- Left: Assignment List -->
        <section class="assignments-section">
            <div class="section-title">
                <h3><i class="fas fa-list-ul"></i> Recent assignments</h3>
                <a onclick="alert('View all assignments (demo)')"><i class="fas fa-arrow-right"></i> View all</a>
            </div>

            <?php foreach ($assignments as $assignment): 
                $status_class = '';
                $status_label = ucfirst($assignment['status']);
                if ($assignment['status'] === 'done') $status_class = 'status-done';
                elseif ($assignment['status'] === 'pending') $status_class = 'status-pending';
                elseif ($assignment['status'] === 'review') $status_class = 'status-review';
            ?>
            <div class="assignment-item" onclick="alert('View details for: <?php echo addslashes($assignment['title']); ?> (demo)')">
                <div class="assignment-info">
                    <span class="title"><?php echo htmlspecialchars($assignment['title']); ?></span>
                    <span class="meta">
                        <i class="fas fa-user-graduate"></i> <?php echo htmlspecialchars($assignment['course']); ?>
                        &nbsp; <i class="fas fa-calendar-alt"></i> Due: <?php echo date('M d', strtotime($assignment['due_date'])); ?>
                    </span>
                </div>
                <span class="assignment-status <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
            </div>
            <?php endforeach; ?>
        </section>

        <!-- Right Panel -->
        <div class="right-panel">

            <!-- Upcoming -->
            <div class="upcoming-card">
                <div class="section-title" style="margin-bottom:0.8rem;">
                    <h3><i class="fas fa-calendar-check"></i> Upcoming</h3>
                    <a onclick="alert('Show full calendar (demo)')"><i class="fas fa-arrow-right"></i></a>
                </div>
                <?php if (count($upcoming) > 0): ?>
                    <?php foreach ($upcoming as $item): ?>
                    <div class="upcoming-item">
                        <span class="badge-date"><?php echo date('M d', strtotime($item['due_date'])); ?></span>
                        <span class="info">
                            <?php echo htmlspecialchars($item['title']); ?>
                            <small>· <?php echo htmlspecialchars($item['course']); ?></small>
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 0.8rem 0; color: #4d6d8f;">No upcoming assignments</div>
                <?php endif; ?>
                <div style="margin-top:0.8rem; font-size:0.85rem; color:#3d5a7a; display:flex; gap:0.4rem; align-items:center;">
                    <i class="fas fa-circle" style="color:#4f8bc9; font-size:0.5rem;"></i> 
                    <?php echo max(0, $total_assignments - count($upcoming)); ?> more this month
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="background:#fafcff; border-radius:32px; padding:1.2rem 1.2rem 1.5rem; border:1px solid #eaf0f8;">
                <h3 style="font-weight:600; font-size:1rem; color:#0b1e33; margin-bottom:1.2rem; display:flex; gap:0.5rem;">
                    <i class="fas fa-bolt" style="color:#1d3b5e;"></i> Quick actions
                </h3>
                <div class="quick-actions">
                    <button class="quick-btn primary" onclick="handleAction('new_assignment')">
                        <i class="fas fa-plus-circle"></i> New
                    </button>
                    <button class="quick-btn" onclick="handleAction('view_submissions')">
                        <i class="fas fa-folder-open"></i> Submissions
                    </button>
                    <button class="quick-btn" onclick="alert('Gradebook view (demo)')">
                        <i class="fas fa-graduation-cap"></i> Gradebook
                    </button>
                    <button class="quick-btn" onclick="alert('Messages / notifications (demo)')">
                        <i class="fas fa-comment"></i> Messages
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="dash-footer">
        <span><i class="fas fa-user-circle"></i> <?php echo ucfirst($user_role); ?> · <?php echo htmlspecialchars($user_name); ?></span>
        <span><i class="fas fa-clock"></i> Last login: <?php echo htmlspecialchars($login_time); ?></span>
        <span><i class="fas fa-database"></i> 3 active courses</span>
    </div>
</div>

<script>
    // Handle quick action buttons with AJAX (simulated)
    function handleAction(action) {
        const formData = new FormData();
        formData.append('action', action);
        
        fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Action completed (demo)');
        })
        .catch(error => {
            alert('Action triggered (demo) — ' + action);
        });
    }

    // Optional: click on assignment items is already handled inline
    console.log('AssignFlow Dashboard • User: <?php echo addslashes($user_name); ?>');
</script>

</body>
</html>