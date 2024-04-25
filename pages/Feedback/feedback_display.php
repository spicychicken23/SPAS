<?php
include "../../assets/includes/header.php";

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'display':
        $feedbackData = file_get_contents("feedback_data.txt");
        echo '<h2>All Feedback Data</h2>';
        displayFeedbackEntries($feedbackData);
        break;

    case 'displayLatest':
        $feedbackData = file_get_contents("feedback_data.txt");
        $lines = explode("\n", $feedbackData);
        rsort($lines);
        echo '<h2>Latest Feedback Data</h2>';
        displayFeedbackEntries(implode("\n", $lines));
        break;

    case 'displayOldest':
        $feedbackData = file_get_contents("feedback_data.txt");
        $lines = explode("\n", $feedbackData);
        sort($lines);
        echo '<h2>Oldest Feedback Data</h2>';
        displayFeedbackEntries(implode("\n", $lines));
        break;

    default:
        $feedbackData = file_get_contents("feedback_data.txt");

        echo '<div style="margin-top: 20px; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">';
        echo '<h2>Feedback Data</h2>';

        // Display buttons for sorting
        echo '<div class="btn-group" role="group" aria-label="Sort Data">';
        echo '<button type="button" class="btn btn-secondary"><a href="feedback_display.php?action=displayLatest">Latest</a></button>';
        echo '<button type="button" class="btn btn-secondary"><a href="feedback_display.php?action=displayOldest">Oldest</a></button>';
        echo '</div>';

        echo '<hr>';

        displayFeedbackEntries($feedbackData);
        echo '</div>';
        break;
}

include "../..//assets/includes/footer.php";

// Function to display each feedback entry in a more organized way
function displayFeedbackEntries($feedbackData) {
    $entries = explode("\n\n", $feedbackData);

    foreach ($entries as $entry) {
        // Separate the feedback data into lines
        $lines = explode("\n", $entry);

        echo '<div style="margin-top: 20px; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">';
        echo '<h3>Feedback Entry</h3>';
        echo '<ul class="list-group">';
        
        // Display each line of feedback as a list item
        foreach ($lines as $line) {
            echo '<li class="list-group-item">' . nl2br($line) . '</li>';
        }

        echo '</ul>';
        echo '</div>';
        echo '<br>';
    }
}
?>
