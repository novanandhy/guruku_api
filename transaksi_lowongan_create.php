 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_guru']) && isset($_POST['id_lowongan'])) {

    // receiving the post params
    $id_guru = $_POST['id_guru'];
    $id_lowongan = $_POST['id_lowongan'];

    // check if user is already existed with the same username
    if ($db->isUserAlreadyTransaction($id_guru,$id_lowongan)) {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "User already done transaction";
        echo json_encode($response);
    } else {
        // create a new user
        $user = $db->transaksi_lowongan_create($id_lowongan, $id_guru);
        if ($user) {
            // user stored successfully
            $response["error"] = FALSE;
            $response["error_msg"] = "completed creating transaction";
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

