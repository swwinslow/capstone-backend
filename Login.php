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

$email = mysql_escape_string($_POST['email']);

$hashedPassword =  hash('sha512', $_POST['password']);

$sqlEnter = "SELECT id
FROM  `users_table`
WHERE email =  '$email'
AND password_hash =  '$hashedPassword'";

$result = $conn->query($sqlEnter);

if ($result->num_rows > 0){

    while($row = $result->fetch_assoc()) {
      $user = $row['id'];
    }

    $sqlCheck = "SELECT * FROM `session` WHERE user_id='$user'";

    $resultCheck = $conn->query($sqlCheck);

    if ($resultCheck->num_rows > 0){
      $session = array();

      while($row = $resultCheck->fetch_assoc()) {
        $sessoin = $row['timestamp'];
      }
    }


    $key = "d";

    $sqlSession= "INSERT INTO `session` (`session_key`, `user_id`) VALUES ('$key','$user')";
    //
    $sessionResult = $conn->query($sqlSession);

    $sqlGet = "SELECT * FROM `session` WHERE user_id='$user'";
    //
    $sesGet = $conn->query($sqlGet);
  //
    if ($sesGet->num_rows > 0){
      $array = array();

    		while($row2 = $sesGet->fetch_assoc()) {
    			$station = $row2;
    			 array_push($array, $station);
    		}
      http_response_code(200);
      $response = array("session"=> $array, "status"=>200);
    }

  } else {
    http_response_code(200);
    $response = array("error"=>"No User", "password"=> $hashedPassword, "status"=>403);
  }

  echo json_encode($response);

//check to see if the user has already created a session...

//if the timestamp is valid -> return it back
//if not, then delete and then create a new sesion keyboard

//

//  return bin2hex(openssl_random_pseudo_bytes(64));

?>
