<?php
session_start();

// Check if the form was submitted
if(isset($_POST['signup'])) {

    // Get the form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Check if password meets complexity requirements
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
        $_SESSION['signup_error'] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number.';
        $_SESSION['signup_status'] = 'error';
        header('Location: signup.php');
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Database connection details
    $host = 'localhost';
    $user = 'root';
    $pass = 'shreya16';
    $db = 'db_connect';

    $conn = mysqli_connect($host, $user, $pass, $db);
    if(!$conn) {
        die('Connection failed: ' . mysqli_connect_error());
    }

    $check_email = "SELECT * FROM users WHERE email='$email'";
    $result_email = mysqli_query($conn, $check_email);

    if(mysqli_num_rows($result_email) == 0) {
        $insert_user = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password_hash')";
        if(mysqli_query($conn, $insert_user)) {
            $_SESSION['username'] = $username;
            $_SESSION['signup_status'] = 'success';
            header('Location: homepage.php');
            exit();
        }
        else {
            $_SESSION['signup_error'] = 'Error: ' . mysqli_error($conn);
            $_SESSION['signup_status'] = 'error';
            header('Location: signup.php');
            exit();
        }
    }
    else {
        $_SESSION['signup_error'] = 'Email already exists.';
        $_SESSION['signup_status'] = 'error';
        header('Location: signup.php');
        exit();
    }

    mysqli_close($conn);
}

if(isset($_SESSION['signup_error'])) {
    $alert_type = ($_SESSION['signup_status'] == 'success') ? 'alert-success' : 'alert-danger';
    echo "<script type='text/javascript'>alert('" . $_SESSION['signup_error'] . "')</script>";
    unset($_SESSION['signup_error']);
    unset($_SESSION['signup_status']);
}
?>
