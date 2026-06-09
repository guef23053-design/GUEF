<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

$message = "";
$messageType = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message_text = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Here you would typically send an email or save to database
    // For now, we'll just show a success message
    
    $message = "Your message has been sent successfully! Our team will respond within 24-48 hours.";
    $messageType = "success";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support - GUEF</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 30px;
            width: 100%;
            flex: 1;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
        }

        .info-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 24px;
            height: fit-content;
        }

        .info-section h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .info-section p {
            opacity: 0.9;
            margin-bottom: 30px;
            line-height: 1.7;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 30px;
        }

        .contact-info-item i {
            font-size: 1.5rem;
            width: 30px;
        }

        .contact-info-item h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .contact-info-item p {
            margin-bottom: 0;
            opacity: 0.85;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 40px;
        }

        .social-link {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 1.2rem;
        }

        .social-link:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }

        .form-section {
            background: var(--white);
            padding: 40px 30px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .form-section h3 {
            font-size: 1.5rem;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }

        .form-section > p {
            color: var(--gray);
            margin-bottom: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
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

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(45, 106, 79, 0.3);
        }

        .faq-section {
            margin-top: 50px;
        }

        .faq-section h3 {
            font-size: 1.5rem;
            color: var(--primary-dark);
            margin-bottom: 30px;
            text-align: center;
        }

        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .faq-item {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .faq-item h4 {
            color: var(--primary-dark);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faq-item p {
            color: var(--gray);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .footer {
            background: var(--white);
            padding: 20px 30px;
            text-align: center;
            color: var(--gray);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
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
        <div class="contact-grid">
            
            <!-- Contact Information -->
            <div class="info-section">
                <h2>Get in Touch</h2>
                <p>Have questions about our programs or need assistance? We're here to help!</p>
                
                <div class="contact-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>Visit Us</h4>
                        <p>Nairobi, Kenya<br>Eastern Region Headquarters</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <h4>Call Us</h4>
                        <p>+254 700 000 000<br>Mon - Fri, 8:00 AM - 5:00 PM</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>Email Us</h4>
                        <p>info@guef.org<br>support@guef.org</p>
                    </div>
                </div>
                
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="form-section">
                <h3>Send us a Message</h3>
                <p>Fill out the form below and we'll get back to you shortly.</p>
                
                <?php if ($message != ""): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Subject</label>
                        <select name="subject" required>
                            <option value="">Select a topic</option>
                            <option value="general">General Inquiry</option>
                            <option value="programs">Program Information</option>
                            <option value="support">Technical Support</option>
                            <option value="partnership">Partnership Opportunities</option>
                            <option value="feedback">Feedback</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" placeholder="Please describe your inquiry in detail..." required></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h3>Frequently Asked Questions</h3>
            <div class="faq-grid">
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle" style="color: var(--primary);"></i> How do I join a program?</h4>
                    <p>Visit our programs page or contact your local GUEF office to learn about available programs and enrollment.</p>
                </div>
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle" style="color: var(--primary);"></i> What documents do I need?</h4>
                    <p>National ID, proof of residence, and group membership details are typically required for registration.</p>
                </div>
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle" style="color: var(--primary);"></i> Is there any cost?</h4>
                    <p>Most programs are subsidized. Contact us for specific program costs and available scholarships.</p>
                </div>
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle" style="color: var(--primary);"></i> How can I volunteer?</h4>
                    <p>We welcome volunteers! Visit our office or email volunteer@guef.org for opportunities.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> Great United Eastern Foundations | Empowering Communities for a Better Future</p>
    </footer>
</div>

</body>
</html>