 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_guru']) && isset($_POST['hari']) && isset($_POST['jam_mulai']) && isset($_POST['jam_selesai'])) {

    // receiving the post params
    $id_guru = $_POST['id_guru'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    // create a new lowongan
    $user = $db->jadwal_create($id_guru,$hari,$jam_mulai,$jam_selesai);
    if ($user) {
        // user stored successfully
        $response["error"] = FALSE;
        $response["error_msg"] = "completed creating jadwal";
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

