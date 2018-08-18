<? php

	require('config.php');

		if (! empty($_POST['roomInfo'])) {
			$room = $_POST['roomInfo'];

			$query = "
	            SELECT
	                *
	            FROM RoomA 
	            WHERE 
	                room_num = :roomnum
	        ";

	        $query_params = array(
	            ':roomnum' => "A"
	        ); 


	    try { 
	        $stmt = $db->prepare($query); 
	        $result = $stmt->execute($query_params); 
	    } 
	    catch(PDOException $ex) { 
	        die("Failed to run query: " . $ex->getMessage()); 
	    }

	    $information = $stmt->fetch();


	    // $instructor_name = $information['instructor_name'];
	    // $class_num = $information['class_num'];
	    // $class_date = $information['class_date'];
	    // $class_time = $information['class_time'];

	    $return_arr[] = array(
	    		"name" => "testname",
	            "email" => "testemail"
	        );

	    json_encode($return_arr);
	}
?>