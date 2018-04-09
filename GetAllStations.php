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

$servername = "";
$username = "";
$password = "";
$dbname = "";

$usernameADMIN = "";
$passwordADMIN = "";
$dbnameADMIN = "";

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
	$sql2 = "all active stations";
	$result = $conn->query($sql2);

	$sql3 = "all the pending stations";
	$result3 = $conn->query($sql3);

	$sql4 = "all the deleted stations";
	$result4 = $conn->query($sql4);

	$sql5 = "all the user pending stations";
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
