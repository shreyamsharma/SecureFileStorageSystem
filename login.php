<?php
session_start();
if(isset($_SESSION['login_error'])) {
    echo "<script>alert('" . $_SESSION['login_error'] . "');</script>";
    unset($_SESSION['login_error']);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login - File Storage Website</title>
	<style>
		body {
			background: linear-gradient(45deg, #2c3e50, #3498db);
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			height: 100vh;
			margin: 0;
			padding: 0;
			color: #fff;
			font-family: sans-serif;
		}
		h1 {
			font-size: 2rem;
			margin: 0 0 1rem;
			text-align: center;
		}
		form {
			display: flex;
			flex-direction: column;
			align-items: flex-start;
			justify-content: center;
			background: #EAEAEA;
			padding: 2rem;
			border-radius: 1rem;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
			width: 50%;
			max-width: 500px;
			margin: 2rem 0;
		}
		form label {
			font-size: 1.2rem;
			display: block;
			color: #556270;
			
			margin: 1rem 0;
		}
		form input {
			padding: 0.5rem 1rem;
			font-size: 1.2rem;
			border: none;
			border-radius: 0.5rem;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
			margin-bottom: 1rem;
			width: 100%;
			box-sizing: border-box;
		}
		form input:first-child {
			margin-top: 2rem;
		}
		button[type="submit"] {
			background: #556270;
			color: #fff;
			padding: 0.5rem 1rem;
			font-size: 1.2rem;
			border: none;
			border-radius: 0.5rem;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
			cursor: pointer;
			transition: background-color 0.2s ease;
			margin-bottom: 1rem;
		}
		button[type="submit"]:hover {
			background: #3498db;
		}
		p {
			font-size: 1.2rem;
			text-align: center;
			margin-top: 1rem;
		}
		a {
			color: #fff;
			text-decoration: none;
			font-weight: bold;
			transition: color 0.2s ease;
		}
		a:hover {
			color: #3498db;
		}
	</style>
</head>
<body>

<?php

    if(isset($_SESSION['login_error'])) {
		$alert_type = ($_SESSION['login_status'] == 'success') ? 'alert-success' : 'alert-danger';
		echo "<div class='alert alert-danger'>" . $_SESSION['login_error'] . "</div>";
        unset($_SESSION['login_error']);
        unset($_SESSION['login_status']);
    }
?>




	<h1>Login to the File Storage Website</h1>
	<form action="login_process.php" method="post">
		<label for="username">Username:</label>
		<input type="text" id="username" name="username" placeholder="Enter your username" required>

		<label for="password">Password:</label>
		<input type="password" id="password" name="password" placeholder="Enter your password" required>

		<button type="submit" name="login">Login</button>
	</form>
	<p>Don't have an account? <a href="signup.php">Sign up</a> now.</p>


</body>
</html>
