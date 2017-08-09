 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['harga_min']) && isset($_POST['harga_mid']) && isset($_POST['harga_max'])
	 && isset($_POST['pengalaman_min']) && isset($_POST['pengalaman_mid']) && isset($_POST['pengalaman_max'])
	  && isset($_POST['jarak_min']) && isset($_POST['jarak_mid']) && isset($_POST['jarak_max'])
	   && isset($_POST['param_harga']) && isset($_POST['param_pengalaman']) && isset($_POST['param_jarak'])
	    && isset($_POST['jenjang']) && isset($_POST['mapel']) && isset($_POST['hari']) && isset($_POST['kelamin'])
	     && isset($_POST['latitude']) && isset($_POST['longitude'])) {
	
	$harga_min = $_POST['harga_min'];
	$harga_mid = $_POST['harga_mid'];
	$harga_max = $_POST['harga_max'];
	$pengalaman_min = $_POST['pengalaman_min'];
	$pengalaman_mid = $_POST['pengalaman_mid'];
	$pengalaman_max = $_POST['pengalaman_max'];
	$jarak_min = $_POST['jarak_min'];
	$jarak_mid = $_POST['jarak_mid'];
	$jarak_max = $_POST['jarak_max'];
	$param_harga = $_POST['param_harga'];
	$param_pengalaman = $_POST['param_pengalaman'];
	$param_jarak = $_POST['param_jarak'];
	$jenjang = $_POST['jenjang'];
	$mapel = $_POST['mapel'];
	$hari = $_POST['hari'];
	$kelamin = $_POST['kelamin'];
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];

	$user = $db->fuzzy_search($harga_min,$harga_mid,$harga_max,$pengalaman_min,$pengalaman_mid,$pengalaman_max,$jarak_min,$jarak_mid,$jarak_max,$param_harga,$param_pengalaman,$param_jarak,$jenjang,$mapel,$hari,$kelamin,$latitude,$longitude);
	if ($user) {
	    // user stored successfully
	    $response["error"] = FALSE;
	    $response["user"] = $user;
	    echo json_encode($response);
	} else {
	    // user failed to store
	    $response["error"] = TRUE;
	    $response["error_msg"] = "Empty field for search";
	    echo json_encode($response);
	}
}else{
	$response["error"] = TRUE;
	    $response["error_msg"] = "required_parameter";
	    echo json_encode($response);
}
?>

