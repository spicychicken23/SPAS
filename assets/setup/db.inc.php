<?php

require 'env.php';

global $conn;

$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if (!$conn)
{
    die("Connection failed: ". mysqli_connect_error());
}
