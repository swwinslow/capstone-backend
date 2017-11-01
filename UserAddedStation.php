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
  $password = "B5C8zUw9a1H";
  $dbname = "midwest_radio";

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
	$active = mysql_escape_string($_POST['active']);


  $sqlEnter = "INSERT INTO stations (frequency, long_name, short_name, city, state, slogan, active, deleted, type, genre, stream) VALUES ('$frequency', '$long_name', '$short_name', '$city', '$state', '$slogan', '$active', 0, '$type', '$genre', '$stream')";

	if ($conn->query($sqlEnter) === TRUE) {

		$sql2 = "SELECT * FROM stations WHERE long_name = '$long_name' AND short_name = '$short_name' AND stream='$stream'";
		$result = $conn->query($sql2);

		//todo look into the larger ids of the two if they are the same...

		if ($result->num_rows > 0){
			$stations = array();
			$count = 0;
			while($row = $result->fetch_assoc()) {
				$station = $row;
				array_push($stations, $station);

        // $sql = "SELECT * FROM users_table WHERE "
			}
		}


    $message = "A new station has been added by a user: 'http://willshare.com/cs495/admin/frontend/#/'";

    //
    // // In case any of our lines are larger than 70 characters, we should use wordwrap()
    $message123 = wordwrap($message, 70, "\r\n");
    //
    // // Send
    mail('swwinslow@gmail.com', 'Reset Password', $message123);

	  http_response_code(200);
		$response = array("status"=>"Station has been editied", "stations" => $stations, "code"=>200);
	} else {
	  http_response_code(200);
		$response = array("error"=>"Edit Station has final Failed","status"=>403);
	}
	echo json_encode($response);

?>
