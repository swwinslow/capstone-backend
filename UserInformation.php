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

  //todo check the session key.....


  $sqlEnter = "SQL FOR FINDING THE current person that has control of the emails";
  $result = $conn->query($sqlEnter);

	//executes the SQL above, sends error if there is an error

  if ($result->num_rows > 0){
    $users = array();
    while($row = $result->fetch_assoc()) {
      $user = $row;
      array_push($users, $user);
    }

    http_response_code(200);
		$response = array("Users" => $users, "error"=>"N/A","status"=>200);
  } else {
    http_response_code(200);
    $response = array("error"=>"No Users in the system","status"=>200);

  }


	  echo json_encode($response);
?>
