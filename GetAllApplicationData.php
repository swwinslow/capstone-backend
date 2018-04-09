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

//assigns the posted values to variables
$input = $_GET;

$stations = array();

//inserts the variables into a SQL query
$firstSQLStation = "query to find the first active stations";
$result = $conn->query($firstSQLStation);

if ($result->num_rows > 0){
    while($row = $result->fetch_assoc()) {
        $station = $row;
        array_push($stations, $station);
    }
}

$otherStations = "get all the other stations";
$result2 = $conn->query($otherStations);

if ($result2->num_rows > 0){
    while($row = $result2->fetch_assoc()) {
        $station = $row;
        array_push($stations, $station);
    }
}


$genreActive = "getting the active genres from the stations";
$genreActiveResults = $conn->query($genreActive);

if ($genreActiveResults->num_rows > 0){
    $genreActiveArray = array();
    while($row = $genreActiveResults->fetch_assoc()) {

        $genre = $row;

        array_push($genreActiveArray, $genre);
    }
}
//
$typeActive = "getting the active type from the stations";
$typeResults = $conn->query($typeActive);

if ($typeResults->num_rows > 0){
    $typeActiveArray = array();
    while($row = $typeResults->fetch_assoc()) {

        $type = $row;

        array_push($typeActiveArray, $type);
    }
}
//
$geoActive = "getting the active state from the stations";
$geoActiveResults = $conn->query($geoActive);

if ($geoActiveResults->num_rows > 0){
    $geoActiveArray = array();
    while($row = $geoActiveResults->fetch_assoc()) {

        $state = $row;

        array_push($geoActiveArray, $state);
    }
}

$popularActive = "getting the popular stations";
$popularActiveResults = $conn->query($popularActive);

if ($popularActiveResults->num_rows > 0){
    $popularActiveArray = array();
    while($row = $popularActiveResults->fetch_assoc()) {

        $station = $row;

        array_push($popularActiveArray, $station);
    }
    $response = array('status'=>200);


    $dataArray = array();
    $dataArray['active'] = $stations;
    $dataArray['genre'] = $genreActiveArray;
    $dataArray['types'] = $typeActiveArray;
    $dataArray['state'] = $geoActiveArray;
    $dataArray['popular'] = $popularActiveArray;


    $response["data"] = $dataArray;



} else {
    http_response_code(200);
    $response = array("error"=>"No Stations Found", "status"=>403);
}

echo json_encode($response);

?>
