 <?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['id_user']) && isset($_POST['id_guru']) && isset($_POST['rating']) && isset($_POST['review'])) {

    // receiving the post params
    $id_user = $_POST['id_user'];
    $id_guru = $_POST['id_guru'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    // create a new user
        $user = $db->rating_update($id_user, $id_guru, $rating, $review);
        if ($user) {
            // user stored successfully
            //get array of rating
            $rate = $db->rating_get_by($id_guru);

            //find the average of array
            $sum_rating = array_sum($rate);
            $count_rating = count($rate);
            $average = $sum_rating / $count_rating;

            $state = $db->rating_guru_update($id_guru,$average);
            if ($state) {
                $response["error"] = FALSE;
                $response["error_msg"] = "completed updating rating";
                echo json_encode($response);
            }else{
                $response["error"] = TRUE;
                $response["error_msg"] = "Unknown error!";
                echo json_encode($response);
            }
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

