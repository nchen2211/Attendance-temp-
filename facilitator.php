 <?php 
	require('config.php');

	 if (! empty($_POST['storeSession'])){

		$data_received = $_POST['storeSession'];
		
		// Add row to database 
        $query = " 
            INSERT INTO classes (
            	class_id,
                instructor_name, 
                class_num, 
                class_time,
                class_date
            ) VALUES ( 
            	default,
                :instructor_name, 
                :class_num,
                :class_time, 
                :class_date
            ) 
        ";

        // $query_params = array( 
        //     ':instructor_name' => $_POST["instructor_name"], 
        //     ':class_num' => $_POST["class_num"], 
        //     ':class_time' => $_POST["class_time"], 
        //     ':class_date' => $_POST["class_date"]
        // );
        $query_params = array( 
            ':instructor_name' => $data_received["data_instructorname"], 
            ':class_num' => $data_received["data_classnum"], 
            ':class_time' => $data_received["data_time"], 
            ':class_date' => $data_received["data_date"]
        );


        try {  
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) { 
            die("Failed to add course: " . $ex->getMessage()); 
        }

        header("Location: facilitator.php?message=success");	
	} 

	// TODO: why if it is empty it is able to retrieve data
	if (empty($_POST['passRoom'])){

			$query = "
            SELECT
                student_name, student_email
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

	    $information = $stmt->fetchAll();

	    $return_arr[] = Array();

	    foreach($information as $row) {
	    	$element_array = Array();
	    	array_push($element_array, $row['student_name']);
	    	array_push($element_array, $row['student_email']);

	    	array_push($return_arr, $element_array);
	    }

	    $result_array = array_shift($return_arr);

	    json_encode($result_array);
	} 


	// TODO: why if it is empty it is able to retrieve data
	if (empty($_POST['getInformation'])){
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

	    $info_result[] = array(
	    		"instructor_name" => $instructor_name,
	            "class_num" => $class_num,
	            "class_date" => $class_date,
	            "class_time" => $class_time
	        );

	    json_encode($info_result);
	}
?>

<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta charset="utf-8">
    <title>ELC Attendance: Technician Page</title>
    <meta name="description" content="USC Experiential Learning Center Attendance">
    <link rel="stylesheet" type="text/css" src="facilitator.css">

</head>

<body>
	
	 <!-- Alert Insertion -->
    <div id="alert-insert"></div>
    <br><br>

	<div id="create_session">
		<form method = "POST"> <!-- onsubmit="validateSession()"> -->
			<!-- <form action = "facilitator.php" method = "POST"> -->
			<label for = "instructorname">Instructor's Name</label>
			<input type = "instructorname" id= "_instructorName" placeholder="Enter instructor's name" name="instructor_name">
			<br><br>

			<label for = "classnum">Class number (ie: 302)</label>
			<input type = "semester" id= "_classNum" placeholder="Enter class number" name="class_num">
			<br><br>

			<!-- TODO: create a dropdown list for AM/PM -->
			<label for = "classtime">Time</label>
			<input type = "classtime" id= "_time" placeholder="Enter the time" name="class_time">
			<br><br>

			<label for = "date">Date</label>
			<input type = "date" id= "_date" placeholder="Enter the date" name="class_date">
			<br>

			<p> Please check the room that will be used for this class</p>
			<div id = "room_checkbox">
				<input type="checkbox" class="rooms" value="A">Room A<br>
				<input type="checkbox" class="rooms" value="B">Room B<br>
				<input type="checkbox" class="rooms" value="C">Room C<br>
				<input type="checkbox" class="rooms" value="D">Room D<br>
				<input type="checkbox" class="rooms" value="E">Room E<br>
				<input type="checkbox" class="rooms" value="F">Room F<br>
				<input type="checkbox" class="rooms" value="G">Room G<br>
				<input type="checkbox" class="rooms" value="H">Room H<br>
				<input type="checkbox" class="rooms" value="I">Room I<br>
				<input type="checkbox" class="rooms" value="J">Room J<br>
				<input type="checkbox" class="rooms" value="K">Room K<br>
				<input type="checkbox" class="rooms" value="L">Room L<br>
				<input type="checkbox" class="rooms" value="M">Room M<br>
				<input type="checkbox" class="rooms" value="N">Room N<br><br>
			</div>
			
			<button type = "button" id ="mergeButton">Merge</button>
			<button type = "button" id ="cancelMergeButton">Cancel Merge</button>

			<br><br>
			<input type = "submit" value = "Create Session" method="post">
		</form>

		<!-- to output students emails in panopto format -->
		<br><br>
	
		<input type="button" id="panopto" value="Output Attendance" method="post">
		<input type="button" id="print_attendance" value="Print Attendance">
		<input type="button" id="get_pdf" value="Get Attendance List PDF">
	

		<div id="output_panopto" style="display: none">
			<p id="panopto_list"></p>
		</div>

		<div id="attendance_table">
		</div>
			
	</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<!-- js pdf API -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

    <script type="text/javascript"></script>

    <script>

    	 var mInstructorName; 
    	 var mClassNum;
    	 var mDate;
    	 var mTime;
    	 var mRooms = [];
    	 var attendanceList =[];

    	 var is_erased = false;

    	 $('#create_session').submit(function(e) {
    	 	// get instructor's name
    		mInstructorName = $('#_instructorName').val();
    		// get class number
    		mClassNum = $('#_classNum').val();
    		// get date
    		mDate = $('#_date').val();
    		// get time
    		mTime = $('#_time').val();

    		// get room used
    		$('#room_checkbox .rooms:checked').each(function() {
    			mRooms.push($(this).attr("value"));
    		});

    		var data = {	
				"data_instructorname": mInstructorName,
				"data_classnum": mClassNum,
				"data_time": mTime,
				"data_date": mDate
    		};

    		// data = $(this).serialize() + "&" + $.param(data);
    		e.preventDefault();
    		var is_success = false;

    		$.ajax({
    			type: "POST",
    			url: "facilitator.php",
    			data: {
    				storeSession : data
    			},
    			success: function(data) {		
    				
		    		// reset textbox
		    		$('#_instructorName').val('');
		    		$('#_classNum').val('');
		    		$('#_date').val('');
		    		$('#_time').val('');

		    		alert("Session is created");
    			}
    		});

    	 	return false;
    	 });


    	function validateSession(e) {

    		// get instructor's name
    		mInstructorName = $('#_instructorName').val();
    		// get class number
    		mClassNum = $('#_classNum').val();
    		// get date
    		mDate = $('#_date').val();
    		// get time
    		mTime = $('#_time').val();

    		// get room used
    		$('#room_checkbox .rooms:checked').each(function() {
    			mRooms.push($(this).attr("value"));
    		});

    		var data = {
    			"action": "createSession",	
				"data_instructorname": mInstructorName,
				"data_classnum": mClassNum,
				"data_time": mTime,
				"data_date": mDate
    		};

    		data = $(this).serialize() + "&" + $.param(data);
    		// e.preventDefault();

    		$.ajax({
    			type: "POST",
    			dataType: "json",
    			url: "facilitator.php",
    			data: data,
    			success: function(data) {
    				$("#alert-insert").css('color', 'green');
        			$("#create_semester").show();

		    		console.log("name " + mInstructorName);
		    		console.log("class num " + mClassNum);
		    		console.log("date " + mDate);
		    		console.log("time " + mTime);
    			}
    		});


    		// for (var i=0; i < mRooms.length; ++i) {
    		// 	console.log("room " + mRooms[i] + "\n");
    		// }
    	}

    	// generate emails in panopto format
    	$("#panopto").click(function(e) {
    	
    		e.preventDefault();
    		var room = "A";

    		$.ajax({
    			type: "POST",
    			url: "facilitator.php",
    			data: {
    				passRoom : 'A'
    			},
    			success: function(data) {	
				 attendanceList = <?php echo json_encode($return_arr) ?>;

    				 $('#output_panopto').css('display', 'block');	
			   		 $('#panopto_list').append("<h1>Panopto format attendance list for Room " + room + "</h1>" );

			   		 // output the student email in panopto format
			   		 for (var i=0; i<attendanceList.length; ++i) {
			   		 	$('#panopto_list').append('<span>' + attendanceList[i][1] + '</span> </br>');
			   		 }
    			}
    		});

    	 	return false;
    	});

    	// print attendance list and session information
    	$("#print_attendance").click(function(e) {
    		console.log('click');
    		e.preventDefault();
    		var room = "A";

    		$.ajax({
    			type: "POST",
    			url: "facilitator.php",
    			data: {
    				getInformation : 'A'
    			},
    			success: function(data) {	
    				var result = <?php echo json_encode($info_result) ?>;
    				console.log(result);
    				console.log(result[0].instructor_name);
    				console.log(result[0].class_num);

    				if (! is_erased) {
	    				// create html table
	    				var content = "<table><tr> <th> Name </th> <th> Email </th></tr>";
	    			
						for(i=0; i<attendanceList.length; i++){

						    content += '<tr><th>' + attendanceList[i][0] + '</th>' + '<th>' + attendanceList[i][1] + '</th> </tr>';
						}
						content += "</table>";

						// output the class information
						$('#attendance_table').append("<p>Room number " + room + "</p") ;
						$('#attendance_table').append("<p>Instructor's name  " + result[0].instructor_name + "</p>") ;
						$('#attendance_table').append("<p>Class number  " + result[0].class_num + "</p>") ;
						$('#attendance_table').append("<p>Class date  " + result[0].class_date + "</p>") ;
						$('#attendance_table').append("<p>Class date  " + result[0].class_time + "</p>") ;

						// output attendance table
						$('#attendance_table').append(content);
					} else {
						alert("The data has been erased");
					}
    			}
    		});

    	 	return false;
    	});

    	// download PDF of the attendance list
    	$('#get_pdf').click(function (e) {
    		getPDF();
    	})

        //parses URL for GET parameters 
        function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
        return vars;
        }

        //insert proper alert given GET parameters
        switch(getUrlVars()["message"]) {
        	case "success":
        		$("#alert-insert").append('<div class="alert alert-danger alert-dismissible fade in" role="alert"><span aria-hidden="true"></span></button><strong>The course and its associated rooms are created successfully</strong></div>');
        		$("#alert-insert").css('color', 'green');
        		$("#create_semester").show();
                break;
            case "coursealreadyset":
                $("#alert-insert").append('<div class="alert alert-danger alert-dismissible fade in" role="alert"><span aria-hidden="true"></span></button><strong>The course number you entered already exists</strong></div>');
                $("#alert-insert").css('color', 'red');
                $("#create_semester").show();
                break;
        }

        // to get PDF format
        function getPDF() {
	        var pdf = new jsPDF('p', 'pt', 'letter');
	        // source can be HTML-formatted string, or a reference
	        // to an actual DOM element from which the text will be scraped.
	        source = $('#attendance_table')[0];

	        // we support special element handlers. Register them with jQuery-style 
	        // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
	        // There is no support for any other type of selectors 
	        // (class, of compound) at this time.
	        specialElementHandlers = {
	            // element with id of "bypass" - jQuery style selector
	            '#bypassme': function (element, renderer) {
	                // true = "handled elsewhere, bypass text extraction"
	                return true
	            }
	        };
	        margins = {
	            top: 80,
	            bottom: 60,
	            left: 40,
	            width: 522
	        };
	        // all coords and widths are in jsPDF instance's declared units
	        // 'inches' in this case
	        pdf.fromHTML(
	            source, // HTML string or DOM elem ref.
	            margins.left, // x coord
	            margins.top, { // y coord
	                'width': margins.width, // max width of content on PDF
	                'elementHandlers': specialElementHandlers
	            },

	            function (dispose) {
	                // dispose: object with X, Y of the last line add to the PDF 
	                //          this allow the insertion of new lines after html
	                pdf.save('Attendance.pdf');
	            }, margins
	        );
    	}

    </script>
</body>