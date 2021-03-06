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

    $hashedPassword =  hash('sha512', $_POST['password']);

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

  $timeLength = $currentStamp - $timstampUNIX;

  //5 minutes
  $timeLength = 600;



      if($timeLength > $difference){

        $sqlCheck = "UPDATE users_table SET `password_hash` = '$hashedPassword' WHERE id='$user'";
        $resultCheck = $conn->query($sqlCheck);

        $deleteSQL = "DELETE FROM `token` WHERE token_string = '$token'";
        $deleteSQLResult = $conn->query($deleteSQL);

        http_response_code(200);
        $response = array("data"=> "Password Updated", "time"=> $difference, "status"=>200);

      } else {
        http_response_code(403);
        $response = array("error"=>"error the time is messed up", "status"=>403);
      }


  } else {
    http_response_code(403);
    $response = array("error"=>"error. there was no user and no token", "status"=>403);
  }

  echo json_encode($response);
?>
