<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }
        input[type="submit"], input[type="button"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        .stars {
            font-size: 24px;
        }
        .stars span {
            color: #ccc;
        }
        .stars span.active {
            color: #FFD700;
        }
        h1 {
            text-align: center;
        }
    </style>
</head>
<body class = "background">

<?php include "../../assets/includes/header.php"; ?>
<br>
<h1 class = "lightText" >Feedback Form</h1>

<form class = "lightText" id="feedbackForm" action="submit.php" method="post">
    <label for="class">Class:</label>
    <select id="class" name="class" required>
        <option value="class1">Class 1</option>
        <option value="class2">Class 2</option>
        <option value="class2">Class 3</option>
    </select>

    <label for="name">Student Name:</label>
    <select id="name" name="name" required>
        <option value="student1">Ali</option>
        <option value="student2">Abu</option>
        <option value="student2">Alia</option>
    </select>

    <label for="comments">Comments:</label>
    <textarea id="comments" name="comments" rows="4" required></textarea>

    <label for="rating">Rating:</label>
    <div class="stars" id="rating">
        <span onclick="setRating(1)">★</span>
        <span onclick="setRating(2)">★</span>
        <span onclick="setRating(3)">★</span>
        <span onclick="setRating(4)">★</span>
        <span onclick="setRating(5)">★</span>
        <input type="hidden" id="ratingInput" name="rating" value="">
    </div>

    <input class = "btn bg-primary" type="button" value="Cancel" onclick="cancelSubmit()">
    <br><br>
    <input class = "btn bg-primary" type="submit" value="Submit" onclick="return confirmSubmit()">
</form>

<script>
    function setRating(rating) {
        var stars = document.querySelectorAll('.stars span');
        var ratingInput = document.getElementById('ratingInput');
        ratingInput.value = rating;
        stars.forEach(function (star, i) {
            star.classList.toggle('active', i < rating);
        });
    }

    function cancelSubmit() {
        // Reset the form elements
        document.getElementById("feedbackForm").reset();
        alert('Form submission canceled!');
    }

    function confirmSubmit() {
        return confirm('Are you sure you want to submit the form?');
    }
</script>
</body>
</html>
