<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['lat']) && isset($_POST['long'])) {

    // receiving the GET params
    $lat = $_POST['lat'];
    $lng = $_POST['long'];

    // get the user by uid_guru and password
    $user = $db->guru_get_all($lat,$lng);

    if ($user != false) {
        // use is found
        $response["error"] = FALSE;
        $response["user"] = $user;
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Mohon isi data diri anda dengan lengkap";
        echo json_encode($response);
    }
} else {
    // required GET params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters id_guru is missing!";
    echo json_encode($response);
}
?>

