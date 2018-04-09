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
	$sql2 = "query to get all the votes in the database";
	$result = $conn->query($sql2);

	if ($result->num_rows > 0){
		$stations = array();
		$count = 0;
		while($row = $result->fetch_assoc()) {

			$station = $row;

			 array_push($stations, $station);
		}
        $response = array('status'=>200);
		$response["data"] = $stations;
	} else {
		http_response_code(200);
		$response = array("error"=>"No Votes", "status"=>403);
	}

	echo json_encode($response);

	?>
