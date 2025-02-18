<?php
session_start();
include 'config.php';

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    // Jika sudah login, redirect ke index.php
    header("Location: index.php");
    exit();
}

$login_msg = '';
$msg_class = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = trim($_POST['password']);

    // Validasi input (simple)
    if (empty($email) || empty($password)) {
        $login_msg = 'Email and Password are required';
        $msg_class = 'danger';
    } else {
        // Cek apakah email ada di database
        $checkQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->bind_param("s", $email); // Ganti username dengan email
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login sukses, set session dan arahkan ke dashboard
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email']; // Simpan email di session
                $_SESSION['username'] = $user['username']; // Simpan username di session
                header("Location: index.php"); // Redirect ke index.php setelah login berhasil
                exit();
            } else {
                $login_msg = 'Incorrect Password';
                $msg_class = 'danger';
            }
        } else {
            $login_msg = 'Email not found';
            $msg_class = 'failed';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRUD With PHP</title>
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
                    <h3>Login</h3>
                    <p>Login to your account to continue.</p>
                </div>
                <?php if($login_msg): ?>
                    <p class="status <?= $msg_class ?>"><?= $login_msg ?></p>
                <?php endif; ?>
                <form action="login.php" method="post">
                    <input type="email" name="email" placeholder="Email" value="<?= isset($email) ? $email : ''; ?>" required>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <button type="submit" class="btn login-btn" name="login">Login</button>
                </form>

                <!-- Register -->
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </section>
</body>
</html>
