 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_guru']) && isset($_POST['nama']) && isset($_POST['alamat']) && isset($_POST['no_telp']) && isset($_POST['email']) && isset($_POST['pendidikan']) && isset($_POST['pengalaman']) && isset($_POST['deskripsi']) && isset($_POST['lat']) && isset($_POST['lng']) && isset($_POST['foto']) && isset($_POST['ipk']) && isset($_POST['kampus']) && isset($_POST['jurusan'])) {

    // receiving the post params
    $id_guru = $_POST['id_guru'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $email = $_POST['email'];
    $pendidikan = $_POST['pendidikan'];
    $pengalaman = $_POST['pengalaman'];
    $deskripsi = $_POST['deskripsi'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $foto = $_POST['foto'];
    $ipk = $_POST['ipk'];
    $kampus = $_POST['kampus'];
    $jurusan = $_POST['jurusan'];

     $user = $db->guru_update($id_guru,$nama,$alamat,$no_telp,$email,$pendidikan,$pengalaman,$deskripsi,$lat,$lng,$foto,$ipk,$kampus,$jurusan);
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