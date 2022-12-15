<?php
session_start();

$roomid = $_POST['room_to_join'];
$roomname = $_POST['room_to_join_name'];
$userid = $_POST['user_to_join'];
$adminid = $_POST['admin_to_join'];
$conn = new mysqli('localhost', 'root', '', 'pami');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {

$username_room = "SELECT `users`.`username` from `users` where `users`.`id` = $userid";
$result_name = $conn->query($username_room);
$name = $result_name->fetch_assoc()["username"];
$conn->close();
}
$myid = $_SESSION['id'];
$username = $_SESSION['username'];
$conn = new mysqli('localhost', 'root', '', 'pami');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {

    if (isset($_POST["want_to_join"]) && $_POST["want_to_join"] == 'accept') {



        $sqlusers_rooms = "UPDATE
        users_rooms users_roms    
        JOIN notifications notificationss ON `users_roms`.`id` = `notificationss`.`users_rooms_id`
        SET
        `users_roms`.`rank` = 1,
        `notificationss`.`readed` = 0
        WHERE
        `users_roms`.`user_id` = $userid AND `users_roms`.`room_id` = $roomid ";
        $result_rank = $conn->query($sqlusers_rooms);





        $sqlU = "UPDATE  `notifications` 
        SET `notifications`.`readed` = -1
        WHERE `notifications`.to_user_id = $adminid and `notifications`.`from_user_id` = $userid 
        and `notifications`.`for_admin` = 1
        and `notifications`.`notification` = '$name wants to join to $roomname'";
        $conn->query($sqlU);

        print_r(json_encode(['adminid' => $adminid, 'adminname' => $username, 'userid' => $userid, 'roomid' => $roomid, 'roomname' => $roomname, 'accept_to_user' => 'accept_to_user']));

        $conn->close();


    }
    /////////////////////////////////////////////////////////////////decline///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if (isset($_POST["want_to_join"]) && $_POST["want_to_join"] == 'decline') {

        $sqlusers_rooms = "DELETE FROM `users_rooms` WHERE `users_rooms`.`user_id` = $userid and `users_rooms`.`room_id` = $roomid ";
        
        $result_rank = $conn->query($sqlusers_rooms);

        $sqlU = "UPDATE  `notifications` 
        SET `notifications`.`readed` = -1
        WHERE `notifications`.to_user_id = $adminid and `notifications`.`from_user_id` = $userid 
        and `notifications`.`for_admin` = 1
        and `notifications`.`notification` = '$name wants to join to $roomname'";


        print_r(json_encode(['adminid' => $adminid, 'adminname' => $username, 'userid' => $userid, 'roomid' => $roomid, 'roomname' => $roomname, 'decline_to_user' => 'decline_to_user']));

        $conn->close();

    }
}
