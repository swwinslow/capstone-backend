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

  $sqlEnter = "UPDATE stations SET frequency = '$frequency', long_name = '$long_name', short_name = '$short_name', city = '$city', state = '$state', slogan = '$slogan', active = '$active', deleted = '$deleted', type = '$type', genre = '$genre', stream = '$stream') WHERE id = '$id'";

	//executes the SQL above, sends error if there is an error
	if ($conn->query($sqlEnter) === TRUE) {
	    http_response_code(200);
		$response = array("error"=>"Station has been Updated","status"=>200);
	} else {
	    http_response_code(200);
		$response = array("error"=>"Update Station Failed","status"=>403);
	}
	echo json_encode($response);
?>
