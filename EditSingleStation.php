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

	if (get_magic_quotes_gpc() === 1)
{
    $_GET = json_decode(stripslashes(json_encode($_GET, JSON_HEX_APOS)), true);
    $_POST = json_decode(stripslashes(json_encode($_POST, JSON_HEX_APOS)), true);
    $_COOKIE = json_decode(stripslashes(json_encode($_COOKIE, JSON_HEX_APOS)), true);
    $_REQUEST = json_decode(stripslashes(json_encode($_REQUEST, JSON_HEX_APOS)), true);
}

$servername = "";
$username = "";
$password = "";
$dbname = "";

$usernameADMIN = "";
$passwordADMIN = "";
$dbnameADMIN = "";

// Create connection
   $ADMINconn = new mysqli($servername, $usernameADMIN, $passwordADMIN, $dbnameADMIN);

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
	$input = json_decode( $inputJSON, TRUE ); //convert JSON into array

//database data


$sqlEnter = "query check";

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
        $deleteSQL = "delete the token";

        //creating the new session
        http_response_code(200);
        $response = array("auth" => "Failed", "status" => 200);

    } else {

        $sqlEnter = "update the station data with the current data in the database from the user";

        //executes the SQL above, sends error if there is an error
        if ($conn->query($sqlEnter) === TRUE) {

            $sql2 = "get all the stations";
            $result = $conn->query($sql2);

            if ($result->num_rows > 0) {
                $stations = array();
                $count = 0;
                while ($row = $result->fetch_assoc()) {

                    $station = $row;
                    array_push($stations, $station);
                }
            }
            http_response_code(200);
            $response = array("status" => "Station has been editied", "stations" => $stations, "code" => 200);
        } else {
            http_response_code(200);
            $response = array("error" => "Edit Station has final Failed", "status" => 403);
        }
    }
} else {
    http_response_code(200);
    $response = array("error" => "Edit Station has final Failed", "status" => 403);
}
	echo json_encode($response);
?>
