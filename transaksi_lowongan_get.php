<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_guru'])) {

    // receiving the post params
    $id_guru = $_POST['id_guru'];

    // get the user by uid_guru and password
    $user = $db->transaksi_lowongan_get($id_guru);

    if ($user != false) {
        // use is found
        $response["error"] = FALSE;
        $response["user"] = $user;
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Login credentials are wrong. Please try again!";
        echo json_encode($response);
    }
} else {
    // required GET params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters uid_guru is missing!";
    echo json_encode($response);
}
?>

