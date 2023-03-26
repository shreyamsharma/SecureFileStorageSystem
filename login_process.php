<?php
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// Database connection details
$host = 'localhost';
$user = 'root';
$pass = 'shreya16';
$db = 'db_connect';

$conn = mysqli_connect($host, $user, $pass, $db);
if(!$conn){
    die('Connection failed: ' . mysqli_connect_error());
}

// Prepare a statement with a parameter placeholder
$stmt = mysqli_prepare($conn, "SELECT id, password FROM users WHERE username = ?");

// Bind the username parameter and execute the statement
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

// Bind the password result to a variable
mysqli_stmt_bind_result($stmt,$id, $hashed_password);

if(mysqli_stmt_fetch($stmt)){
    if(password_verify($password, $hashed_password)){
        $_SESSION['id'] = $id; // fix here, $row should be $id
        $_SESSION['username'] = $username;
        header('Location: homepage.php');
        exit();
    }
    else{
        $_SESSION['login_error'] = 'Invalid username or password.';
        echo "<script type='text/javascript'>alert('Invalid username or password.')</script>";
        
        $_SESSION['login_status'] = 'error';
        header('Location: login.php');
        exit();
    }
}
else{
    $_SESSION['login_error'] = 'Invalid username or password.';
    echo "<script type='text/javascript'>alert('Invalid username or password.')</script>";
    $_SESSION['login_status'] = 'error';
    header('Location: login.php');
    exit();
}

// Clean up the statement and close the connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
