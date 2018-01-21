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

$long_name = mysql_escape_string($_POST['long_name']);
$broken = mysql_escape_string($_POST['broken']);
$comment = mysql_escape_string($_POST['comment']);




        $getAdminUsersSQL = "SELECT * FROM users_table WHERE winner = 1";
        $adminUsers = $ADMINconn->query($getAdminUsersSQL);



        $details = "Station: ". $long_name . "  | How: " . $broken . " | Comment:" . $comment;

//$details = "asdf";


//        $message = "A user has reported a station. Visit'http://willshare.com/cs495/admin/frontend/#/'". $details;

        //
        // // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $fullMessage = wordwrap($details, 70, "\r\n");
        //
        // Making admins get this message
        while($rowUser = $adminUsers->fetch_assoc()) {

            mail("swwinslow@gmail.com", 'A Station has been reported', $fullMessage);
        }

        http_response_code(200);
        $response = array("error"=>"none", "email"=>"sent", "status"=>200);

echo json_encode($response);

?>
