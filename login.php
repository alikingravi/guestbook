<?php

session_start();

$submit = $_POST['submit'];
$username = $_POST['username'];
$password = $_POST['password'];
$loginErrors = array();

if ($submit){
	//Check if username is empty
	if (empty($username)){
		// header('location: index.php');
		$loginErrors['loginName'] = "User Name field cannot be empty";
		// var_dump($loginErrors['loginName']);
	}


if($username&&$password){

	$connect = mysql_connect("localhost", "root", "test123") or die("Could not connect to database");
	mysql_select_db("login") or die("Couldnt find database");

	$query = mysql_query("SELECT * FROM users WHERE username='$username'");
	$numrows = mysql_num_rows($query);

	if($numrows!=0){
		
		while ($row = mysql_fetch_assoc($query)) {
			$dbuserID = $row['id'];
			$dbusername = $row['username'];
			$dbpassword = $row['password'];
		}
		
		//Check to see if they match
		if ($username==$dbusername&&sha1($password)==$dbpassword) {
			$_SESSION['username']=$dbusername;
			$_SESSION['id']=$dbuserID;
			header('location: guestlog.php');
				exit;
			}else{
				header('location: index.php');
			}
	}else
		die("Please enter a valid username and password");
}

}

?>

