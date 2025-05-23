<?php
// include the db.php file that contains the connection code
include('db.php');

// icheck if successfull ba connection
if ($conn->connect_error) {
    // kung may error lalabas ito
    die("Connection failed: " . $conn->connect_error);
} else {
    // tignan if connected ba
    echo "Connected to the database successfully!";
}
?>
