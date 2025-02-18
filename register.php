<?php
session_start();
include 'config.php';

$register_msg = '';
$msg_class = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input (simple)
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: register.php");
        exit();
    }

    // Cek apakah email/username sudah terdaftar
    $checkQuery = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $db->prepare($checkQuery);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'Username or Email already exists!';
        header("Location: register.php");
        exit();
    }

    // Hash password biar aman
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert data ke database
    $insertQuery = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $db->prepare($insertQuery);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Account created successfully!, Please log in';
        header("Location: register.php");
        exit();
    } else {
        $_SESSION['error'] = "Something's wrong, please try again.";
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CRUD With PHP</title>
    <link rel="stylesheet" href="css/login.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- Remix Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css" integrity="sha512-kJlvECunwXftkPwyvHbclArO8wszgBGisiLeuDFwNM8ws+wKIw0sv1os3ClWZOcrEB2eRXULYUsm8OVRGJKwGA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <section class="login">
        <div class="login-container">
            <div class="login-img">
                <img src="img/login.jpg" alt="">
            </div>
            <!-- Login Form -->
            <div class="login-form">
                <div class="login-header">
                    <h3>Register</h3>
                    <p>This mini project is only for training and practicing purposes.</p>
                </div>
                <?php if(isset($_SESSION['error'])): ?>
                    <p class="status danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                <?php endif; ?>
                <?php if(isset($_SESSION['success'])): ?>
                    <p class="status success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
                <?php endif; ?>
                <form action="register.php" method="post">
                    <input type="text" name="username" placeholder="Username">
                    <input type="email" name="email" placeholder="Email">
                    <input type="password" name="password" id="password" placeholder="Password">
                    <button type="submit" class="btn login-btn" name="register">Create Account</button>
                </form>

                <!-- Register -->
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </section>
</body>
</html>
