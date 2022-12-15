
 <?php
ob_start();
$pageTitle = "Logout";
include "includes/header.php";
$conn = new mysqli('localhost', 'root', '', 'pami');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {

    $sqlI = " UPDATE `users`
         SET `online` = 0   WHERE  `username` = '$username' ";

    $conn->query($sqlI);

}
$conn->close();

setcookie("auth", $username, time() + (-86400 * 30), "/"); // 86400 = 1 day
unset($_SESSION);
session_destroy();
header("location:login.php");
include "includes/footer.php";
ob_end_flush();
?>