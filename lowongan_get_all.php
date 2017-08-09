 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];

	$user = $db->lowongan_get_all($latitude,$longitude);
	if ($user) {
	    // user stored successfully
	    $response["error"] = FALSE;
	    $response["user"] = $user;
	    echo json_encode($response);
	} else {
	    // user failed to store
	    $response["error"] = TRUE;
	    $response["error_msg"] = "Empty field for lowongan";
	    echo json_encode($response);
	}
}else{
	$response["error"] = TRUE;
	    $response["error_msg"] = "required_parameter";
	    echo json_encode($response);
}
?>

