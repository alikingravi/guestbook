<?php
	session_start();

	//Store Name of member in $memberName
	$memberName = $_SESSION['username'];

	//If user has logged in successfully
	if($_SESSION['username']){
		$memberName = ucfirst($memberName); //Capitalise first letter
	}
	else
		//Display woops error page
		header('location: woops.php');

	//Submit messages

	//Connect to the database and messages table
	mysql_connect("localhost", "root", "test123") or die("Could not connect to databse");
	mysql_select_db("login") or die("Could not find database");

	//Select everything from messages
	$query = mysql_query("SELECT * FROM messages");

	//Check if anything is retrieved
	$numrows = mysql_num_rows($query);
	if($numrows!=0){
		$databaseContent = array();						//Create new array 
		$c=0;											//Count items in database
		while ($rows = mysql_fetch_assoc($query)) {		//Get content from database 
			$databaseContent[] = $rows;					//Fill array with content from database, namely id, memberName, memberMessage and time
			$c++;										//Count every entry in database
		}
	}
	// echo"<pre>";print_r($databaseContent);

	//Post messages on Guest Book wall
	$submitMessage = $_POST['submitMessage'];				//When user clicks the submit message button
	$memberMessage = strip_tags($_POST['memberMessage']);	//Whatever the user types in the message box 
	$messageErrors = array(); 								//An array to hold error messages, will be called within HTML tags in code further below
	if($submitMessage){

		if(empty($memberMessage)){
			$messageErrors['noMessageWritten'] = "Please write a message";
		}
		else{
		$queryMessages = mysql_query("
				INSERT INTO messages VALUES ('', '$memberName', '$memberMessage', NOW())	
			");
			header("location: viewMessage.php");										//NOW() is a mysql current timestamp function, hence does not need ''
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

<section id="guest-page">
	<div class="welcome-user">
		<p>Welcome, <?php echo $memberName;?>. <a class="logout-btn" href="logout.php"> logout</a> </p> 
	</div>
	
	<div class="guest-header">
		<img src="images/guestbook.png">
	</div>

	<div class="guest-messages">
		<?php 
			for($i=0; $i<$c; $i++){
				echo "<h4>".$databaseContent[$i]['memberName']."</h4>" . "&nbsp;&nbsp;&nbsp;&nbsp;". "<h5>" .$databaseContent[$i]['time']. "</h5>";
				echo "<br><br>";
				echo $databaseContent[$i]['memberMessage'];
				echo "<br><hr>";
			}
		?>
	</div>

	<div class="write-message">
		<form action="guestlog.php" method="post">
			<div class="form-group">
				<label for="writeMessage">Your Message</label>
				<?php if(isset($messageErrors['noMessageWritten'])) {echo '<span class="error">'.$messageErrors['noMessageWritten'].'</span>';}  ?>
				<textarea class="form-control" name="memberMessage" rows="6"></textarea>
			</div>
			<input type="submit" name="submitMessage" class="btn btn-success" value="Submit">
		</form>	<br>
		
	</div>
</section>

<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/guestScripts.js"></script>

</body>
</html>