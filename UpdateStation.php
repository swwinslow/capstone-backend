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

	$short_name = mysql_escape_string($_POST['short_name']);
	$long_name = mysql_escape_string($_POST['long_name']);
	$frequency = mysql_escape_string($_POST['frequency']);
    $city = mysql_escape_string($_POST['city']); $state = mysql_escape_string($_POST['state']);
    $slogan = mysql_escape_string($_POST['slogan']);
    $type = mysql_escape_string($_POST['type']);
    $genre = mysql_escape_string($_POST['genre']);
    $stream = mysql_escape_string($_POST['stream']);
    $id = mysql_escape_string($_POST['id']);


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

        $sqlEnter = "UPDATE stations SET frequency = '$frequency', long_name = '$long_name', short_name = '$short_name', city = '$city', state = '$state', slogan = '$slogan', active = '$active', deleted = '$deleted', type = '$type', genre = '$genre', stream = '$stream') WHERE id = '$id'";

        //executes the SQL above, sends error if there is an error
        if ($conn->query($sqlEnter) === TRUE) {
            http_response_code(200);
            $response = array("error" => "Station has been Updated", "status" => 200);
        } else {
            http_response_code(200);
            $response = array("error" => "Update Station Failed", "status" => 403);
        }
    }
} else {
    http_response_code(200);
    $response = array("error" => "Update Station Failed", "status" => 403);
}
	echo json_encode($response);
?>
