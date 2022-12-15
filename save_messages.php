<?php
session_start();
$username = $_SESSION['username'];
$myid = $_SESSION['id'];
$conn = new mysqli('localhost', 'root', '', 'pami');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    if (isset($_POST["save_message"])) {
        $message = $_POST["message"];
        $roomid = $_POST["roomid"];
        $userid = $_POST["userid"];

        $sql_user_room_id = "SELECT id FROM `users_rooms` WHERE user_id = $userid AND room_id = $roomid";
        $user_room_id = $conn->query($sql_user_room_id);
        $user_room_id = $user_room_id->fetch_assoc();
        $user_room_id = $user_room_id['id'];

        $sqlchat = "INSERT INTO chat (`message`) VALUES ('$message')";

        if ($conn->query($sqlchat) === true) {
            $last_id = $conn->insert_id;
        }

        $sql3 = "INSERT INTO users_chats_rooms (`chat_id`, `users_rooms_id`) VALUES ('$last_id','$user_room_id')";

        if ($conn->query($sql3) === true) {
            $last_for_seen = $conn->insert_id;
        }


        $sqlU = "UPDATE  `users_rooms` 
        join `rooms`
        on `users_rooms`.`room_id` = `rooms`.`id`
        SET `users_rooms`.`readed` = 1
        where  `users_rooms`.`user_id` = $myid and `users_rooms`.`room_id` = $roomid";
        $conn->query($sqlU);

        $sqlU = "UPDATE  `users_rooms` 
        join `rooms`
        on `users_rooms`.`room_id` = `rooms`.`id`
        SET `users_rooms`.`readed` = 0
        where  `users_rooms`.`user_id` <> $myid and `users_rooms`.`room_id` = $roomid";
        $conn->query($sqlU);
        print_r(json_encode(['roomid' => $roomid, 'userid' => $userid, 'message' => $message, 'username' => $username, 'save_message' => 'save_message']));


        $conn->close();
    }
    if (isset($_POST["get_message"])) {
        $roomid = $_POST["roomid"];
        $userid = $_POST["userid"];
        
        $sqlU = "UPDATE  `users_rooms` 
        join `rooms`
        on `users_rooms`.`room_id` = `rooms`.`id`
        SET `users_rooms`.`readed` = 1
        where `users_rooms`.`readed` = 0  and `users_rooms`.`user_id` = $myid and `users_rooms`.`room_id` = $roomid";
        $conn->query($sqlU);


        $sql_count = "UPDATE  `users_rooms` 
        SET `users_rooms`.`room_count` = 0
        where `users_rooms`.`user_id` =  $userid and `users_rooms`.`room_id` = $roomid and `users_rooms`.`rank` <> 0";
        $conn->query($sql_count);


        $sql_getchat = "SELECT
            `users_rooms`.`user_id`,
            `users_rooms`.`room_id`,
            `chat`.`message`,
            DATE_FORMAT( `chat`.`date`, '%Y-%m-%d %h:%i %p'),           
            `chat`.`is_file`,
            `users`.`username`,
            `chat`.`is_img`,
            `users_chats_rooms`.`seen`
            FROM
                `users_chats_rooms`
            JOIN `users_rooms` 
            ON `users_chats_rooms`.users_rooms_id = `users_rooms`.id
            JOIN `users` 
            ON `users`.id = `users_rooms`.`user_id`
            JOIN `chat` ON `users_chats_rooms`.chat_id = `chat`.id
            WHERE
                `users_rooms`.room_id = $roomid
            ORDER BY `chat`.`date`";

        $getchat = $conn->query($sql_getchat);
        $getchat = $getchat->fetch_all();
        if (isset($getchat)) {


            $users_num = "SELECT
            COUNT(`users_rooms`.`user_id`) AS users_num
        FROM
            `users_rooms`
        WHERE
            `users_rooms`.`room_id` = $roomid";

            $users_num = $conn->query($users_num);
            $users_num = $users_num->fetch_assoc()['users_num'];

            $users_readed = "SELECT
            COUNT(`users_rooms`.`user_id`) AS users_readed
        FROM
            `users_rooms`
        WHERE
            `users_rooms`.`room_id` = $roomid and `users_rooms`.`readed` = 1";
            $users_readed = $conn->query($users_readed);
            $users_readed = $users_readed->fetch_assoc()['users_readed'];

            $room_users_id = "SELECT id FROM `users_rooms` WHERE `users_rooms`.`room_id` = $roomid";

            $room_users_id = $conn->query($room_users_id);
            $room_users_id = mysqli_fetch_all($room_users_id);

            if ($users_num == $users_readed) {
                for ($i = 0; $i < count($room_users_id); $i++) {
                    $id = $room_users_id[$i][0];
                    $sqlU = "UPDATE  `users_chats_rooms` 
                    SET `users_chats_rooms`.`seen` = 1
                    where  `users_chats_rooms`.`users_rooms_id` = $id";
                    $conn->query($sqlU);
                }
                print_r(json_encode([$getchat, 'roomid' => $roomid, 'focusSeen' => 'focusSeen']));
            } else {

                print_r(json_encode([$getchat]));
            }
        }


        $conn->close();
    }
    if (isset($_POST["focusRead"])) {
        $roomid = $_POST["roomid"];
        $userid = $_POST["userid"];
        $select_room = "SELECT `rooms`.`name` from `rooms` where `rooms`.`id` = $roomid";
        $select_room = $conn->query($select_room);
        $roomname = $select_room->fetch_assoc()['name'];

        $sqlU = "UPDATE  `users_rooms` 
            SET `users_rooms`.`room_count` = 0
            where `users_rooms`.`user_id` =  $userid and `users_rooms`.`room_id` = $roomid and `users_rooms`.`rank` <> 0";
        $conn->query($sqlU);
        $sqlU2 = "UPDATE  `users_rooms` 
            SET `users_rooms`.`readed` = 1
            where `users_rooms`.`user_id` =  $userid and `users_rooms`.`room_id` = $roomid and `users_rooms`.`rank` <> 0";
        $conn->query($sqlU2);
        print_r(json_encode(['room_count' => 'room_count', 'roomid' => $roomid, 'room_name' => $roomname]));
        $conn->close();
    }
    if (isset($_POST["focusSeen"])) {
        $roomid = $_POST["roomid"];
        $userid = $_POST["userid"];

        $users_num = "SELECT
            COUNT(`users_rooms`.`user_id`) AS users_num
        FROM
            `users_rooms`
        WHERE
            `users_rooms`.`room_id` = $roomid";

        $users_num = $conn->query($users_num);
        $users_num = $users_num->fetch_assoc()['users_num'];

        $users_readed = "SELECT
            COUNT(`users_rooms`.`user_id`) AS users_readed
        FROM
            `users_rooms`
        WHERE
            `users_rooms`.`room_id` = $roomid and `users_rooms`.`readed` = 1";
        $users_readed = $conn->query($users_readed);
        $users_readed = $users_readed->fetch_assoc()['users_readed'];

        $room_users_id = "SELECT id FROM `users_rooms` WHERE `users_rooms`.`room_id` = $roomid";

        $room_users_id = $conn->query($room_users_id);
        $room_users_id = mysqli_fetch_all($room_users_id);

        if ($users_num == $users_readed) {
            for ($i = 0; $i < count($room_users_id); $i++) {
                $id = $room_users_id[$i][0];
                $sqlU = "UPDATE  `users_chats_rooms` 
                    SET `users_chats_rooms`.`seen` = 1
                    where  `users_chats_rooms`.`users_rooms_id` = $id";
                $conn->query($sqlU);
            }

            print_r(json_encode(['roomid' => $roomid, 'focusSeen' => 'focusSeen']));
            $conn->close();
        }
    }
    if (isset($_FILES["attach"])) {
        $attach = $_FILES["attach"]["name"];
        $attach_type = $_FILES["attach"]["type"];
        $get = strpos($attach_type, "image");
        $roomid = $_POST["roomid-file"];
        $userid = $_POST["userid-file"];

        $sql_user_room_id = "SELECT id FROM `users_rooms` WHERE user_id = $userid AND room_id = $roomid";
        $user_room_id = $conn->query($sql_user_room_id);
        $user_room_id = $user_room_id->fetch_assoc();
        $user_room_id = $user_room_id['id'];
        if (strpos($attach_type, "image") !== false) {
            $sqlchat = "INSERT INTO chat (`message`,`is_img`) VALUES ('$attach',2)";
        } else {
            $sqlchat = "INSERT INTO chat (`message`,`is_file`) VALUES ('$attach',1)";
        }

        if ($conn->query($sqlchat) === true) {
            $last_id = $conn->insert_id;
        }

        $sql3 = "INSERT INTO users_chats_rooms (`chat_id`, `users_rooms_id`) VALUES ('$last_id','$user_room_id')";
        $done = $conn->query($sql3);


        if ($done) {
            $loc = getcwd() . "/imgs/" . $_FILES["attach"]["name"];
            $increment = 0;
            list($name, $ext) = explode('.', $_FILES["attach"]["name"]);
            while (file_exists($loc)) {
                $increment++;

                $_FILES["attach"]["name"] = $name . "(" . $increment . ")" . '.' . $ext;
                $loc = getcwd() . "/imgs/" . $_FILES["attach"]["name"];
            }
            move_uploaded_file($_FILES["attach"]["tmp_name"], $loc);
        }
        print_r(json_encode(['roomid' => $roomid, 'userid' => $userid, 'message' => $attach, 'username' => $username, 'save_message' => 'save_message', 'file_type' => $attach_type]));
        $conn->close();
    }
    if (isset($_POST["leave"])) {
        $userid = $_POST["userid"];
        $roomid = $_POST["roomid"];

        $sqlusers_rooms = "DELETE FROM `users_rooms` WHERE `users_rooms`.`user_id` = $userid and `users_rooms`.`room_id` = $roomid ";
        
        $leave = $conn->query($sqlusers_rooms);
        $conn->close();   
       
    }
}
