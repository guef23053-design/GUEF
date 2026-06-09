<?php
include 'db.php';

$message = "";
$messageType = "error";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $national_id = mysqli_real_escape_string($conn, $_POST['national_id']);
    $county = mysqli_real_escape_string($conn, $_POST['county']);
    $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if (mysqli_num_rows($check) > 0) {
        $message = "Username already exists! Please choose a different username.";
        $messageType = "error";
    } else {
        // Check if email already exists
        $checkEmail = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            $message = "Email already registered! Please use a different email or login.";
            $messageType = "error";
        } else {
            $sql = "INSERT INTO users (full_name, email, phone, national_id, county, group_name, username, password, role, created_at)
                    VALUES ('$full_name','$email','$phone','$national_id','$county','$group_name','$username','$password', 'user', NOW())";

            if (mysqli_query($conn, $sql)) {
                header("Location: login.php?registered=success");
                exit();
            } else {
                $message = "Error: " . mysqli_error($conn);
                $messageType = "error";
            }
        }
    }
}

// Kenyan counties array for dropdown
$counties = [
    "Baringo", "Bomet", "Bungoma", "Busia", "Elgeyo Marakwet", "Embu", "Garissa", 
    "Homa Bay", "Isiolo", "Kajiado", "Kakamega", "Kericho", "Kiambu", "Kilifi", 
    "Kirinyaga", "Kisii", "Kisumu", "Kitui", "Kwale", "Laikipia", "Lamu", "Machakos", 
    "Makueni", "Mandera", "Marsabit", "Meru", "Migori", "Mombasa", "Murang'a", 
    "Nairobi", "Nakuru", "Nandi", "Narok", "Nyamira", "Nyandarua", "Nyeri", 
    "Samburu", "Siaya", "Taita Taveta", "Tana River", "Tharaka Nithi", "Trans Nzoia", 
    "Turkana", "Uasin Gishu", "Vihiga", "Wajir", "West Pokot"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#2d6a4f">
    <title>Register - Great United Eastern Foundations</title>

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
            --warning: #ffc107;
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
            max-width: 550px;
            margin: 0 auto;
        }

        .auth-card {
            background: var(--white);
            padding: 40px 30px;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(45, 106, 79, 0.1);
            animation: fadeInUp 0.6s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .auth-card::-webkit-scrollbar {
            width: 6px;
        }

        .auth-card::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .auth-card::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
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
            margin-bottom: 25px;
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
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 16px;
            background: white;
            padding: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
            border: 3px solid var(--white);
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 2px;
            line-height: 1.2;
            text-transform: uppercase;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .org-name {
            text-align: center;
            margin-top: 12px;
        }

        .org-name h3 {
            font-size: 1.2rem;
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
            gap: 15px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
            font-size: 1rem;
            z-index: 1;
        }

        form input,
        form select {
            width: 100%;
            padding: 14px 15px 14px 42px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s ease;
            background: var(--white);
            color: var(--dark);
        }

        form select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%232d6a4f' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        form input:focus,
        form select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.1);
        }

        form input::placeholder {
            color: #aaa;
        }

        .password-strength {
            margin-top: -8px;
            padding-left: 5px;
        }

        .strength-bar {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background 0.3s ease;
            border-radius: 2px;
        }

        .strength-text {
            font-size: 0.8rem;
            color: var(--gray);
            margin-top: 3px;
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 10px 0;
        }

        .terms-checkbox input {
            width: auto;
            margin-top: 3px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .terms-checkbox label {
            font-size: 0.9rem;
            color: var(--gray);
            cursor: pointer;
        }

        .terms-checkbox a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .terms-checkbox a:hover {
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

        form button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(45, 106, 79, 0.3);
        }

        form button:active:not(:disabled) {
            transform: translateY(0);
        }

        form button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
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
            
            form input,
            form select {
                background: #1e1e1e;
                border-color: #444;
                color: #e0e0e0;
            }
            
            form input::placeholder {
                color: #888;
            }
            
            form select option {
                background: #2d2d2d;
            }
            
            .auth-footer {
                border-top-color: #444;
                color: #aaa;
            }
            
            .strength-bar {
                background: #444;
            }
        }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 600px) {
            body {
                padding: 15px;
                align-items: flex-start;
            }
            
            .auth-card {
                padding: 30px 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .logo-img {
                width: 60px;
                height: 60px;
            }
            
            .logo-text {
                font-size: 1rem;
            }
            
            .org-name h3 {
                font-size: 1rem;
            }
            
            .auth-card h2 {
                font-size: 1.5rem;
            }
            
            form input,
            form select {
                padding: 12px 12px 12px 40px;
                font-size: 0.9rem;
            }
            
            form button {
                padding: 13px 20px;
                font-size: 1rem;
            }
        }

        @media (max-width: 360px) {
            .logo-wrapper {
                padding: 12px 18px 16px 18px;
            }
            
            .logo-img {
                width: 55px;
                height: 55px;
            }
            
            .logo-text {
                font-size: 0.9rem;
            }
        }

        /* Required field indicator */
        .required::after {
            content: " *";
            color: var(--error);
            font-weight: bold;
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

        <h2>Create Account</h2>
        <p class="auth-subtitle">Join us in empowering communities</p>

        <?php if($message != ""): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="full_name" placeholder="Full Name" required autocomplete="name">
            </div>

            <div class="form-row">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email Address" required autocomplete="email">
                </div>

                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" placeholder="Phone Number" required autocomplete="tel">
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="national_id" placeholder="National ID" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-users"></i>
                    <input type="text" name="group_name" placeholder="Group Name">
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <select name="county" required>
                    <option value="">Select Your County</option>
                    <?php foreach($counties as $county): ?>
                        <option value="<?php echo $county; ?>"><?php echo $county; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" name="username" id="username" placeholder="Username" required autocomplete="username">
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password" required autocomplete="new-password">
                </div>
            </div>

            <!-- Password Strength Indicator -->
            <div class="password-strength" id="passwordStrength">
                <div class="strength-bar">
                    <div class="strength-fill" id="strengthFill"></div>
                </div>
                <div class="strength-text" id="strengthText">Enter a password</div>
            </div>

            <!-- Terms and Conditions -->
            <div class="terms-checkbox">
                <input type="checkbox" id="termsCheckbox" required>
                <label for="termsCheckbox">
                    I agree to the <a href="terms.php">Terms & Conditions</a> and 
                    <a href="privacy.php">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" id="submitBtn">
                <i class="fas fa-user-plus" style="margin-right: 8px;"></i>
                Create Account
            </button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
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
    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            
            const percentage = (strength / 5) * 100;
            strengthFill.style.width = percentage + '%';
            
            if (strength <= 2) {
                strengthFill.style.background = '#dc3545';
                strengthText.textContent = 'Weak password';
                strengthText.style.color = '#dc3545';
            } else if (strength <= 3) {
                strengthFill.style.background = '#ffc107';
                strengthText.textContent = 'Fair password';
                strengthText.style.color = '#ffc107';
            } else if (strength <= 4) {
                strengthFill.style.background = '#28a745';
                strengthText.textContent = 'Good password';
                strengthText.style.color = '#28a745';
            } else {
                strengthFill.style.background = '#2d6a4f';
                strengthText.textContent = 'Strong password';
                strengthText.style.color = '#2d6a4f';
            }
            
            if (password.length === 0) {
                strengthFill.style.width = '0%';
                strengthText.textContent = 'Enter a password';
                strengthText.style.color = '#6c757d';
            }
        });
    }

    // Input focus effects
    document.querySelectorAll('form input, form select').forEach(input => {
        input.addEventListener('focus', function() {
            const icon = this.parentElement.querySelector('i');
            if (icon) icon.style.color = '#1b4332';
        });
        
        input.addEventListener('blur', function() {
            const icon = this.parentElement.querySelector('i');
            if (icon) icon.style.color = '#2d6a4f';
        });
    });

    // Phone number validation (Kenyan format)
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 10) value = value.slice(0, 10);
            
            if (value.length >= 9) {
                if (value.startsWith('0')) {
                    value = value.slice(1);
                }
                if (value.length === 9 && !value.startsWith('7')) {
                    value = '7' + value.slice(0, 8);
                }
            }
            
            if (value.length > 3) {
                this.value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
            } else {
                this.value = value;
            }
        });
    }

    // National ID validation (Kenyan format)
    const idInput = document.querySelector('input[name="national_id"]');
    if (idInput) {
        idInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            this.value = value;
        });
    }

    // Auto-hide error messages after 8 seconds
    setTimeout(() => {
        const message = document.querySelector('.error-message');
        if (message) {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(() => {
                if (message.parentElement) {
                    message.style.display = 'none';
                }
            }, 500);
        }
    }, 8000);

    // Prevent form submission if terms not accepted
    const form = document.getElementById('registerForm');
    const termsCheckbox = document.getElementById('termsCheckbox');
    
    if (form && termsCheckbox) {
        form.addEventListener('submit', function(e) {
            if (!termsCheckbox.checked) {
                e.preventDefault();
                alert('Please agree to the Terms & Conditions and Privacy Policy.');
            }
        });
    }
</script>

</body>
</html>