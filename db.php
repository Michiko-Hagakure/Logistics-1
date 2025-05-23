<?php
$servername = "localhost";
$username = "root"; 
$password = "Michiko"; 
$dbname = "logs1_project_management"; 

// create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
