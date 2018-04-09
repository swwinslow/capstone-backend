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

//databse creditials

$sqlEnter = "matching query with the data that was sent in";

$result = $conn->query($sqlEnter);


if ($result->num_rows > 0){

    while($row = $result->fetch_assoc()) {
      $user = $row['id'];
    }

    $sqlCheck = "see if there is a session for the user";

    $resultCheck = $conn->query($sqlCheck);

    if ($resultCheck->num_rows > 0){
      $sessionArray = array();

      while($row2 = $resultCheck->fetch_assoc()) {
        $oldSession = $row2;
        $sessoin = $row2['timestamp'];
        $id = $row2['session_id'];
        array_push($sessionArray, $sessoin);
      }
      $timstampUNIX = strtotime($sessoin);
      $currentStamp = time();

      $difference = $currentStamp - $timstampUNIX;

      $timeLength = 2*24*60*60;

      if($timeLength < $difference){
        //renew...

        //deleting the session
        $deleteSQL = "delete the old sesion";

        //creating the new session
        $key = bin2hex(openssl_random_pseudo_bytes(64));
        $sqlCreate= "new session insert";
        $sqlCreateResult = $conn->query($sqlCreate);

        //returning the new seesion
        $sqlGetSession = "get the new sesions";
        $sesGetResult = $conn->query($sqlGetSession);

        if ($sesGetResult->num_rows > 0){
        		while($row = $sesGetResult->fetch_assoc()) {
        			$ThisSession = $row;
        		}
          http_response_code(200);
          $response = array("session"=> $ThisSession, "status"=>200);
        } else {
          http_response_code(200);
          $response = array("error"=> 'we have hit an error', "status"=>200);
        }

      } else {
        //keep
        http_response_code(200);
        $response = array("session"=> $oldSession, "status"=>200);
      }
    } else {
      // there is no session created
      $key = bin2hex(openssl_random_pseudo_bytes(64));
      $sqlCreate= "create the session";
      $sqlCreateResult = $conn->query($sqlCreate);

      //returning the new seesion
      $sqlGetSession = "select the new session";
      $sesGetResult = $conn->query($sqlGetSession);

      if ($sesGetResult->num_rows > 0){
          while($row = $sesGetResult->fetch_assoc()) {
            $ThisSession = $row;
          }
        http_response_code(200);
        $response = array("session"=> $ThisSession, "status"=>200);
      } else {
        http_response_code(200);
        $response = array("error"=> 'we have hit an error', "status"=>200);
      }
    }


  } else {
    http_response_code(403);
    $response = array("error"=>"No User", "status"=>403);
  }

  echo json_encode($response);
?>
