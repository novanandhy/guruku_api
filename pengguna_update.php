 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_user']) && isset($_POST['nama']) && isset($_POST['alamat']) && isset($_POST['no_telp']) && isset($_POST['email']) && isset($_POST['lat']) && isset($_POST['lng']) && isset($_POST['foto'])) {

    // receiving the post params
    $id_user = $_POST['id_user'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $email = $_POST['email'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $foto = $_POST['foto'];

     $user = $db->pengguna_update($id_user,$nama,$alamat,$no_telp,$email,$lat,$lng,$foto);
    if ($user) {
        // user stored successfully
        $response["error"] = FALSE;
        $response["error_msg"] = "completed updated";
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