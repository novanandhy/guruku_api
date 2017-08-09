 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_user']) && isset($_POST['subjek']) && isset($_POST['description'])) {

    // receiving the post params
    $id_user = $_POST['id_user'];
    $subjek = $_POST['subjek'];
    $description = $_POST['description'];

    // create a new lowongan
    $user = $db->lowongan_create($id_user, $subjek, $description);
    if ($user) {
        // user stored successfully
        $response["error"] = FALSE;
        $response["error_msg"] = "completed registration lowongan";
        echo json_encode($response);
    } else {
        // user failed to store
        $response["error"] = TRUE;
        $response["error_msg"] = "Unknown error occurred in registration!";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters is missing!";
    echo json_encode($response);
}
?>

