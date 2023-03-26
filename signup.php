<?php
session_start();
if(isset($_SESSION['signup_error'])) {
    echo "<script>alert('" . $_SESSION['signup_error'] . "');</script>";
    unset($_SESSION['signup_error']);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Sign Up - File Storage Website</title>
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
		small {
    color: #333333;
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
			color: #556270;
			display: block;
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
		form input:not(:last-child) {
			margin-bottom: 0.5rem;
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

<body>
<?php
    if(isset($_SESSION['signup_error'])) {
        $alert_type = ($_SESSION['signup_status'] == 'success') ? 'alert-success' : 'alert-danger';
        echo "<script type='text/jscript'>alert('" . $_SESSION['signup_error'] . "')</script>";
        unset($_SESSION['signup_error']);
        unset($_SESSION['signup_status']);
    }
?>

	<h1>Sign Up for the File Storage Website</h1>
	<form action="signup_process.php" method="post">
		<label for="username">Username:</label>
		<input type="text" id="username" name="username" placeholder="Enter your username" required>

		<label for="password">Password:</label>
<input type="password" id="password" name="password" placeholder="Enter your password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
<small>Password must be at least 8 characters long and include at least one uppercase letter and one number.</small>
		<label for="email">Email:</label>
		<input type="email" id="email" name="email" placeholder="Enter your email
" required>

		<button type="submit" name="signup">Sign Up</button>
	</form>
	<p>Already have an account? <a href="login.php">Login</a> now.</p>
</body>
</html>
