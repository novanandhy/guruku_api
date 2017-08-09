 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_user']) && isset($_POST['id_guru']) && isset($_POST['status'])) {

    // receiving the post params
    $id_user = $_POST['id_user'];
    $id_guru = $_POST['id_guru'];
    $status = $_POST['status'];

    // create a new user
    $user = $db->booking_update($id_user, $id_guru, $status);
    if ($user) {
        // user stored successfully
        $response["error"] = FALSE;
        $response["error_msg"] = "completed updating booking";
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

