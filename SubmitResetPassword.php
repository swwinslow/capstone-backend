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
	$input = $_GET;

  $hashedPassword =  hash('sha512', $_POST['password']);
  $token = mysql_escape_string($_POST['token']);

	//inserts the variables into a SQL query
	$emailQuery = "SELECT users_table_id FROM token WHERE token_string = '$token'";
	$resultEmailQuery = $conn->query($emailQuery);

	if ($resultEmailQuery->num_rows > 0){

		while($row = $resultEmailQuery->fetch_assoc()) {
				$id =  $row['id'];
		}

		$token = openssl_random_pseudo_bytes(64);
    //Convert the binary data into hexadecimal representation.
    $token = bin2hex($token);

    $deleteTokens = "DELETE FROM `token` WHERE users_table_id = '$id'";
    $deleteTokensQuery = $conn->query($deleteTokens);

    $tokenQuery = "INSERT INTO `token`(`users_table_id`, `timestamp`, `token_string`) VALUES ('$id', CURRENT_TIMESTAMP,'$token')";
  	$resultTokenQuery = $conn->query($tokenQuery);

    $returnToken = "SELECT token_string FROM 'token' WHERE users_table_id = '$id'";
    $returnTokenQuery = $conn->query($returnToken);
    //
    while($rowToken = $returnTokenQuery->fetch_assoc()) {
        $token =  $rowToken;
    }

    $message = "http://willshare.com/cs495/admin/frontend/#/resetpassword/";

    $fullMessage = $message . $token;

    // In case any of our lines are larger than 70 characters, we should use wordwrap()
    $message = wordwrap($fullMessage, 70, "\r\n");

    mail('swwinslow@gmail.com', 'My Subject', $message);


  $response = array('status'=>200);
  $response = array("error"=>"False","Email"=>"Sent", "status"=>200);

	} else {
		http_response_code(200);
		$response = array("error"=>"No Stations", "status"=>403);
	}

	echo json_encode($response);

	?>
