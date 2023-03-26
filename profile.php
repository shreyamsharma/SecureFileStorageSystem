<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...


$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = 'shreya16';
$DATABASE_NAME = 'db_connect';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
$stmt = $con->prepare('SELECT password, email FROM users WHERE username = ?');
// In this case we can use the username to get the account info.
$stmt->bind_param('s', $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Profile Page</title>
	<link href="style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<style>
	body {
		font-family: Arial, sans-serif;
		background-color: #F5F5F5;

	}

	.navtop {
		background: linear-gradient(45deg, #2c3e50, #3498db);
		overflow: hidden;
		position: fixed;
		top: 0;
		width: 100%;
		z-index: 9999;
	}

	.navtop div {
		display: flex;
		justify-content: space-between;
		align-items: center;
		height: 80px;
		margin: 0 auto;
		max-width: 1000px;
		padding: 0 20px;
	}

	.navtop h1 {
		font-size: 28px;
		color: #fff;
		margin: 0;
		padding: 0;
		flex: 1;
	}

	.navtop a {
		font-size: 18px;
		color: #fff;
		text-decoration: none;
		padding: 0 20px;
		flex: 0;
	}

	.navtop a:hover {
		color: whitesmoke;
		background-color: lightslategray;
		height: 100%;
	}

	.navtop a i {
		padding-right: 5px;
	}

	.profile-content {
		padding-left: 200px;
		width: 100%;

		display: flex;
		justify-content: flex-end;
		margin-top: 20px;
		box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.1);
		margin: 125px 0;
		padding: 25px;
		background-color: #fff;
	}
</style>

<body class="loggedin">
	<nav class="navtop">
		<div>
			<h1 style="margin-left: 30px;">File Storage System</h1>
			<div style="display: flex; align-items: center; margin-right: 30px;">
				<a href="homepage.php"><i class="fas fa-home"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</div>
	</nav>
	<div class="profile-content">
		<div class="content">
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Username:</td>
						<td><?= $_SESSION['username'] ?></td>
					</tr>

					<tr>
						<td>Email:</td>
						<td><?= $email ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</body>

</html>