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

$session_id = mysql_escape_string($_POST['session_id']);
$session_key = mysql_escape_string($_POST['session_key']);


$sqlEnter = "SELECT user_id, timestamp
FROM  `session`
WHERE session_id =  '$session_id'
AND session_key =  '$session_key'";

$result = $conn->query($sqlEnter);


if ($result->num_rows > 0){

    while($row = $result->fetch_assoc()) {
      $user = $row['user_id'];
      $timestamp = $row['timestamp'];
    }

    $timstampUNIX = strtotime($timestamp);
    $currentStamp = time();

    $difference = $currentStamp - $timstampUNIX;

    $timeLength = 2*24*60*60;

          if($timeLength < $difference){

            //deleting the session
            $deleteSQL = "DELETE FROM `session` WHERE session_id = '$session_id'";

            //creating the new session
              http_response_code(200);
              $response = array("auth"=> "Failed", "status"=>200);

          } else {
            //keep
            http_response_code(200);
            $response = array("auth"=> "Success", "status"=>200);
          }

} else {
      // there is no session created
      http_response_code(200);
      $response = array("auth"=> "Failed", "status"=>200);
}

  echo json_encode($response);

?>
