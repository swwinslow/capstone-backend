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
  $username = "csstudent";
  $password = "DrLinRules";
  $dbname = "cs495_admin";

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

  $email = mysql_escape_string($_POST['email']);

	//inserts the variables into a SQL query
	$emailQuery = "SELECT id, email FROM users_table WHERE email = '$email'";
	$resultEmailQuery = $conn->query($emailQuery);

	if (true){

		while($row = $resultEmailQuery->fetch_assoc()) {

            $id =  $row['id'];
			$email =  $row['email'];
		}

		$token = bin2hex(openssl_random_pseudo_bytes(64));
		//
		$deleteTokens = "DELETE FROM `token` WHERE users_table_id = '$id'";
		$deleteTokensQuery = $conn->query($deleteTokens);
		//

		$tokenQuery = "INSERT INTO  `token` (  `id` ,  `users_table_id` ,  `timestamp` ,  `token_string` ) VALUES ( NULL ,  '$id', NOW( ) ,  '$token' );";

		$resultTokenQuery = $conn->query($tokenQuery);

		$message = "Here is the link you requsted. \r\r\r If this is a unknown, please ignore. \r\r http://willshare.com/cs495/admin/frontend/#/resetpassword/". $token;

		mail($email, 'Reset Password', $message);

		$response = array('status'=>200);
		$response = array("error"=>"False","Email"=>"sent", "id"=> $id, "status"=>200);

	} else {
		http_response_code(403);
		$response = array("error"=>"Could not complete request", "status"=>403);
	}

	echo json_encode($response);

?>
