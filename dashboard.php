<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current user details with all fields
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

// Check if user is admin
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get dynamic statistics - WITH TABLE EXISTENCE CHECKS
$totalUsers = 0;
$usersResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
if ($usersResult) {
    $totalUsers = mysqli_fetch_assoc($usersResult)['total'];
}

$totalProjects = 50; // Default value
$projectsResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM projects");
if ($projectsResult) {
    $totalProjects = mysqli_fetch_assoc($projectsResult)['total'];
}

$totalPartners = 25; // Default value
$partnersResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM partners");
if ($partnersResult) {
    $totalPartners = mysqli_fetch_assoc($partnersResult)['total'];
}

$totalRegions = 15; // Default value
$regionsResult = mysqli_query($conn, "SELECT COUNT(DISTINCT county) as total FROM users");
if ($regionsResult) {
    $totalRegions = mysqli_fetch_assoc($regionsResult)['total'];
}

// Get recent users for activity feed (admin only)
$recentUsers = [];
if ($is_admin) {
    $recentQuery = mysqli_query($conn, "SELECT full_name, county, created_at FROM users ORDER BY id DESC LIMIT 5");
    if ($recentQuery) {
        while ($row = mysqli_fetch_assoc($recentQuery)) {
            $recentUsers[] = $row;
        }
    }
}

// Get user's recent activity
$userActivities = [];
if (!$is_admin) {
    $userActivities[] = [
        'action' => 'Account created',
        'date' => $user['created_at'] ?? date('Y-m-d H:i:s'),
        'icon' => '👤'
    ];
}

// Organization photos array
$gallery_images = [
    ['src' => '1.jpg', 'alt' => 'Community Outreach', 'caption' => 'Community Outreach Programs'],
    ['src' => '2.jpg', 'alt' => 'Skills Workshop', 'caption' => 'Vocational Training Workshops'],
    ['src' => '3.jpg', 'alt' => 'Women Empowerment', 'caption' => 'Women Empowerment Initiatives'],
    ['src' => '4.jpg', 'alt' => 'Agriculture Project', 'caption' => 'Sustainable Agriculture Projects'],
    ['src' => '6.jpg', 'alt' => 'Youth Training', 'caption' => 'Youth Skills Development'],
    ['src' => '7.jpg', 'alt' => 'Market Linkage', 'caption' => 'Market Linkage Programs'],
    ['src' => '8.jpg', 'alt' => 'Community Meeting', 'caption' => 'Community Engagement'],
    ['src' => '9.jpg', 'alt' => 'Green Technology', 'caption' => 'Green Technology Initiatives']
];

// Notification count
$notificationCount = 3;
$notifications = [
    ['message' => 'Welcome to GUEF! Complete your profile.', 'time' => 'Just now', 'icon' => '👋', 'read' => false],
    ['message' => 'New program: Sustainable Farming Workshop', 'time' => '2 hours ago', 'icon' => '🌱', 'read' => false],
    ['message' => 'Your account has been verified', 'time' => '1 day ago', 'icon' => '✅', 'read' => true]
];

// Rest of the HTML remains exactly the same...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#2d6a4f">
    <title>Dashboard - Great United Eastern Foundations</title>
    
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
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9f4 0%, #e8f5e9 100%);
            min-height: 100vh;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: var(--white);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 5px 0 30px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: white;
            padding: 5px;
            margin-bottom: 15px;
            border: 3px solid var(--white);
        }

        .sidebar-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            padding: 0 15px;
            margin-bottom: 20px;
        }

        .menu-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.6;
            padding: 0 15px;
            margin-bottom: 10px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .menu-item i {
            width: 24px;
            font-size: 1.2rem;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
            border-left: 3px solid var(--white);
        }

        .menu-item .badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ===== TOP HEADER ===== */
        .top-header {
            background: var(--white);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
        }

        .page-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 8px;
        }

        .notification-bell i {
            font-size: 1.3rem;
            color: var(--gray);
            transition: color 0.3s;
        }

        .notification-bell:hover i {
            color: var(--primary);
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--danger);
            color: white;
            font-size: 0.6rem;
            font-weight: 600;
            padding: 2px 5px;
            border-radius: 20px;
            min-width: 18px;
            text-align: center;
        }

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--white);
            width: 320px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 15px;
            display: none;
            border: 1px solid rgba(45, 106, 79, 0.1);
        }

        .notification-bell:hover .notification-dropdown {
            display: block;
        }

        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            border-radius: 10px;
            transition: background 0.3s;
            border-bottom: 1px solid #eee;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.unread {
            background: rgba(45, 106, 79, 0.05);
        }

        .notification-icon {
            width: 35px;
            height: 35px;
            background: rgba(45, 106, 79, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .notification-content {
            flex: 1;
        }

        .notification-content p {
            font-size: 0.85rem;
            color: var(--dark);
            margin-bottom: 3px;
        }

        .notification-time {
            font-size: 0.7rem;
            color: var(--gray);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 40px;
            transition: background 0.3s;
        }

        .user-profile:hover {
            background: var(--light);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--gray);
        }

        /* ===== DASHBOARD CONTENT ===== */
        .content-wrapper {
            padding: 30px;
            flex: 1;
        }

        /* Hero Welcome */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 35px 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(45, 106, 79, 0.25);
        }

        .welcome-card h1 {
            font-size: clamp(24px, 5vw, 32px);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .welcome-card p {
            font-size: clamp(14px, 3vw, 16px);
            opacity: 0.95;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px 20px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            text-align: center;
            transition: all 0.3s;
            border: 1px solid rgba(45, 106, 79, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(45, 106, 79, 0.12);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Two Column Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 25px;
            margin-bottom: 30px;
        }

        /* Profile Card */
        .profile-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 30px 20px;
            text-align: center;
        }

        .profile-avatar {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 4px solid var(--white);
        }

        .profile-avatar span {
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--primary);
        }

        .profile-header h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .profile-header p {
            opacity: 0.9;
            font-size: 0.85rem;
        }

        .profile-body {
            padding: 25px;
        }

        .profile-info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .profile-info-item:last-child {
            border-bottom: none;
        }

        .profile-info-icon {
            width: 40px;
            height: 40px;
            background: rgba(45, 106, 79, 0.08);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .profile-info-content {
            flex: 1;
        }

        .profile-info-label {
            font-size: 0.75rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .profile-info-value {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 0.95rem;
        }

        .edit-profile-btn {
            display: block;
            background: var(--primary);
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            margin-top: 15px;
            transition: all 0.3s;
        }

        .edit-profile-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 106, 79, 0.3);
        }

        /* Activity Feed */
        .activity-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            padding: 20px;
        }

        .activity-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: rgba(45, 106, 79, 0.08);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .activity-content p {
            font-size: 0.9rem;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .activity-time {
            font-size: 0.75rem;
            color: var(--gray);
        }

        /* Gallery Section */
        .gallery-section {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .gallery-container {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
        }

        .gallery-track {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            padding: 10px 5px 20px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .gallery-track::-webkit-scrollbar {
            height: 6px;
        }

        .gallery-track::-webkit-scrollbar-track {
            background: #e0e0e0;
            border-radius: 10px;
        }

        .gallery-track::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        .gallery-item {
            flex: 0 0 280px;
            scroll-snap-align: start;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            height: 200px;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
            padding: 30px 15px 15px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .gallery-nav {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
        }

        .nav-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e0;
            cursor: pointer;
            transition: all 0.3s;
        }

        .nav-dot.active {
            background: var(--primary);
            width: 24px;
            border-radius: 20px;
        }

        /* Quick Actions */
        .quick-actions {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .action-btn {
            background: var(--light);
            color: var(--primary-dark);
            padding: 12px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(45, 106, 79, 0.1);
        }

        .action-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 106, 79, 0.25);
        }

        .action-btn.primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
        }

        .action-btn.primary:hover {
            box-shadow: 0 8px 20px rgba(45, 106, 79, 0.35);
        }

        /* Programs Grid */
        .programs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .program-card {
            background: var(--light);
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid var(--primary);
            transition: all 0.3s;
        }

        .program-card:hover {
            background: var(--white);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transform: translateX(5px);
        }

        .program-card h4 {
            color: var(--primary-dark);
            margin-bottom: 10px;
            font-size: 1rem;
            font-weight: 600;
        }

        .program-card p {
            color: var(--gray);
            font-size: 0.85rem;
            line-height: 1.6;
        }

        /* Footer */
        .dashboard-footer {
            background: var(--white);
            padding: 20px 30px;
            text-align: center;
            color: var(--gray);
            font-size: 0.85rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin-top: auto;
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .top-header {
                padding: 15px 20px;
            }

            .content-wrapper {
                padding: 20px 15px;
            }

            .user-info {
                display: none;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .gallery-item {
                flex: 0 0 85%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-btn {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 1rem;
            }

            .welcome-card {
                padding: 25px 20px;
            }
        }

        /* Edit Profile Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-header h3 {
            color: var(--primary-dark);
            font-size: 1.3rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-dark);
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: var(--light);
            color: var(--gray);
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="logo.jpg" alt="GUEF Logo" class="sidebar-logo" onerror="this.src='https://via.placeholder.com/80/2d6a4f/ffffff?text=GUEF'">
            <h3>Great United Eastern</h3>
            <p>Foundations</p>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main Menu</div>
                <a href="dashboard.php" class="menu-item active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics</span>
                </a>
            </div>
            
            <div class="menu-section">
                <div class="menu-section-title">Programs</div>
                <a href="#" class="menu-item">
                    <i class="fas fa-seedling"></i>
                    <span>My Projects</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-calendar"></i>
                    <span>Events</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Community</span>
                </a>
            </div>
            
            <div class="menu-section">
                <div class="menu-section-title">Settings</div>
                <a href="#" class="menu-item" onclick="openEditProfileModal()">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-question-circle"></i>
                    <span>Help & Support</span>
                </a>
                <?php if ($is_admin): ?>
                <a href="admin.php" class="menu-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Admin Panel</span>
                </a>
                <?php endif; ?>
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="page-title">Dashboard</h2>
            </div>
            
            <div class="header-right">
                <!-- Notifications -->
                <div class="notification-bell">
                    <i class="far fa-bell"></i>
                    <?php if ($notificationCount > 0): ?>
                    <span class="notification-badge"><?php echo $notificationCount; ?></span>
                    <?php endif; ?>
                    <div class="notification-dropdown">
                        <h4 style="margin-bottom: 10px; color: var(--primary-dark);">Notifications</h4>
                        <?php foreach ($notifications as $notif): ?>
                        <div class="notification-item <?php echo !$notif['read'] ? 'unread' : ''; ?>">
                            <div class="notification-icon"><?php echo $notif['icon']; ?></div>
                            <div class="notification-content">
                                <p><?php echo $notif['message']; ?></p>
                                <span class="notification-time"><?php echo $notif['time']; ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <a href="#" style="display: block; text-align: center; padding: 10px; color: var(--primary); text-decoration: none; font-size: 0.85rem;">View All</a>
                    </div>
                </div>
                
                <!-- User Profile -->
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                        <span class="user-role"><?php echo $is_admin ? 'Administrator' : 'Member'; ?></span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Wrapper -->
        <main class="content-wrapper">
            
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h1>Welcome Back, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h1>
                <p>Together, we're empowering communities and building sustainable futures across Eastern regions.</p>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">🌱</div>
                    <div class="stat-number"><?php echo $totalProjects; ?>+</div>
                    <div class="stat-label">Active Projects</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number"><?php echo number_format($totalUsers); ?>+</div>
                    <div class="stat-label">Community Members</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🤝</div>
                    <div class="stat-number"><?php echo $totalPartners; ?>+</div>
                    <div class="stat-label">Partners</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🌍</div>
                    <div class="stat-number"><?php echo $totalRegions; ?></div>
                    <div class="stat-label">Regions</div>
                </div>
            </div>

            <!-- Profile & Activity Grid -->
            <div class="dashboard-grid">
                
                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <span><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                        <p>Member since <?php echo isset($user['created_at']) ? date('F Y', strtotime($user['created_at'])) : date('F Y'); ?></p>
                    </div>
                    <div class="profile-body">
                        <div class="profile-info-item">
                            <div class="profile-info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="profile-info-content">
                                <div class="profile-info-label">Username</div>
                                <div class="profile-info-value">@<?php echo htmlspecialchars($user['username']); ?></div>
                            </div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="profile-info-content">
                                <div class="profile-info-label">Email</div>
                                <div class="profile-info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="profile-info-content">
                                <div class="profile-info-label">Phone</div>
                                <div class="profile-info-value"><?php echo htmlspecialchars($user['phone']); ?></div>
                            </div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="profile-info-content">
                                <div class="profile-info-label">National ID</div>
                                <div class="profile-info-value"><?php echo htmlspecialchars($user['national_id']); ?></div>
                            </div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="profile-info-content">
                                <div class="profile-info-label">County</div>
                                <div class="profile-info-value"><?php echo htmlspecialchars($user['county']); ?></div>
                            </div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="profile-info-content">
                                <div class="profile-info-label">Group</div>
                                <div class="profile-info-value"><?php echo htmlspecialchars($user['group_name']); ?></div>
                            </div>
                        </div>
                        
                        <a href="#" class="edit-profile-btn" onclick="openEditProfileModal()">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                    </div>
                </div>

                <!-- Activity Feed -->
                <div class="activity-card">
                    <div class="activity-title">
                        <i class="fas fa-clock"></i>
                        Recent Activity
                    </div>
                    
                    <?php if ($is_admin && !empty($recentUsers)): ?>
                        <ul class="activity-list">
                            <?php foreach ($recentUsers as $recent): ?>
                            <li class="activity-item">
                                <div class="activity-icon">👤</div>
                                <div class="activity-content">
                                    <p><strong><?php echo htmlspecialchars($recent['full_name']); ?></strong> joined from <?php echo htmlspecialchars($recent['county']); ?></p>
                                    <span class="activity-time"><?php echo isset($recent['created_at']) ? date('M d, Y', strtotime($recent['created_at'])) : 'Recently'; ?></span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <ul class="activity-list">
                            <?php foreach ($userActivities as $activity): ?>
                            <li class="activity-item">
                                <div class="activity-icon"><?php echo $activity['icon']; ?></div>
                                <div class="activity-content">
                                    <p><?php echo $activity['action']; ?></p>
                                    <span class="activity-time"><?php echo date('M d, Y', strtotime($activity['date'])); ?></span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                            <li class="activity-item">
                                <div class="activity-icon">📊</div>
                                <div class="activity-content">
                                    <p>Welcome to your dashboard! Explore programs and resources.</p>
                                    <span class="activity-time">Just now</span>
                                </div>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Gallery Section -->
            <div class="gallery-section">
                <h2 class="section-title">
                    <i class="fas fa-images" style="color: var(--primary);"></i>
                    Our Impact in Pictures
                </h2>
                
                <div class="gallery-container">
                    <div class="gallery-track" id="galleryTrack">
                        <?php foreach ($gallery_images as $image): ?>
                        <div class="gallery-item">
                            <img src="<?php echo htmlspecialchars($image['src']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['alt']); ?>"
                                 loading="lazy"
                                 onerror="this.src='https://via.placeholder.com/400x300/2d6a4f/ffffff?text=GUEF+Impact'">
                            <div class="gallery-caption">
                                <?php echo htmlspecialchars($image['caption']); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="gallery-nav" id="galleryNav"></div>
                </div>
            </div>

            <!-- Programs Section -->
            <div class="gallery-section">
                <h2 class="section-title">
                    <i class="fas fa-bullseye" style="color: var(--primary);"></i>
                    Our Focus Areas
                </h2>
                
                <div class="programs-grid">
                    <div class="program-card">
                        <h4>🌱 Sustainable Livelihoods</h4>
                        <p>Supporting agriculture, green technology, and community enterprises for lasting economic growth.</p>
                    </div>
                    <div class="program-card">
                        <h4>🎓 Skills Development</h4>
                        <p>Vocational training, financial literacy, and entrepreneurship programs to build capacity.</p>
                    </div>
                    <div class="program-card">
                        <h4>💼 MSME Support</h4>
                        <p>Grants, microfinance, and business incubation services for small enterprises.</p>
                    </div>
                    <div class="program-card">
                        <h4>🌍 Market Linkages</h4>
                        <p>Connecting local producers to regional and global markets for sustainable growth.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2 class="section-title">
                    <i class="fas fa-bolt" style="color: var(--primary);"></i>
                    Quick Actions
                </h2>
                
                <div class="action-buttons">
                    <a href="reports.php" class="action-btn primary">
                        <i class="fas fa-chart-line"></i> View Reports
                    </a>
                    <a href="#" class="action-btn" onclick="openEditProfileModal()">
                        <i class="fas fa-user-edit"></i> Update Profile
                    </a>
                    <a href="contact.php" class="action-btn">
                        <i class="fas fa-headset"></i> Contact Support
                    </a>
                    <a href="resources.php" class="action-btn">
                        <i class="fas fa-book"></i> Resources
                    </a>
                    <a href="events.php" class="action-btn">
                        <i class="fas fa-calendar-alt"></i> View Events
                    </a>
                </div>
            </div>
            
        </main>

        <!-- Footer -->
        <footer class="dashboard-footer">
            <p>© <?php echo date('Y'); ?> Great United Eastern Foundations | Empowering Communities for a Better Future</p>
        </footer>
        
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal" id="editProfileModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit" style="margin-right: 10px; color: var(--primary);"></i>Edit Profile</h3>
            <button class="modal-close" onclick="closeEditProfileModal()">&times;</button>
        </div>
        
        <form action="update_profile.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label>County</label>
                <input type="text" name="county" value="<?php echo htmlspecialchars($user['county']); ?>" required>
            </div>
            <div class="form-group">
                <label>Group Name</label>
                <input type="text" name="group_name" value="<?php echo htmlspecialchars($user['group_name']); ?>" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-secondary" onclick="closeEditProfileModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Mobile Menu Toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    }

    // Gallery Carousel
    const galleryTrack = document.getElementById('galleryTrack');
    const galleryNav = document.getElementById('galleryNav');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    if (galleryNav && galleryItems.length > 0) {
        galleryItems.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.className = 'nav-dot' + (index === 0 ? ' active' : '');
            dot.onclick = () => scrollToImage(index);
            galleryNav.appendChild(dot);
        });
    }
    
    const navDots = document.querySelectorAll('.nav-dot');
    
    function updateActiveDot() {
        if (!galleryTrack || galleryItems.length === 0) return;
        
        const scrollPosition = galleryTrack.scrollLeft;
        const itemWidth = galleryItems[0].offsetWidth + 20;
        const activeIndex = Math.round(scrollPosition / itemWidth);
        
        navDots.forEach((dot, index) => {
            dot.classList.toggle('active', index === activeIndex);
        });
    }
    
    window.scrollToImage = function(index) {
        if (!galleryTrack || !galleryItems[index]) return;
        
        const itemWidth = galleryItems[0].offsetWidth + 20;
        galleryTrack.scrollTo({
            left: index * itemWidth,
            behavior: 'smooth'
        });
    };
    
    if (galleryTrack) {
        galleryTrack.addEventListener('scroll', updateActiveDot);
        window.addEventListener('resize', updateActiveDot);
    }
    
    document.addEventListener('DOMContentLoaded', updateActiveDot);

    // Edit Profile Modal
    const modal = document.getElementById('editProfileModal');
    
    function openEditProfileModal() {
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeEditProfileModal() {
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    window.openEditProfileModal = openEditProfileModal;
    window.closeEditProfileModal = closeEditProfileModal;
    
    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeEditProfileModal();
            }
        });
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeEditProfileModal();
        }
    });
</script>

</body>
</html>