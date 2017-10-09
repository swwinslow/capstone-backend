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


	//inserts the variables into a SQL query
	$sql2 = "SELECT * FROM popular ORDER BY votes DESC";
	$result = $conn->query($sql2);

	if ($result->num_rows > 0){
		$stations = array();
		$count = 0;
		while($row = $result->fetch_assoc()) {

			$station = $row;

			 array_push($stations, $station);
		}
        $response = array('status'=>200);
		$response["data"] = $stations;
	} else {
		http_response_code(200);
		$response = array("error"=>"No Dogs", "status"=>403);
	}

	echo json_encode($response);

	?>
