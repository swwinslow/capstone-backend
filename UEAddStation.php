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
$password = "2a7B9z?TD";
$dbname = "south_midwest_radio";

$usernameADMIN = "csstudent";
$passwordADMIN = "DrLinRules";
$dbnameADMIN = "cs495_admin";

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

$short_name = mysql_escape_string($_POST['short_name']);
$long_name = mysql_escape_string($_POST['long_name']);
$frequency = mysql_escape_string($_POST['frequency']);
$city = mysql_escape_string($_POST['city']);
$state = mysql_escape_string($_POST['state']);
$slogan = mysql_escape_string($_POST['slogan']);
$type = "N/A";
$genre = "N/A";
$website = "N/A";
$stream = "N/A";
$active = mysql_escape_string($_POST['active']);
$user_entered = 1;

//todo check to see if the slogan has quotes around it.

$sqlEnter = "INSERT INTO stations (frequency, long_name, short_name, city, state, slogan, active, deleted, type, genre, stream, user_entered, website) VALUES ('$frequency', '$long_name', '$short_name', '$city', '$state', '$slogan', '$active', 0, '$type', '$genre', '$stream', '$user_entered', '$website')";

if ($conn->query($sqlEnter) === TRUE) {

    $sql2 = "SELECT * FROM stations WHERE long_name = '$long_name' AND short_name = '$short_name' AND stream='$stream'";
    $result = $conn->query($sql2);

    //todo look into the larger ids of the two if they are the same...

    if ($result->num_rows > 0){
        $stations = array();
        $count = 0;
        while($row = $result->fetch_assoc()) {

            $station = $row;
            array_push($stations, $station);
        }
    }
    if($user_entered == 1) {

        $getAdminUsersSQL = "SELECT * FROM users_table WHERE winner = 1";
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
