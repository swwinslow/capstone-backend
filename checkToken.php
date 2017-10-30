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

$token = mysql_escape_string($_POST['token']);


$sqlEnter = "SELECT *
FROM  `token`
WHERE token_string =  '$token'";

$result = $conn->query($sqlEnter);

if ($result->num_rows > 0){

    while($row = $result->fetch_assoc()) {
      $user = $row['users_table_id'];
      $timestamp = $row['timestamp'];
    }

    $timstampUNIX = strtotime($timestamp);
    $currentStamp = time();

    $difference = $currentStamp - $timstampUNIX;

    //5 minutes
    $timeLength = 60*5;

          if($timeLength < $difference){

            //deleting the session
            $deleteSQL = "DELETE FROM `token` WHERE token_string = '$token'";

            //creating the new session
              http_response_code(403);
              $response = array("auth"=> "Failed", "status"=>403);

          } else {
            //keep
            http_response_code(200);
            $response = array("auth"=> "Success", "status"=>200);
          }

} else {
      // there is no session created
      http_response_code(403);
      $response = array("auth"=> "Failed", "status"=>403);
}

  echo json_encode($response);

?>
