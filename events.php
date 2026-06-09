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

// Sample events data - you can replace with database query
$upcoming_events = [
    [
        'title' => 'Sustainable Farming Workshop',
        'date' => 'May 15, 2026',
        'time' => '9:00 AM - 3:00 PM',
        'location' => 'Kiambu County',
        'description' => 'Learn climate-smart agriculture techniques from expert trainers.',
        'icon' => '🌱',
        'spots' => 45
    ],
    [
        'title' => 'Financial Literacy Training',
        'date' => 'May 22, 2026',
        'time' => '10:00 AM - 1:00 PM',
        'location' => 'Nairobi',
        'description' => 'Master budgeting, saving, and investment strategies for your business.',
        'icon' => '💰',
        'spots' => 30
    ],
    [
        'title' => 'MSME Networking Event',
        'date' => 'June 5, 2026',
        'time' => '2:00 PM - 6:00 PM',
        'location' => 'Kisumu',
        'description' => 'Connect with other entrepreneurs and explore partnership opportunities.',
        'icon' => '🤝',
        'spots' => 60
    ],
    [
        'title' => 'Green Technology Expo',
        'date' => 'June 18, 2026',
        'time' => '9:00 AM - 5:00 PM',
        'location' => 'Nakuru',
        'description' => 'Discover innovative green technologies for sustainable development.',
        'icon' => '🔋',
        'spots' => 100
    ]
];

$past_events = [
    [
        'title' => 'Women in Business Summit',
        'date' => 'April 10, 2026',
        'location' => 'Mombasa',
        'attendees' => 120,
        'icon' => '👩‍💼'
    ],
    [
        'title' => 'Youth Skills Bootcamp',
        'date' => 'March 25, 2026',
        'location' => 'Eldoret',
        'attendees' => 85,
        'icon' => '🎓'
    ],
    [
        'title' => 'Market Access Forum',
        'date' => 'March 5, 2026',
        'location' => 'Nairobi',
        'attendees' => 150,
        'icon' => '🌍'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - GUEF</title>
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
            --warning: #f39c12;
            --danger: #e74c3c;
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

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .event-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: all 0.3s;
            border: 1px solid rgba(45, 106, 79, 0.1);
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(45, 106, 79, 0.12);
        }

        .event-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .event-icon {
            font-size: 2.5rem;
        }

        .event-title h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .event-title .event-date {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .event-body {
            padding: 25px;
        }

        .event-detail {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .event-detail i {
            width: 20px;
            color: var(--primary);
        }

        .event-description {
            color: var(--dark);
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 20px 0;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .spots-badge {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .register-btn {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 30px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .register-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 106, 79, 0.3);
        }

        .register-btn.disabled {
            background: var(--gray);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .past-events-section {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .past-events-section h3 {
            font-size: 1.3rem;
            color: var(--primary-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .past-events-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }

        .past-event-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: var(--light);
            border-radius: 12px;
        }

        .past-event-icon {
            width: 50px;
            height: 50px;
            background: rgba(45, 106, 79, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
        }

        .past-event-info h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 3px;
        }

        .past-event-info p {
            font-size: 0.8rem;
            color: var(--gray);
        }

        .calendar-section {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .calendar-section h3 {
            font-size: 1.3rem;
            color: var(--primary-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .calendar-placeholder {
            text-align: center;
            padding: 40px;
            background: var(--light);
            border-radius: 15px;
            color: var(--gray);
        }

        .calendar-placeholder i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
            opacity: 0.5;
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
            
            .events-grid {
                grid-template-columns: 1fr;
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
            <h2><i class="fas fa-calendar-alt" style="margin-right: 15px; color: var(--primary);"></i>Upcoming Events</h2>
            <p>Join our workshops, trainings, and networking events across Eastern Kenya.</p>
        </div>

        <!-- Upcoming Events -->
        <div class="events-grid">
            <?php foreach ($upcoming_events as $event): ?>
            <div class="event-card">
                <div class="event-header">
                    <div class="event-icon"><?php echo $event['icon']; ?></div>
                    <div class="event-title">
                        <h3><?php echo $event['title']; ?></h3>
                        <span class="event-date"><?php echo $event['date']; ?></span>
                    </div>
                </div>
                <div class="event-body">
                    <div class="event-detail">
                        <i class="fas fa-clock"></i>
                        <span><?php echo $event['time']; ?></span>
                    </div>
                    <div class="event-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo $event['location']; ?></span>
                    </div>
                    <div class="event-description">
                        <?php echo $event['description']; ?>
                    </div>
                    <div class="event-footer">
                        <span class="spots-badge">
                            <i class="fas fa-users"></i> <?php echo $event['spots']; ?> spots left
                        </span>
                        <a href="#" class="register-btn" onclick="registerEvent('<?php echo $event['title']; ?>')">
                            <i class="fas fa-ticket-alt"></i> Register
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Past Events -->
        <div class="past-events-section">
            <h3><i class="fas fa-history" style="color: var(--primary);"></i> Past Events</h3>
            <div class="past-events-list">
                <?php foreach ($past_events as $event): ?>
                <div class="past-event-item">
                    <div class="past-event-icon"><?php echo $event['icon']; ?></div>
                    <div class="past-event-info">
                        <h4><?php echo $event['title']; ?></h4>
                        <p><i class="fas fa-calendar"></i> <?php echo $event['date']; ?> | 
                           <i class="fas fa-map-marker-alt"></i> <?php echo $event['location']; ?> | 
                           <i class="fas fa-user"></i> <?php echo $event['attendees']; ?> attendees</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Calendar Overview -->
        <div class="calendar-section">
            <h3><i class="fas fa-calendar" style="color: var(--primary);"></i> Event Calendar</h3>
            <div class="calendar-placeholder">
                <i class="far fa-calendar-alt"></i>
                <p>Interactive calendar coming soon!</p>
                <p style="font-size: 0.85rem; margin-top: 10px;">Stay tuned for our full event calendar feature.</p>
            </div>
        </div>

        <?php if ($is_admin): ?>
        <!-- Admin Actions -->
        <div class="calendar-section" style="margin-top: 30px;">
            <h3><i class="fas fa-plus-circle" style="color: var(--primary);"></i> Admin Actions</h3>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="#" class="register-btn">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
                <a href="#" class="register-btn" style="background: var(--gray);">
                    <i class="fas fa-edit"></i> Manage Events
                </a>
                <a href="#" class="register-btn" style="background: var(--info);">
                    <i class="fas fa-download"></i> Export Attendee List
                </a>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> Great United Eastern Foundations | Empowering Communities for a Better Future</p>
    </footer>
</div>

<script>
    function registerEvent(eventName) {
        alert('Thank you for your interest in "' + eventName + '"!\n\nRegistration functionality will be available soon. Please contact support@guef.org for immediate assistance.');
    }
</script>

</body>
</html>