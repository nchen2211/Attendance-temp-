<?php

	require('config.php');
	
	// ############# get information of the class exercise #########
 	$query = "
            SELECT
                *
            FROM classes 
            WHERE 
                class_time = :classtime
        ";

        $query_params = array(
            ':classtime' => "10 AM"
        ); 


    try { 
        $stmt = $db->prepare($query); 
        $result = $stmt->execute($query_params); 
    } 
    catch(PDOException $ex) { 
        die("Failed to run query: " . $ex->getMessage()); 
    }

    $information = $stmt->fetch();
    $instructor_name = $information['instructor_name'];
    $class_num = $information['class_num'];
    $class_date = $information['class_date'];
    $class_time = $information['class_time'];

    $return_arr[] = array(
    		"instructor_name" => $instructor_name,
            "class_num" => $class_num,
            "class_date" => $class_date,
            "class_time" => $class_time
        );

    json_encode($return_arr);
    // ############################################################


   
    // ############# storing attendance ###########################
    if (! empty($_POST['sendArray']) && ! empty($_POST['roomInfo'])) {
    	// get room info
    	$room_number = $_POST['roomInfo'];
    	// get attendance array
    	$array = $_POST['sendArray'];
    	(json_decode($array));


    	for ($i = 0; $i < count($array); ++$i) {
    		$name;
    		$email = "";

    		for ($j = 0; $j < 2; ++$j) {
    			if ($j == 0) {
    				$name = $array[$i][$j]['name'];
    			} else if ($j == 1) {
    				$email = $array[$i][$j]['email'];
    			}
    		}

	    	// Add rows to database 
			$query = " 
	            INSERT INTO Room".$room_number." (
	                student_name, 
	                student_email,
	                room_num
	            ) VALUES ( 
	               	:studentName, 
	                :studentEmail,
	                :roomNum
	            ) 
	    	";

	    	$query_params = array( 
	            ':studentName' => $name, 
	            ':studentEmail' =>  $email, 
	            ':roomNum' => $room_number
	        );

	         try {  
	            $stmt = $db->prepare($query); 
	            $result = $stmt->execute($query_params); 
	        } 
	        catch(PDOException $ex) { 
	            die("Failed to add course: " . $ex->getMessage()); 
	        }  

	        header("Location: student.php?message=success");
    	}
    }

    // ########################################################
?>

<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta charset="utf-8">
    <title>ELC Treasure Hunt: Student</title>
    <meta name="description" content="USC Experiential Learning Center Attendance">

    <style type="text/css">
		#textboxGroup{
			padding:8px;
		}

		#addButton {
			float: right; 
		}
	</style>
</head>

<body >
	
	<!-- show session information -->
	<div class = "room_info"></div> <br>
	Instructor's name: <div class = "name_info"></div> <br>
	Class number: <div class = "class_info"></div> <br>
	Date: <div class = "date_info"></div> <br>
	Time: <div class = "time_info"></div> <br>

	
	<!-- attendance -->
	<div id = "attendance">
		<form method = "POST">
			<div id = "textboxGroup">

				<div id = "textboxname1">
					<label>Student 1</label>
					<input type = "name1" id= "_student1" placeholder="Enter name" name="student1_name">
			
					<label>Email</label>
					<input type = "email1" id= "email_student1" placeholder="Enter email" name="student1_email">

					<br><br>
				</div>

				<div id = "textboxname2">
					<label>Student 2</label>
					<input type = "name2" id= "_student2" placeholder="Enter name" name="student2_name">
			
					<label>Email</label>
					<input type = "email2" id= "email_student2" placeholder="Enter email" name="student2_email">

					<br><br>
				</div>

				<div id = "textboxname3">
					<label>Student 1</label>
					<input type = "name3" id= "_student3" placeholder="Enter name" name="student3_name">
			
					<label>Email</label>
					<input type = "email3" id= "email_student3" placeholder="Enter email" name="student3_email">

					<br><br>
				</div>

				<div id = "textboxname4">
					<label>Student 4</label>
					<input type = "name4" id= "_student4" placeholder="Enter name" name="student4_name">
			
					<label>Email</label>
					<input type = "email4" id= "email_student4" placeholder="Enter email" name="student4_email">

					<br><br>
				</div>

				<div id = "textboxname5">
					<label>Student 5</label>
					<input type = "name5" id= "_student5" placeholder="Enter name" name="student5_name">
			
					<label>Email</label>
					<input type = "email5" id= "email_student5" placeholder="Enter email" name="student5_email">

					<br><br>
				</div>

			</div>

				<br><br>
				<input type = "button" value = "Add More Student" id = "addButton"> 
				<br><br>
				<input type = "submit" value = "Submit Attendance">

		</form>
	</div>

	  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	  <!-- Include socket.io plugin libraries -->
	  <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>

	  <script>


	  var room_info;
	  $(document).ready(function() {
	  		// get room number
	  		var room = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                room[key] = value;
                console.log(room[key]);
            });

            for (var i = 0; i <room.user.length; ++i) {
            	if (i == 4) {
            		room_info = room.user[i];
            	}
            }
          	
            $.ajax({
	  		   type: "POST",
			   url: "student.php",
			   success: function(data) {
			   		var result = <?php echo json_encode($return_arr) ?>;

			   		$(".room_info").text("Room " + room_info);
			   		$(".name_info").text(result[0].instructor_name);
			   		$(".class_info").text(result[0].class_num);
			   		$(".date_info").text(result[0].class_date);
			   		$(".time_info").text(result[0].class_time);
        		}
			});

	  	});


	  // adding textbox dynamically starts from the 6th textbox
	  var count = 5; 
	  $("#addButton").click(function() {
	  	console.log(count);
	  	++count;

	  	var newtextboxname = $(document.createElement('div')).attr("id", 'textboxname' + count);

	  	newtextboxname.after().html('<label>Student ' + count + '</label> <input type="name ' + count  + '"" id="_student' + count + '" placeholder="Enter name" name="student' + count + '_name"> <label>Email</label> <input type="email' + count  +  '" id="email_student' + count + '" placeholder="Enter email" name="student' + count + '_email"> <br><br>');

	  	newtextboxname.appendTo('#textboxGroup');
	  });

	  // when attendance is submitted
	   $('#attendance').submit(function(e) {

	   		var students_array = new Array(); // data structure to store students' names and emails

	   		// loop through textboxes and store students' names and email
	   		for (var i=1; i <= count; ++i) {
	   			if (document.getElementById('_student' + i).value === "") {
	   				break;
	   			}

	   			var name = document.getElementById('_student' + i).value;
	   			var email = document.getElementById('email_student' + i).value;
	   			
	   			var student = new Array({"name" : name}, {"email" : email});
	   			students_array.push(student);

	   		}

	   		console.log(students_array);
    		e.preventDefault();
    	

    		$.ajax({
    			type: "POST",
    			url: "student.php",
    			data: {
    				sendArray : students_array, 
    				roomInfo : room_info
    			},
    			
    			success: function(data) {		
    				
		    		// reset textbox
		    		for (var i=1; i <= count; ++i) {
		    			document.getElementById('_student' + i).value = "";
		    			document.getElementById('email_student' + i).value = "";
		    		}
		    		alert ("Attendance is submitted successfully");
    			}
    		});
    		
    	 	return false;
    	 });



	  //parses URL for GET parameters 
        function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
        return vars;
        }

        //insert proper alert given GET parameters
        switch(getUrlVars()["user"]) {
        	case "success":
        		$("#alert-insert").append('<div class="alert alert-danger alert-dismissible fade in" role="alert"><span aria-hidden="true"></span></button><strong>Students attendance are saved successfully</strong></div>');
        		$("#alert-insert").css('color', 'green');
        		$("#create_semester").show();
                break;
        }
       </script>
</body>