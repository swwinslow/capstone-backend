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
	$ADMINconn = new mysqli($servername, $usernameADMIN, $passwordADMIN, $dbnameADMIN);

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
	$input = json_decode( $inputJSON, TRUE ); //convert JSON into array

    //station data and query



$sqlEnter = "check session";

$result = $ADMINconn->query($sqlEnter);

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        $user = $row['user_id'];
        $timestamp = $row['timestamp'];
    }

    $timstampUNIX = strtotime($timestamp);
    $currentStamp = time();

    $difference = $currentStamp - $timstampUNIX;

    $timeLength = 2 * 24 * 60 * 60;

    if ($timeLength < $difference) {

        //deleting the session
        $deleteSQL = "delet the session";

        //creating the new session
        http_response_code(200);
        $response = array("auth" => "Failed", "status" => 200);

    } else {

        $sqlEnter = "insert the station with the new data";

        if ($conn->query($sqlEnter) === TRUE) {

            $sql2 = "find the sttion that you just inserted";
            $result = $conn->query($sql2);

            //todo look into the larger ids of the two if they are the same...

            if ($result->num_rows > 0) {
                $stations = array();
                $count = 0;
                while ($row = $result->fetch_assoc()) {

                    $station = $row;
                    array_push($stations, $station);
                }
            }

            http_response_code(200);
            $response = array("status" => "Station has been added", "stations" => $stations, "code" => 200);
        } else {
            http_response_code(200);
            $response = array("error" => "Added Station has failed", "status" => 403);
        }
    }
} else {
    http_response_code(200);
    $response = array("error" => "No user found", "status" => 403);
}


    echo json_encode($response);


?>