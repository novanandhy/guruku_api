 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_guru'])) {

    // receiving the post params
    $id_guru = $_POST['id_guru'];

    // create a new lowongan
    $user = $db->skill_get_by($id_guru);
    if ($user) {
        // user stored successfully
        $response["error"] = FALSE;
        $response["user"] = $user;
        echo json_encode($response);
    } else {
        // user failed to store
        $response["error"] = TRUE;
        $response["error_msg"] = "Tidak ada daftar keahlian";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters is missing!";
    echo json_encode($response);
}
?>

