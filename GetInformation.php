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
$sql2 = "query for active type";
$result = $conn->query($sql2);

$sql3 = "query for active genre";
$result3 = $conn->query($sql3);

$sql4 = "query for active state";
$result4 = $conn->query($sql4);

	if ($result->num_rows > 0){
		$typeArray = array();
		$genreArray = array();
		$statesArray = array();

		while($row1 = $result->fetch_assoc()) {
				$type = $row1;
				array_push($typeArray, $type);
		}



		while($row2 = $result3->fetch_assoc()) {
				$genre = $row2;
				array_push($genreArray, $genre);
		}



		while($row3 = $result4->fetch_assoc()) {
				$state = $row3;
				array_push($statesArray, $state);
		}


		$AllStations = $activeStations;

		//  $AllStations = ['active' => $activeStations];
    $response = array('status'=>200);
		$response["types"] = $typeArray;
		$response["genre"] = $genreArray;
		$response["states"] = $statesArray;

	} else {
		http_response_code(200);
		$response = array("error"=>"No Stations", "status"=>403);
	}

	echo json_encode($response);

	?>
