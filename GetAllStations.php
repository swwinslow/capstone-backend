<?php
	// For 4.3.0 <= PHP <= 5.4.0
	if (!function_exists('http_response_code'))
	{
	    function http_response_code($newcode = NULL)
	    {
	        static $code = 200;
	        if($newcode !== NULL)
	        {
	            header('X-PHP-Response-Code: '.$newcode, true, $newcode);
	            if(!headers_sent())
	                $code = $newcode;
	        }
	        return $code;
	    }
	}

	$servername = "willshar.ipowermysql.com";
	$username = "admin_user";
	$password = "2a7B9z?TD";
	$dbname = "south_midwest_radio";

	// $servername = "localhost:8080";
	// $username = "root";
	// $password = "root";
	// $dbname = "radio";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	header("Content-type: application/json");

	$response = array("message"=>"Something went wrong.","status"=>500);

	// Check connection
	if ($conn->connect_error) {
		http_response_code(500);
		echo json_encode(array("message"=>"Connection failed: " . $conn->connect_error,"status"=>500));
		die("Connection failed: " . $conn->connect_error);
		exit;
	}

	//assigns the posted values to variables
	$input = $_GET;


	//inserts the variables into a SQL query
	$sql2 = "SELECT * FROM stations WHERE active = 1";
	$result = $conn->query($sql2);

	$sql3 = "SELECT * FROM stations WHERE active = 0 AND deleted = 0 AND user_entered = 0";
	$result3 = $conn->query($sql3);

	$sql4 = "SELECT * FROM stations WHERE deleted = 1";
	$result4 = $conn->query($sql4);

	$sql5 = "SELECT * FROM stations WHERE active = 0 AND deleted = 0 AND user_entered = 1";
	$result5 = $conn->query($sql5);

	if ($result->num_rows > 0){
		$activeStations = array();
		$pendingStations = array();
		$pendingUEStations = array();
		$deletedStations = array();

		while($row1 = $result->fetch_assoc()) {
				$activeStation = $row1;
				array_push($activeStations, $activeStation);
		}

		while($row2 = $result3->fetch_assoc()) {
				$pendStation = $row2;
				array_push($pendingStations, $pendStation);
		}

		while($row3 = $result4->fetch_assoc()) {
				$delStation = $row3;
				array_push($deletedStations, $delStation);
		}

		while($row3 = $result5->fetch_assoc()) {
				$station = $row3;
				array_push($pendingUEStations, $station);
		}

		$AllStations = $activeStations;

		//  $AllStations = ['active' => $activeStations];
        $response = array('status'=>200);
		$response["active"] = $AllStations;
		$response["pending"] = $pendingStations;
		$response["pendingUE"] = $pendingUEStations;
		$response["deleted"] = $deletedStations;

	} else {
		http_response_code(200);
		$response = array("error"=>"No Stations", "status"=>403);
	}

	echo json_encode($response);

	?>
