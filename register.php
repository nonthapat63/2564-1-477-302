<?php
		//2.save regist info into database
		//2.1 insert user data into databass
		include_once "dbconnect.php";
		
		//check if from or submit button is submittesd
		if (isset($_POST['signup'])) {
			//get data into variables
			$name = $_POST ('user-name');
			$email = $_POST ('user-email');
			$passwd = $_POST ('user-password');
			$cpasswd = $_POST ('user-cpasswd');

			//2.2 validate user data
			//set validation error flag as flase
			$validate_error = false;
			//validation error message
			$validate_msg = "";

			//validate e-mail format
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$validate_error = true;
				$validate_msg = "E-mail is not correct.";
			}
			//validate length of password
			if (strlen($passwd) < 6) {
				$validate_error = true;
				$validate_msg = "Password must be more than 6 characters.";
			}

			//validate password & confirm password
			if ($passwd != $cpasswd) {
				$validate_error = true;
				$validate_msg = "Password and confirm password do not match.";
			}

			if (!$validate_error) {
				//insert into users table
				$sql = "INSERT INTO users(user_name, user_email, user_passwd) 
				VALUES ('" . $name . "', '" . $email . "', '" . $passwd . "')";
			
				mysqli_query($con, $sql);
				header("location: login.php");
			}		
		}



?>

<!DOCTYPE html>
<html>
<head>
	<title>User Registration</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" >
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
</head>
<body>

<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<!-- add header -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php">PHP Simple CRUD</a>
		</div>
		<!-- menu items -->
		<div class="collapse navbar-collapse" id="navbar1">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="login.php">Login</a></li>
				<li class="active"><a href="register.php">Sign Up</a></li>
				<li><a href="admin_login.php">Admin</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4 well">
			<form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="signupform">
				<fieldset>
					<legend>Sign Up</legend>

					<div class="form-group">
						<label for="name">Name</label>
						<input type="text" name="name" placeholder="Enter Full Name" required value="" class="form-control" />
					</div>

					<div class="form-group">
						<label for="name">Email</label>
						<input type="text" name="email" placeholder="Email" required value="" class="form-control" />
					</div>

					<div class="form-group">
						<label for="name">Password</label>
						<input type="password" name="password" placeholder="Password" required class="form-control" />
					</div>

					<div class="form-group">
						<label for="name">Confirm Password</label>
						<input type="password" name="cpassword" placeholder="Confirm Password" required class="form-control" />
					</div>

					<div class="form-group">
						<input type="submit" name="signup" value="Sign Up" class="btn btn-primary" />
					</div>
				</fieldset>
			</form>
			<!--3.display message -->
			<?php
				if (isset($validate_error)) {
					if ($validate_error) {
						echo $validate_msg;
					}	
				}
			?> 
				
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4 text-center">
		Already Registered? <a href="login.php">Login Here</a>
		</div>
	</div>
</div>
</body>
</html>
