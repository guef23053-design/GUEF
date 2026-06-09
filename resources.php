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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources - GUEF</title>
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

        .header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
        }

        .logo h1 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .back-btn {
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            color: var(--primary);
            border: 2px solid var(--primary);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: var(--primary);
            color: white;
        }

        .content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 30px;
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

        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .resource-card {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: all 0.3s;
            border: 1px solid rgba(45, 106, 79, 0.1);
        }

        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(45, 106, 79, 0.12);
        }

        .resource-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .resource-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }

        .resource-card p {
            color: var(--gray);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(45, 106, 79, 0.1);
            color: var(--primary);
            text-decoration: none;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .download-btn:hover {
            background: var(--primary);
            color: white;
        }

        .video-section {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .video-section h3 {
            font-size: 1.3rem;
            color: var(--primary-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .video-card {
            background: var(--light);
            border-radius: 15px;
            overflow: hidden;
        }

        .video-thumbnail {
            height: 180px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .video-info {
            padding: 20px;
        }

        .video-info h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 8px;
        }

        .video-info p {
            color: var(--gray);
            font-size: 0.85rem;
        }

        .footer {
            background: var(--white);
            padding: 20px 30px;
            text-align: center;
            color: var(--gray);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .page-title h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    
    <header class="header">
        <div class="logo">
            <img src="logo.jpg" alt="GUEF" onerror="this.src='https://via.placeholder.com/45/2d6a4f/ffffff?text=G'">
            <h1>Great United Eastern Foundations</h1>
        </div>
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </header>

    <main class="content">
        <div class="page-title">
            <h2><i class="fas fa-book-open" style="margin-right: 15px; color: var(--primary);"></i>Resource Library</h2>
            <p>Access training materials, guides, and helpful resources for your journey.</p>
        </div>

        <!-- Document Resources -->
        <div class="resources-grid">
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h3>Program Guide 2026</h3>
                <p>Comprehensive guide to all GUEF programs, eligibility criteria, and application processes.</p>
                <a href="#" class="download-btn">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
            
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-file-excel"></i>
                </div>
                <h3>Business Plan Template</h3>
                <p>Professional business plan template for MSMEs seeking funding and support.</p>
                <a href="#" class="download-btn">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
            
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3>Financial Literacy Guide</h3>
                <p>Learn budgeting, saving, and financial management for your business and personal life.</p>
                <a href="#" class="download-btn">
                    <i class="fas fa-download"></i> Download Guide
                </a>
            </div>
            
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-file-powerpoint"></i>
                </div>
                <h3>Market Linkage Toolkit</h3>
                <p>Strategies and tools for connecting your products to local and global markets.</p>
                <a href="#" class="download-btn">
                    <i class="fas fa-download"></i> Download Toolkit
                </a>
            </div>
            
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h3>Grant Application Guide</h3>
                <p>Step-by-step guide to preparing successful grant applications for your projects.</p>
                <a href="#" class="download-btn">
                    <i class="fas fa-download"></i> Download Guide
                </a>
            </div>
            
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3>Sustainable Farming Manual</h3>
                <p>Best practices for sustainable agriculture and climate-smart farming techniques.</p>
                <a href="#" class="download-btn">
                    <i class="fas fa-download"></i> Download Manual
                </a>
            </div>
        </div>

        <!-- Video Resources -->
        <div class="video-section">
            <h3><i class="fas fa-video" style="color: var(--primary);"></i> Training Videos</h3>
            <div class="video-grid">
                <div class="video-card">
                    <div class="video-thumbnail">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="video-info">
                        <h4>Introduction to GUEF Programs</h4>
                        <p>Learn about our mission and available programs.</p>
                    </div>
                </div>
                
                <div class="video-card">
                    <div class="video-thumbnail">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="video-info">
                        <h4>How to Write a Business Plan</h4>
                        <p>Step-by-step tutorial for creating effective business plans.</p>
                    </div>
                </div>
                
                <div class="video-card">
                    <div class="video-thumbnail">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="video-info">
                        <h4>Financial Management Basics</h4>
                        <p>Essential financial skills for entrepreneurs.</p>
                    </div>
                </div>
                
                <div class="video-card">
                    <div class="video-thumbnail">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="video-info">
                        <h4>Marketing Strategies for MSMEs</h4>
                        <p>Effective marketing techniques for small businesses.</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($is_admin): ?>
        <!-- Admin Only Section -->
        <div class="video-section">
            <h3><i class="fas fa-shield-alt" style="color: var(--primary);"></i> Admin Resources</h3>
            <div class="resources-grid" style="grid-template-columns: repeat(2, 1fr);">
                <div class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3>User Management Guide</h3>
                    <p>Guidelines for managing user accounts and permissions.</p>
                    <a href="#" class="download-btn">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
                <div class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Reporting Templates</h3>
                    <p>Standard templates for monthly and quarterly reports.</p>
                    <a href="#" class="download-btn">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> Great United Eastern Foundations | Empowering Communities for a Better Future</p>
    </footer>
</div>

</body>
</html>