<?php
// Yes Homework App - Main Entry Point
session_start();

// Redirect logic
if (isset($_SESSION['user_id']) || isset($_COOKIE['jwt_token'])) {
    // User is logged in, redirect to worksheets
    header('Location: worksheets.php');
    exit();
} else {
    // User is not logged in, redirect to login
    header('Location: login.php');
    exit();
}
?> 