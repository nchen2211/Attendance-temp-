<?php
	require("config.php");  

    if(! empty($_POST)){ 

        //query database for user credentials
        $query = " 
            SELECT 
                id, 
                username, 
                password, 
                acct_type
            FROM users 
            WHERE 
                username = :username 
        ";

        $query_params = array( 
            ':username' => $_POST['username'] 
        ); 
         
        try{ 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) { 
            die("Failed to run query: " . $ex->getMessage()); 
        } 

        $login_ok = false;
        $row = $stmt->fetch();


        if ($row) {
        	$typed_password = $_POST['password'];

        	if ($typed_password === $row['password']){
        		$login_ok = true;
        	} 
        }

        if ($login_ok) {

        	if ($row['acct_type'] == 'facilitator') {
        		// redirect browser
        		header("Location: facilitator.php");
        		exit;
        	}

        	if ($row['acct_type'] == 'student') {
        		$room = $_POST['username']; 	
        		$room = trim($room);
        		$room = str_ireplace(" ","",$room);

        		// $time = $_POST['time'];
        		header("Location: student.php?user=" . $room);
        		exit;
        	}
        }
	}
?>

<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta charset="utf-8">
    <title>ELC Attendance: Login</title>
    <meta name="description" content="USC Experiential Learning Center Attendance">	
</head>

<body background="image.jpg" style="background-repeat: no-repeat; background-size: cover;">
	<form action = "index.php" method = "POST">
		<label for = "inputusername">Username</label>
		<input type = "user" id= "_username" placeholder="Enter Username" name="username">
		<label for = "inputpassword">Password</label>
		<input type = "password" id= "_password" placeholder="Enter Password" name="password">

		<input type = "submit" value = "Sign In">
	</form>
</body>


