 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['latitude1']) && isset($_POST['latitude2']) && isset($_POST['longitude1']) && isset($_POST['longitude2'])) {

    // receiving the post params
    $lat1 = $_POST['latitude1'];
    $lat2 = $_POST['latitude2'];
    $long1 = $_POST['longitude1'];
    $long2 = $_POST['longitude2'];

    // create a new user
    $user = $db->GetDrivingDistance($lat1, $lat2, $long1, $long2);
    if ($user) {
        // user stored successfully
        $response["error"] = FALSE;
        $response["error_msg"] = 'Distance: <b>'.$user.' meters';
        echo json_encode($response);
    } else {
        // user failed to store
        $response["error"] = TRUE;
        $response["error_msg"] = "cannot get distance";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters is missing!";
    echo json_encode($response);
}
?>

