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
	$inputJSON = file_get_contents('php://input');
	$input = json_decode( $inputJSON, TRUE );

   //convert JSON into array
//database data

  $sqlEnter = "delete query for that staion id";

	//executes the SQL above, sends error if there is an error
	if ($conn->query($sqlEnter) === TRUE) {
	    http_response_code(200);
		$response = array("error"=>"Station has been editied","status"=>200);
	} else {
	    http_response_code(200);
		$response = array("error"=>"Edit Station has final Failed","status"=>403);
	}
	echo json_encode($response);
?>
