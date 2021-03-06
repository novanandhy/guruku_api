 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_user'])) {

    // receiving the post params
    $id_user = $_POST['id_user'];

    // create a new lowongan
    $user = $db->lowongan_get_by($id_user);
    if ($user) {
        // user stored successfully
        $response["error"] = FALSE;
        $response["user"] = $user;
        echo json_encode($response);
    } else {
        // user failed to store
        $response["error"] = FALSE;
        $response["error_msg"] = "Lowongan Kosong";
        $response["user"] = array();
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters is missing!";
    echo json_encode($response);
}
?>

