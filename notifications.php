<?php
session_start();
$myid = $_SESSION['id'];
$conn = new mysqli('localhost', 'root', '', 'pami');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {


        if (isset($_POST["notifyForm"])  ) {
            $id_noti = $_POST["myid"];

            $sql = "SELECT
            `notifications`.`from_user_id`,
            `notifications`.`to_user_id`,
            `notifications`.`notification`,
            `rooms`.`id`,
            `rooms`.`name`,
            DATE_FORMAT(
                `notifications`.`date`,
                '%Y-%m-%d %h:%i %p'
            ) AS DATE
        FROM
            `rooms`
        JOIN `users_rooms` ON `rooms`.`id` = `users_rooms`.`room_id`
        RIGHT JOIN `notifications` ON `users_rooms`.`id` = `notifications`.`users_rooms_id`
        WHERE
            `notifications`.`to_user_id` = $id_noti AND `notifications`.`readed` <> -1 ";
                    
                    $nots = $conn->query($sql);

                    $res = $nots->fetch_all();

                    // if($note->num_rows == 0 ){
                    //     $sql = "SELECT
                    //     `notifications`.`from_user_id`,
                    //     `notifications`.`to_user_id`,
                    //     `notifications`.`notification`,
                    //     -- `rooms`.`id`,
                    //     -- `rooms`.`name`,
                    //     DATE_FORMAT(
                    //         `notifications`.`date`,
                    //         '%Y-%m-%d %h:%i %p'
                    //     ) AS DATE
                    // FROM
                    //     `notifications`
                    // -- JOIN `users_rooms` ON `users_rooms`.`id` = `notifications`.`users_rooms_id`
                    // -- JOIN `rooms` ON `users_rooms`.`room_id` = `rooms`.id
                    // WHERE
                    //     `notifications`.`to_user_id` = $id_noti  and `notifications`.`readed` <> -1   ";

                    // $note = $conn->query($sql);

                    // $res = $note->fetch_all();

                    // }


            print_r(json_encode(['result' => $res]));

            $sqlU = "UPDATE  `notifications`
                    SET `readed` = 1
                    WHERE to_user_id = $id_noti and `for_admin` <> 1";
            $conn->query($sqlU);


            $conn->close();
    }
        if (isset($_POST["notifyMessage"])  ) {
            $sql = "SELECT  `users_rooms`.`room_id`,
                        `rooms`.`name`,
                        DATE_FORMAT( `users_rooms`.`date`, '%Y-%m-%d %h:%i %p')                            
                    FROM `users_rooms`
                    join `rooms`
                    on `users_rooms`.`room_id` = `rooms`.`id`
                    where `users_rooms`.`readed` = 0 and `users_rooms`.`user_id` = $myid and `users_rooms`.`rank`<> 0" ;
                    
                    $note = $conn->query($sql);
                    $res = $note->fetch_all();

            print_r(json_encode(['result' => $res ,'userid'=>$_SESSION['id']]));



            $conn->close();
    }
    
        if (isset($_POST["room_count"])  ) {
                // $roomid = $_POST["roomid"];

            $sql_count = "SELECT  `users_rooms`.`room_count`,
                            `rooms`.`name`                       
                    FROM `users_rooms`
                    join `rooms`
                    on `users_rooms`.`room_id` = `rooms`.`id`
                    where `users_rooms`.`user_id` = $myid and `users_rooms`.`rank`<> 0" ;
                    
                    $note_count = $conn->query($sql_count);
                    $res = $note_count->fetch_all();

            print_r(json_encode(['result' => $res ]));



            $conn->close();
    }


}
