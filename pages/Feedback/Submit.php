<?php
include "../../assets/includes/header.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Kuala_Lumpur');

function getColoredStarRating($rating) {
    $coloredStars = str_repeat('★', $rating);

    return '<span style="color: #FFD700;">' . $coloredStars . '</span>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $class = $_POST["class"];
    $name = $_POST["name"];
    $comments = $_POST["comments"];
    $rating = isset($_POST["rating"]) ? $_POST["rating"] : "Not rated";
    
    $timezone = new DateTimeZone('Asia/Kuala_Lumpur');
    $now = new DateTime('now', $timezone);
    $Date = $now->format("d/m/Y H:i:s");

    $message = "Class: $class\n";
    $message .= "Student Name: $name\n";
    $message .= "Comments: $comments\n";
    $message .= "Rating: " . getColoredStarRating($rating) . "\n";
    $message .= "Date: $Date\n";

    file_put_contents("feedback_data.txt", $message, FILE_APPEND);

    echo "Class: " . $class . "<br>";
    echo "Student Name: " . $name . "<br>";
    echo "Comments: " . $comments . "<br>";
    echo "Rating: " . getColoredStarRating($rating) . "<br>";
    echo "Date: " . $Date . "<br>";
} else {
    header("Location: feedback_form.php");
    exit();
}
include "../..//assets/includes/footer.php";
?>
