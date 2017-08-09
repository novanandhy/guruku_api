 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id']) && isset($_POST['id_guru']) && isset($_POST['jenjang']) && isset($_POST['mapel']) && isset($_POST['biaya'])) {

    // receiving the post params
    $id = $_POST['id'];
    $id_guru = $_POST['id_guru'];
    $jenjang = $_POST['jenjang'];
    $mapel = $_POST['mapel'];
    $biaya = $_POST['biaya'];

    // create a new lowongan
    $user = $db->skill_update($id, $id_guru, $jenjang, $mapel, $biaya);
    if ($user) {
        // user stored successfully
        $response["error"] = FALSE;
        $response["error_msg"] = "completed updating skill";
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

