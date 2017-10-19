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

$session_key = mysql_escape_string($_POST['session_key']);

$session_id = mysql_escape_string($_POST['session_id']);


$sqlEnter = "SELECT id
FROM  `session`
WHERE id =  '$session_id'
AND session_key =  '$session_key'";

$result = $conn->query($deleteSQL);


?>
