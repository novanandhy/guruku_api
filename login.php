<?php

/**
 * @author Ravi Tamada
 * @link http://www.androidhive.info/2012/01/android-login-and-registration-with-php-mysql-and-sqlite/ Complete tutorial
 */

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['previllage'])) {

    // receiving the post params
    $username = $_POST['username'];
    $password = $_POST['password'];
    $previllage = $_POST['previllage'];

    // get the user by username and password
    $user = $db->getUserByUsernameAndPassword($username, $password, $previllage);

    if ($user != false) {
        // use is found
        $response["error"] = FALSE;
        $response["uid"] = $user["id_user"];
        echo json_encode($response);
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Login credentials are wrong. Please try again!";
        echo json_encode($response);
    }
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters username or password is missing!";
    echo json_encode($response);
}
?>

