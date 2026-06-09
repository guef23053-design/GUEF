<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current user details
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

// Check if user is admin
if ($user['role'] != 'admin') {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Access Denied - GUEF</title>
        <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
        <style>
            body { font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: linear-gradient(135deg, #f0f9f4 0%, #e8f5e9 100%); margin: 0; }
            .error-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); text-align: center; max-width: 400px; }
            .error-card i { font-size: 4rem; color: #dc3545; margin-bottom: 20px; }
            .error-card h2 { color: #1b4332; margin-bottom: 15px; }
            .error-card p { color: #6c757d; margin-bottom: 25px; }
            .error-card a { background: #2d6a4f; color: white; padding: 12px 30px; border-radius: 30px; text-decoration: none; font-weight: 500; transition: all 0.3s; display: inline-block; }
            .error-card a:hover { background: #1b4332; transform: translateY(-2px); }
        </style>
    </head>
    <body>
        <div class='error-card'>
            <i class='fas fa-lock'></i>
            <h2>Access Denied</h2>
            <p>You don't have permission to access this page. This area is restricted to administrators only.</p>
            <a href='dashboard.php'><i class='fas fa-arrow-left'></i> Back to Dashboard</a>
        </div>
    </body>
    </html>";
    exit();
}

// Handle user count statistics
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$totalAdmins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='admin'"))['total'];
$totalRegularUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'];
$recentUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['total'];

// Get all users with all details
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Great United Eastern Foundations</title>
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2d6a4f;
            --primary-dark: #1b4332;
            --secondary: #74c69d;
            --accent: #40916c;
            --light: #f8f9fa;
            --dark: #1e1e1e;
            --gray: #6c757d;
            --white: #ffffff;
            --warning: #f39c12;
            --danger: #e74c3c;
            --success: #27ae60;
            --info: #3498db;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9f4 0%, #e8f5e9 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(45, 106, 79, 0.2);
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-small {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: white;
            padding: 5px;
            border: 3px solid var(--white);
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .nav-links a.logout {
            background: rgba(231, 76, 60, 0.3);
        }

        .nav-links a.logout:hover {
            background: rgba(231, 76, 60, 0.5);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px 20px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            border: 1px solid rgba(45, 106, 79, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(45, 106, 79, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-icon.primary { background: rgba(45, 106, 79, 0.1); color: var(--primary); }
        .stat-icon.success { background: rgba(39, 174, 96, 0.1); color: var(--success); }
        .stat-icon.warning { background: rgba(243, 156, 18, 0.1); color: var(--warning); }
        .stat-icon.info { background: rgba(52, 152, 219, 0.1); color: var(--info); }

        .stat-content h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            line-height: 1.2;
        }

        .stat-content p {
            color: var(--gray);
            font-size: 0.95rem;
        }

        /* Card Styles */
        .card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-header h2 {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h2 i {
            color: var(--primary);
        }

        .search-box {
            display: flex;
            gap: 10px;
        }

        .search-box input {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 30px;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: border-color 0.3s;
            min-width: 250px;
        }

        .search-box input:focus {
            border-color: var(--primary);
        }

        .export-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .export-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .card-body {
            padding: 0;
            overflow-x: auto;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        table th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 15px 12px;
            font-weight: 500;
            font-size: 0.9rem;
            text-align: left;
            white-space: nowrap;
        }

        table td {
            padding: 15px 12px;
            border-bottom: 1px solid #eee;
            color: var(--dark);
            font-size: 0.9rem;
            white-space: nowrap;
        }

        table tr:hover {
            background: rgba(45, 106, 79, 0.03);
        }

        table tr:last-child td {
            border-bottom: none;
        }

        .role-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .role-admin {
            background: rgba(39, 174, 96, 0.15);
            color: var(--success);
        }

        .role-user {
            background: rgba(52, 152, 219, 0.15);
            color: var(--info);
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .action-btn.edit {
            background: rgba(52, 152, 219, 0.1);
            color: var(--info);
        }

        .action-btn.edit:hover {
            background: var(--info);
            color: white;
        }

        .action-btn.delete {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger);
        }

        .action-btn.delete:hover {
            background: var(--danger);
            color: white;
        }

        .action-btn.view {
            background: rgba(45, 106, 79, 0.1);
            color: var(--primary);
        }

        .action-btn.view:hover {
            background: var(--primary);
            color: white;
        }

        .protected-badge {
            background: #f1f3f5;
            color: var(--gray);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-left {
                flex-direction: column;
            }

            .header-right {
                flex-direction: column;
                width: 100%;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }

            .nav-links a {
                flex: 1;
                justify-content: center;
                min-width: 120px;
            }

            .card-header {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                flex-direction: column;
            }

            .search-box input {
                min-width: auto;
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <img src="logo.jpg" alt="GUEF Logo" class="logo-small">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Great United Eastern Foundations - Management Portal</p>
                </div>
            </div>
            <div class="header-right">
                <div class="admin-badge">
                    <i class="fas fa-shield-alt"></i>
                    Administrator: <?php echo htmlspecialchars($user['username']); ?>
                </div>
                <div class="nav-links">
                    <a href="dashboard.php"><i class="fas fa-user"></i> My Dashboard</a>
                    <a href="index.html"><i class="fas fa-home"></i> Home</a>
                    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalUsers; ?></h3>
                <p>Total Users</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalAdmins; ?></h3>
                <p>Administrators</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-user"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalRegularUsers; ?></h3>
                <p>Regular Users</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $recentUsers; ?></h3>
                <p>New Users (7 days)</p>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card">
        <div class="card-header">
            <h2>
                <i class="fas fa-list"></i>
                All Registered Users
                <span style="font-size: 0.9rem; font-weight: normal; color: var(--gray); margin-left: 10px;">
                    (<?php echo $totalUsers; ?> total)
                </span>
            </h2>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search users..." onkeyup="filterTable()">
                <button class="export-btn" onclick="exportToCSV()">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>National ID</th>
                        <th>County</th>
                        <th>Group Name</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($users) > 0) {
                        while ($row = mysqli_fetch_assoc($users)) {
                            $registered_date = isset($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : 'N/A';
                            ?>
                            <tr>
                                <td><strong>#<?php echo $row['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['national_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['county']); ?></td>
                                <td><?php echo htmlspecialchars($row['group_name']); ?></td>
                                <td>
                                    <span class="role-badge <?php echo $row['role'] == 'admin' ? 'role-admin' : 'role-user'; ?>">
                                        <?php echo ucfirst($row['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo $registered_date; ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="view_user.php?id=<?php echo $row['id']; ?>" class="action-btn view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="action-btn edit" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($row['id'] != $user_id): ?>
                                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" 
                                               class="action-btn delete" 
                                               title="Delete User"
                                               onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($row['full_name']); ?>?\nThis action cannot be undone!')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="protected-badge" title="Cannot delete yourself">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                        }
                    } else {
                        echo "<tr><td colspan='11' style='text-align: center; padding: 40px; color: var(--gray);'>
                                <i class='fas fa-users' style='font-size: 3rem; margin-bottom: 15px; opacity: 0.3;'></i><br>
                                No users found in the database.
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>© <?php echo date('Y'); ?> Great United Eastern Foundations | Admin Panel v1.0</p>
    </div>

</div>

<script>
    // Table Search Function
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('usersTable');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < td.length - 1; j++) {
                if (td[j]) {
                    const txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            tr[i].style.display = found ? '' : 'none';
        }
    }

    // Export to CSV Function
    function exportToCSV() {
        const table = document.getElementById('usersTable');
        const rows = table.querySelectorAll('tr');
        let csv = [];
        
        // Get headers
        const headers = [];
        const headerCells = rows[0].querySelectorAll('th');
        for (let i = 0; i < headerCells.length - 1; i++) {
            headers.push('"' + headerCells[i].innerText.replace(/"/g, '""') + '"');
        }
        csv.push(headers.join(','));
        
        // Get data
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            if (row.style.display !== 'none') {
                const rowData = [];
                const cells = row.querySelectorAll('td');
                for (let j = 0; j < cells.length - 1; j++) {
                    let text = cells[j].innerText.replace(/"/g, '""');
                    rowData.push('"' + text + '"');
                }
                csv.push(rowData.join(','));
            }
        }
        
        // Download
        const csvString = csv.join('\n');
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'guef_users_' + new Date().toISOString().slice(0,10) + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Keyboard shortcut for search (Ctrl+F)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            document.getElementById('searchInput').focus();
        }
    });
</script>

</body>
</html>