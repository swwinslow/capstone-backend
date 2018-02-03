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

$station_id = mysql_escape_string($_POST['station_id']);

$stationQuery = "SELECT * FROM stations WHERE id = '$station_id'";
$stationsQueryResults = $conn->query($stationQuery);

while($row = $stationsQueryResults->fetch_assoc()) {
    $confirmed_station_id =  $row['id'];
}

if(is_numeric($confirmed_station_id)){
    $voteQuery = "SELECT * FROM popular WHERE stations_id = '$confirmed_station_id'";
    $voteQueryResult = $conn->query($voteQuery);

    while($row = $voteQueryResult->fetch_assoc()) {
        $votes =  $row['votes'];
    }

    if(is_numeric($votes)){
        //update value
        $newVotes = $votes + 1;

        $voteQuery = "UPDATE `popular` SET `votes` = '$newVotes' WHERE stations_id = '$confirmed_station_id'";
        $voteQueryResult = $conn->query($voteQuery);
        http_response_code(200);
        $response = array("error"=>"none", "status"=>200);
    } else {

        $voteQuery = "INSERT INTO `popular`(`stations_id`, `votes`) VALUES ('$confirmed_station_id', 1)";
        $voteQueryResult = $conn->query($voteQuery);
        http_response_code(200);
        $response = array("error"=>"none", "status"=>200);
    }

} else {
    http_response_code(200);
    $response = array("error"=>"no station", "status"=>200);
}
$newVotes = $votes + 1;

$addingNewVotes = "INSERT INTO popular(votes) VALUE '$newVotes'";
//$newData = $conn->query($addingNewVotes);



echo json_encode($response);

?>
