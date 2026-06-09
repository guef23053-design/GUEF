<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get statistics for reports
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$usersByCounty = mysqli_query($conn, "SELECT county, COUNT(*) as count FROM users GROUP BY county ORDER BY count DESC LIMIT 10");
$recentRegistrations = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count'];
$activeProjects = 52; // You can replace with actual projects table query
$totalImpact = 10500; // You can replace with actual impact data
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - GUEF</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --light: #f8f9fa;
            --dark: #1e1e1e;
            --gray: #6c757d;
            --white: #ffffff;
            --success: #27ae60;
            --warning: #f39c12;
            --info: #3498db;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9f4 0%, #e8f5e9 100%);
            min-height: 100vh;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: white;
            padding: 3px;
            border: 2px solid var(--primary);
        }

        .logo h1 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .logo span {
            font-size: 0.8rem;
            color: var(--gray);
            display: block;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }

        /* Content */
        .content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
            width: 100%;
            flex: 1;
        }

        .page-title {
            margin-bottom: 30px;
        }

        .page-title h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }

        .page-title p {
            color: var(--gray);
        }

        /* Stats Overview */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            text-align: center;
        }

        .stat-box i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .stat-box h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
        }

        .stat-box p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Report Cards */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .report-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .report-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px 25px;
        }

        .report-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .report-body {
            padding: 25px;
        }

        .county-list {
            list-style: none;
        }

        .county-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .county-item:last-child {
            border-bottom: none;
        }

        .county-name {
            font-weight: 500;
            color: var(--dark);
        }

        .county-count {
            background: rgba(45, 106, 79, 0.1);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .progress-item {
            margin-bottom: 20px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .progress-bar {
            height: 8px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
        }

        .export-section {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .export-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .export-btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .export-pdf {
            background: var(--danger);
            color: white;
        }

        .export-excel {
            background: var(--success);
            color: white;
        }

        .export-print {
            background: var(--info);
            color: white;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .footer {
            background: var(--white);
            padding: 20px 30px;
            text-align: center;
            color: var(--gray);
            font-size: 0.85rem;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .header-left {
                flex-direction: column;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .page-title h2 {
                font-size: 1.5rem;
            }
            
            .reports-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    
    <header class="header">
        <div class="header-left">
            <div class="logo">
                <img src="logo.jpg" alt="GUEF" onerror="this.src='https://via.placeholder.com/45/2d6a4f/ffffff?text=G'">
                <div>
                    <h1>GUEF Reports</h1>
                    <span>Great United Eastern Foundations</span>
                </div>
            </div>
        </div>
        
        <div class="header-right">
            <a href="dashboard.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <?php if ($is_admin): ?>
            <a href="admin.php" class="btn btn-primary">
                <i class="fas fa-shield-alt"></i> Admin Panel
            </a>
            <?php endif; ?>
        </div>
    </header>

    <main class="content">
        <div class="page-title">
            <h2><i class="fas fa-chart-bar" style="margin-right: 15px; color: var(--primary);"></i>Reports & Analytics</h2>
            <p>Track impact, monitor progress, and analyze community engagement data.</p>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-box">
                <i class="fas fa-users"></i>
                <h3><?php echo number_format($totalUsers); ?></h3>
                <p>Total Members</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-user-plus"></i>
                <h3><?php echo $recentRegistrations; ?></h3>
                <p>New Members (30 days)</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-project-diagram"></i>
                <h3><?php echo $activeProjects; ?></h3>
                <p>Active Projects</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-heart"></i>
                <h3><?php echo number_format($totalImpact); ?></h3>
                <p>Lives Impacted</p>
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="reports-grid">
            <!-- Membership by County -->
            <div class="report-card">
                <div class="report-header">
                    <h3><i class="fas fa-map-marker-alt"></i> Membership by County</h3>
                </div>
                <div class="report-body">
                    <ul class="county-list">
                        <?php 
                        $hasData = false;
                        while ($county = mysqli_fetch_assoc($usersByCounty)): 
                            $hasData = true;
                        ?>
                        <li class="county-item">
                            <span class="county-name"><?php echo htmlspecialchars($county['county'] ?: 'Unknown'); ?></span>
                            <span class="county-count"><?php echo $county['count']; ?> members</span>
                        </li>
                        <?php endwhile; ?>
                        <?php if (!$hasData): ?>
                        <li class="county-item">
                            <span class="county-name">No data available</span>
                            <span class="county-count">0</span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Program Progress -->
            <div class="report-card">
                <div class="report-header">
                    <h3><i class="fas fa-tasks"></i> Program Progress</h3>
                </div>
                <div class="report-body">
                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Sustainable Livelihoods</span>
                            <span>78%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 78%"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Skills Development</span>
                            <span>65%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 65%"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label">
                            <span>MSME Support</span>
                            <span>52%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 52%"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Market Linkages</span>
                            <span>43%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 43%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Impact Summary -->
            <div class="report-card">
                <div class="report-header">
                    <h3><i class="fas fa-chart-pie"></i> Impact Summary</h3>
                </div>
                <div class="report-body">
                    <div class="progress-item">
                        <div class="progress-label">
                            <span><i class="fas fa-seedling"></i> Agriculture Support</span>
                            <span>2,500+ farmers</span>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label">
                            <span><i class="fas fa-graduation-cap"></i> Training Programs</span>
                            <span>1,200+ graduates</span>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label">
                            <span><i class="fas fa-hand-holding-usd"></i> Microfinance</span>
                            <span>KSh 5.2M disbursed</span>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label">
                            <span><i class="fas fa-store"></i> MSMEs Supported</span>
                            <span>350+ businesses</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
                <i class="fas fa-download" style="margin-right: 10px;"></i>Export Reports
            </h3>
            <div class="export-buttons">
                <button class="export-btn export-pdf" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf"></i> Export as PDF
                </button>
                <button class="export-btn export-excel" onclick="exportReport('excel')">
                    <i class="fas fa-file-excel"></i> Export as Excel
                </button>
                <button class="export-btn export-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> Great United Eastern Foundations | Empowering Communities for a Better Future</p>
    </footer>
</div>

<script>
    function exportReport(format) {
        if (format === 'pdf') {
            alert('PDF export functionality will be implemented with a PDF library like TCPDF or Dompdf.');
        } else if (format === 'excel') {
            // Export to CSV
            window.location.href = 'export_data.php?format=csv';
        }
    }
</script>

</body>
</html>