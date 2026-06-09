<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();

        } else {
            $message = "Incorrect password!";
        }

    } else {
        $message = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#2d6a4f">
    <title>Login - Great United Eastern Foundations</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* ===== RESET & BASE ===== */
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
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
            --error: #dc3545;
            --success: #28a745;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f9f4 0%, #e8f5e9 100%);
            padding: 20px;
        }

        /* ===== AUTH CONTAINER ===== */
        .auth-container {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }

        .auth-card {
            background: var(--white);
            padding: 40px 30px;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(45, 106, 79, 0.1);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== LOGO SECTION ===== */
        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-wrapper {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            padding: 16px 24px 20px 24px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(45, 106, 79, 0.25);
            transition: all 0.3s ease;
        }

        .logo-wrapper:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(45, 106, 79, 0.35);
        }

        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 16px;
            background: white;
            padding: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
            border: 3px solid var(--white);
        }

        .logo-text {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 2px;
            line-height: 1.2;
            text-transform: uppercase;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .org-name {
            text-align: center;
            margin-top: 15px;
        }

        .org-name h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .org-name p {
            font-size: 0.85rem;
            color: var(--gray);
            letter-spacing: 0.5px;
        }

        /* ===== FORM STYLES ===== */
        .auth-card h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 10px;
            text-align: center;
            position: relative;
        }

        .auth-card h2::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: var(--primary);
            margin: 10px auto 0;
            border-radius: 2px;
        }

        .auth-subtitle {
            text-align: center;
            color: var(--gray);
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .error-message {
            background: rgba(220, 53, 69, 0.1);
            color: var(--error);
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
            border-left: 4px solid var(--error);
        }

        .success-message {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
            border-left: 4px solid var(--success);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.1rem;
        }

        form input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s ease;
            background: var(--white);
            color: var(--dark);
        }

        form input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.1);
        }

        form input::placeholder {
            color: #aaa;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
            cursor: pointer;
        }

        .remember-me input {
            width: auto;
            padding: 0;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        form button {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 15px 25px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            margin-top: 10px;
        }

        form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(45, 106, 79, 0.3);
        }

        form button:active {
            transform: translateY(0);
        }

        .auth-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: var(--gray);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 20px;
            transition: color 0.3s ease;
        }

        .back-home:hover {
            color: var(--primary);
        }

        .back-home i {
            font-size: 0.9rem;
        }

        /* ===== DARK MODE SUPPORT ===== */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a2a22 0%, #15261c 100%);
            }
            
            .auth-card {
                background: #2d2d2d;
                border-color: #444;
            }
            
            .org-name h3 {
                color: #e0e0e0;
            }
            
            .auth-card h2 {
                color: #e0e0e0;
            }
            
            form input {
                background: #1e1e1e;
                border-color: #444;
                color: #e0e0e0;
            }
            
            form input::placeholder {
                color: #888;
            }
            
            .auth-footer {
                border-top-color: #444;
                color: #aaa;
            }
            
            .remember-me {
                color: #aaa;
            }
        }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 480px) {
            body {
                padding: 15px;
                align-items: flex-start;
            }
            
            .auth-card {
                padding: 30px 20px;
            }
            
            .logo-img {
                width: 70px;
                height: 70px;
            }
            
            .logo-text {
                font-size: 1.1rem;
            }
            
            .org-name h3 {
                font-size: 1.1rem;
            }
            
            .auth-card h2 {
                font-size: 1.5rem;
            }
            
            form input {
                padding: 13px 13px 13px 42px;
                font-size: 0.95rem;
            }
            
            form button {
                padding: 13px 20px;
                font-size: 1rem;
            }
            
            .form-options {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }

        @media (max-width: 360px) {
            .logo-wrapper {
                padding: 12px 18px 16px 18px;
            }
            
            .logo-img {
                width: 60px;
                height: 60px;
            }
            
            .logo-text {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>

<div class="auth-container">
    <div class="auth-card">

        <!-- Logo Section -->
        <div class="auth-logo">
            <div class="logo-wrapper">
                <img src="logo.jpg" alt="GUEF Logo" class="logo-img">
                <h2 class="logo-text">GUEF</h2>
            </div>
            <div class="org-name">
                <h3>Great United Eastern Foundations</h3>
                <p>Empowering Communities for a Better Future</p>
            </div>
        </div>

        <h2>Welcome Back</h2>
        <p class="auth-subtitle">Sign in to access your dashboard</p>

        <?php if($message != ""): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
            <div class="success-message">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                Registration successful! Please login.
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['timeout']) && $_GET['timeout'] == '1'): ?>
            <div class="error-message">
                <i class="fas fa-clock" style="margin-right: 8px;"></i>
                Session expired. Please login again.
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required autocomplete="username">
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" value="1">
                    <span>Remember me</span>
                </label>
                <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit">
                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                Login
            </button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Create Account</a></p>
        </div>

        <div style="text-align: center;">
            <a href="index.html" class="back-home">
                <i class="fas fa-arrow-left"></i>
                Back to Homepage
            </a>
        </div>

    </div>
</div>

<script>
    // Simple animation for input fields
    document.querySelectorAll('form input').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.querySelector('i').style.color = '#1b4332';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.querySelector('i').style.color = '#2d6a4f';
        });
    });

    // Auto-hide success/error messages after 5 seconds
    setTimeout(() => {
        const message = document.querySelector('.error-message, .success-message');
        if (message) {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(() => {
                if (message.parentElement) {
                    message.style.display = 'none';
                }
            }, 500);
        }
    }, 5000);
</script>

</body>
</html>