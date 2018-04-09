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

if( !isset($_POST['station_id']) ) {
    http_response_code(400);
    $response = array("error"=>"no station in network request", "status"=>400);
} else {
    $station_id = mysql_escape_string($_POST['station_id']);


    $stationQuery = "select the station with the id";
    $stationsQueryResults = $conn->query($stationQuery);

    while ($row = $stationsQueryResults->fetch_assoc()) {
        $confirmed_station_id = $row['id'];
    }

    if (is_numeric($confirmed_station_id)) {
        $voteQuery = "see if the station is popular";
        $voteQueryResult = $conn->query($voteQuery);

        while ($row = $voteQueryResult->fetch_assoc()) {
            $votes = $row['votes'];
        }

        if (is_numeric($votes)) {
            //update value
            $newVotes = $votes + 1;

            $voteQuery = "update th query if it is aliove";
            $voteQueryResult = $conn->query($voteQuery);
            http_response_code(200);
            $response = array("error" => "n/a", "update" => "station updated", "status" => 200);
        } else {

            $voteQuery = "insert the new query for the new station";
            $voteQueryResult = $conn->query($voteQuery);
            http_response_code(200);
            $response = array("error" => "n/a", "update" => "station updated", "status" => 200);
        }

    } else {
        http_response_code(400);
        $response = array("error" => "no station", "status" => 400);
    }

    echo json_encode($response);
}

?>
