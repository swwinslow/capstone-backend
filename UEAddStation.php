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
$ADMINconn = new mysqli($servername, $usernameADMIN, $passwordADMIN, $dbnameADMIN);

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
$inputJSON = file_get_contents('php://input');
$input = json_decode( $inputJSON, TRUE ); //convert JSON into array

// all the database station data
$user_entered = 1;

//todo check to see if the slogan has quotes around it.

$sqlEnter = "insert query with all the station data";

if ($conn->query($sqlEnter) === TRUE) {

    $sql2 = "find out what station was put in with the same data";
    $result = $conn->query($sql2);


    if ($result->num_rows > 0){
        $stations = array();
        $count = 0;
        while($row = $result->fetch_assoc()) {

            $station = $row;
            array_push($stations, $station);
        }
    }
    if($user_entered == 1) {

        $getAdminUsersSQL = "query for user table with the email set";
        $adminUsers = $ADMINconn->query($getAdminUsersSQL);

        $message = "A new station has been added by a user! It is waiting for approval";

        // Making admins get this message
        while($rowUser = $adminUsers->fetch_assoc()) {

            mail($rowUser['email'], 'A New Station has been added... in method', $message);
        }
    }
    http_response_code(200);
    $response = array("status"=>"Station has been added", "stations" => $stations, "code"=>200);
} else {
    http_response_code(200);
    $response = array("error"=>"Added Station has failed","status"=>403);
}
echo json_encode($response);

?>
