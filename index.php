
<?php 	
	//Connect to the database
	mysql_connect("localhost", "root", "test123") or die("Could not connect to database");
	mysql_select_db("login") or die("Couldnt find database");

	//Select all users from database
	$query = mysql_query("SELECT * FROM users");

	//Check if anything is retrieved from database
	$numrows = mysql_num_rows($query);

	if($numrows!=0){
		$userArray = array();
		while ($row = mysql_fetch_assoc($query)) { 		//Store all database info into $row array
			$userArray[] = $row['username']; 			//Get all usernames from the row array and fill inside $userArray
		}
	}	 

	//Registration form validation
	$submit = $_POST['submit'];							//When user presses Register button
	$userName = strip_tags($_POST['userName']);			//When user enters User Name
	$password = $_POST['password'];						//When user enters password
	$confirmPassword= $_POST['confirmPassword'];		//When user confirms password
	$date = date("Y-m-d");								//Set the date format
	$error = array();									//Create errors array to store error messages

	//When user completes the Registration and submits the form
	if($submit){
		//Check for existance
		if($userName&&$password&&$confirmPassword){
			
				//Check char length of user name
				if(strlen($userName)>25||strlen($userName)<4){
					$error['userName'] = "Length of username must be between 4 and 25 characters long";
				}
				//Check if $username is already present inside the database. This is done by using $userArray (which contains all usernames, see lines 14-16)
				if(in_array($userName, $userArray)){
					$error['uniqueUser'] = "This user is already taken!";
				}
				//Check password length
				if (strlen($password)>25||strlen($password)<6) {
					$error['password'] = "Password must be between 6 and 25 characters long";
				}
				//Check if passwords match
				if ($password!=$confirmPassword) {
					$error['confirmPassword'] = "The passwords do not match";
				}
				//If there are no errors, then carry on with registration
				if	(empty($error))
				{
					//Encrypt the password
					$password = sha1($password);
					$confirmPassword = sha1($confirmPassword);
					
					//Register the user	
					$queryreg = mysql_query("
							INSERT INTO users VALUES ('', '$userName', '$password', '$date')
						");
					//Thank you for registering page
					header('location: thankyou.php');
				}
		}			
		else
			$error['fillFields'] = "Please fill in <b>all</b> the fields!";
	}

	//Login Form Validation
	session_start();

	$submitLogin = $_POST['submitLogin'];		//When user clicks Log In
	$username = $_POST['username'];				//When user enters username
	$password = $_POST['password'];				//When user enters password
	
	//When user clicks Log In
	if ($submitLogin){
		//Check if username is empty
		if (empty($username)){
			$errors['loginName'] = "This field cannot be empty";
		}
		//Check if password is empty
		if (empty($password)){
			$errors['loginPass'] = "This field cannot be empty";
		}
		//If username and password have been entered
		if($username&&$password){

			//Query username from the database
			$query = mysql_query("SELECT * FROM users WHERE username='$username'");
			$numrows = mysql_num_rows($query);

			if($numrows!=0){
				
				while ($row = mysql_fetch_assoc($query)) {
					$dbuserID = $row['id'];						//Get Id from database
					$dbusername = $row['username'];				//Get username from database
					$dbpassword = $row['password'];				//Get password from database
				}
				
				//Check to see if they match
				if ($username==$dbusername&&sha1($password)==$dbpassword) {
					$_SESSION['username']=$dbusername;			//Pass into session variable if username is correct
					$_SESSION['id']=$dbuserID;					//Pass into session variable if Id is correct
					header('location: guestlog.php');			//Go to guest log if log in is successful
						exit;
					}else{
						$errors['loginMissmatch'] = "Details are not correct.";		
					}
			}else
				$errors['noUser'] = "Please enter a valid username and password";
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Guest Book Log</title>
	<meta name="author" content="Ali A. Kingravi" />
	<meta name="description" content="Guest Book Log to display guest messages" />
	<meta name="keywords"  content="fullpage,jquery,ali,kingravi,plugin,fullscren,screen,full,iphone5,apple,pure,javascript,slider,php" />
	<meta name="Resource-type" content="Document" />

	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="css/guestStyles.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css" media="all">
	<link rel="stylesheet" type="text/css" href="css/unsemantic-grid-responsive-tablet.css">
	
	<!-- Add Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Playball' rel='stylesheet' type='text/css'>
</head>

<body>
<section id="login-page">
	<div class="login-header">
		
		<img src="images/logo.png">

		<div class="login-box">
			<div class="grid-container">

				<div class="grid-75 tablet-grid-50 mobile-100">
					<h3>Welcome</h3>
					<p>Please register to post a message to my guest book.</p>
					<p>You can view your message once it is posted.</p>
					<p>If you do not wish to register, then please use the following login details:</p>
					<ul>
						<li>Username: Guest001</li>
						<li>Password: guestuser</li>
					</ul>
				</div>

				<div class="grid-25 tablet-grid-50 mobile-100">
					<h3>Login</h3>
					<?php if(isset($errors['loginMissmatch'])){echo '<span class="error">'.$errors['loginMissmatch'].'</span>';} ?>
					<?php if(isset($errors['noUser'])){echo '<span class="error">'.$errors['noUser'].'</span>';} ?>
					<form action="index.php" method="post">
					  <div class="form-group">
					    <input type="text" class="form-control" name="username" placeholder="User Name">
					    <?php if(isset($errors['loginName'])){echo '<span class="error">'.$errors['loginName'].'</span>';} ?>
					  </div>
					  <div class="form-group">
					    <input type="password" class="form-control" name="password" placeholder="Password">
					    <?php if(isset($errors['loginPass'])){echo '<span class="error">'.$errors['loginPass'].'</span>';} ?>
					  </div>

					  <input type="submit" name="submitLogin" class="btn btn-primary" value="Log In">

					</form>
				</div>

			</div>
		</div><!-- END Login Box -->


			
		<div class="register-box">
			<div class="grid-container">
				<div class="grid-75 tablet-grid-75 mobile-grid-100">
					<h3>Registration</h3>
					<?php if(isset($error['fillFields'])){echo '<span class="error">'.$error['fillFields'].'</span>';} ?>
					<form action="index.php" method="post">

					  <div class="form-group">
					    <label for="userName">User Name</label>
					    <input type="text" class="form-control" name="userName" placeholder="Enter a user name of your choice" value="<?php echo "$userName" ?>">
					    <?php if(isset($error['uniqueUser'])){echo '<span class="error">'.$error['uniqueUser'].'</span>';} ?>
					    <?php if(isset($error['userName'])){echo '<span class="error">'.$error['userName'].'</span>';} ?>
					  </div>
					
					<div class="form-group">
					  <label for="exampleInputPassword1">Password</label>
					  <input type="password" class="form-control" name="password" placeholder="Must be at least 6 characters long">
					  <?php if(isset($error['password'])){echo '<span class="error">'.$error['password'].'</span>';} ?>
					</div>
				
					<div class="form-group">
					    <label for="exampleInputPassword1">Confirm Password</label>
					    <input type="password" class="form-control" name="confirmPassword" placeholder="Must match your password">
					    <?php if(isset($error['confirmPassword'])){echo '<span class="error">'.$error['confirmPassword'].'</span>';} ?>
					  </div>
					
						<input type="submit" name="submit" class="btn btn-success" value="Register">
					
					</form>
				</div>		
			</div>	
		</div>

	</div><!-- END Login Header -->
</section><!-- END Login Page -->

	
<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/guestScripts.js"></script>

</body>
</html>