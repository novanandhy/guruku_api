 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_user']) && isset($_POST['id_guru'])) {

    // receiving the post params
    $id_user = $_POST['id_user'];
    $id_guru = $_POST['id_guru'];

    // check if user is already existed with the same username
    if ($db->isUserAlreadyBook($id_user,$id_guru)) {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "anda telah memesan guru ini";
        echo json_encode($response);
    } else {
        // create a new user
        $user = $db->booking_create($id_user, $id_guru);
        if ($user) {
            // user stored successfully
            $response["error"] = FALSE;
            $response["error_msg"] = "completed creating booking";
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in registration!";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters is missing!";
    echo json_encode($response);
}
?>

