<?php
session_start();
$myid = $_SESSION['id'];
$conn = new mysqli('localhost', 'root', '', 'pami');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    if (isset($_POST["members"])) {
        $roomid = $_POST["roomid"];

        $sql = "SELECT users_rooms.user_id, users_rooms.room_id, users_rooms.rank,users.username,users.photo,users.online,users_rooms.bann
  FROM `users`
  join `users_rooms`
  on users.id = users_rooms.user_id
  where users_rooms.room_id = $roomid and users_rooms.rank != 0";
        $note = $conn->query($sql);
        $res = $note->fetch_all();

        $sql = "SELECT  users_rooms.rank
  FROM `users_rooms`
  where users_rooms.room_id = $roomid and users_rooms.user_id  = $myid";
        $note = $conn->query($sql);
        $resrank = $note->fetch_assoc()["rank"];
        print_r(json_encode(['result' => $res, 'rank' => $resrank]));

        $conn->close();

    }
    if (isset($_POST["istyping"])) {
        $roomid = $_POST["roomid"];
        $userid = $_POST["userid"];

        $sql = "SELECT users_rooms.user_id, users_rooms.room_id, users_rooms.rank,users.username,users.photo,users.online
        FROM `users`
        join `users_rooms`
        on users.id = users_rooms.user_id
        where users_rooms.room_id = $roomid and users_rooms.rank != 0";
        $note = $conn->query($sql);
        $res = $note->fetch_all();

        $sql = "SELECT  users_rooms.rank
        FROM `users_rooms`
        where users_rooms.room_id = $roomid and users_rooms.user_id  = $myid";
        $note = $conn->query($sql);
        $resrank = $note->fetch_assoc()["rank"];
        print_r(json_encode(['result' => $res, 'rank' => $resrank]));

        $conn->close();

    }
}
