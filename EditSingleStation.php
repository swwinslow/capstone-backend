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

  $servername = "willshar.ipowermysql.com";
  $username = "admin_user";
  $password = "B5C8zUw9a1H";
  $dbname = "midwest_radio";

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
  $city = mysql_escape_string($_POST['city']);
  $state = mysql_escape_string($_POST['state']);
  $slogan = mysql_escape_string($_POST['slogan']);
  $type = mysql_escape_string($_POST['type']);
  $genre = mysql_escape_string($_POST['genre']);
  $stream = mysql_escape_string($_POST['stream']);
  $activeStatus = mysql_escape_string($_POST['active']);
	$deletedStatus = mysql_escape_string($_POST['delete']);
	$user_entered = mysql_escape_string($_POST['user_entered']);

  $sqlEnter = "UPDATE stations SET frequency = '$frequency', long_name = '$long_name', short_name = '$short_name', city = '$city', state = '$state', slogan = '$slogan', active = $activeStatus, deleted = $deletedStatus, type = '$type', genre = '$genre', stream = '$stream', user_entered = '$user_entered' WHERE id = '$id'";

	//executes the SQL above, sends error if there is an error
	if ($conn->query($sqlEnter) === TRUE) {

		$sql2 = "SELECT * FROM stations WHERE id = '$id'";
		$result = $conn->query($sql2);

		if ($result->num_rows > 0){
			$stations = array();
			$count = 0;
			while($row = $result->fetch_assoc()) {

				$station = $row;
				array_push($stations, $station);
			}
		}
	    http_response_code(200);
		$response = array("status"=>"Station has been editied", "stations" => $stations, "code"=>200);
	} else {
	    http_response_code(200);
		$response = array("error"=>"Edit Station has final Failed","status"=>403);
	}
	echo json_encode($response);
?>
