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

	$usernameADMIN = "csstudent";
	$passwordADMIN = "DrLinRules";
	$dbnameADMIN = "cs495_admin";

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

	$short_name = mysql_escape_string($_POST['short_name']);
	$long_name = mysql_escape_string($_POST['long_name']);
	$frequency = mysql_escape_string($_POST['frequency']);
	$city = mysql_escape_string($_POST['city']);
	$state = mysql_escape_string($_POST['state']);
	$slogan = mysql_escape_string($_POST['slogan']);
	$type = mysql_escape_string($_POST['type']);
	$genre = mysql_escape_string($_POST['genre']);
	$stream = mysql_escape_string($_POST['stream']);
    $website = mysql_escape_string($_POST['website']);
    $active = mysql_escape_string($_POST['active']);
	$user_entered = mysql_escape_string($_POST['user_entered']);

	//todo check to see if the slogan has quotes around it.

$session_id = mysql_escape_string($_POST['session_id']);
$session_key = mysql_escape_string($_POST['session_key']);


$sqlEnter = "SELECT user_id, timestamp
	FROM  `session`
	WHERE session_id =  '$session_id'
	AND session_key =  '$session_key'";

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
        $deleteSQL = "DELETE FROM `session` WHERE session_id = '$session_id'";

        //creating the new session
        http_response_code(200);
        $response = array("auth" => "Failed", "status" => 200);

    } else {

        $sqlEnter = "INSERT INTO stations (frequency, long_name, short_name, city, state, slogan, active, deleted, type, genre, stream, website ,user_entered) VALUES ('$frequency', '$long_name', '$short_name', '$city', '$state', '$slogan', '$active', 0, '$type', '$genre', '$stream', '$website' ,'$user_entered')";

        if ($conn->query($sqlEnter) === TRUE) {

            $sql2 = "SELECT * FROM stations WHERE long_name = '$long_name' AND short_name = '$short_name' AND stream='$stream'";
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