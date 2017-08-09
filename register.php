 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['name']) && isset($_POST['username']) && isset($_POST['previllage']) && isset($_POST['password'])) {

    // receiving the post params
    $previllage = $_POST['previllage'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];

    // check if user is already existed with the same username
    if ($db->isUserExisted($username)) {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "User already existed with " . $username;
        echo json_encode($response);
    } else {
        // create a new user
        $user = $db->storeUser($name, $previllage, $username, $password);
        if ($user) {
            // user stored successfully
            $response["error"] = FALSE;
            $response["error_msg"] = "completed registration";
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

