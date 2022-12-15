
<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'pami');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    //////////////////////////////////////////////////////////////////////////////////
    if (isset($_POST["join"])) {

        $room_id = $_POST["roomid"];
        $user_id = $_POST["userid"];
        $room_name = $_POST["roomname"];
        $admin_id = $_POST["adminid"];

        $username = $_SESSION["username"];

        // $sqlusers_noti = "SELECT `users_rooms`.`user_id` from `users_rooms` 
        // where `users_rooms`.`user_id` =$user_id and `users_rooms`.`room_id` = $room_id ";
        // $res_noti = $conn->query($sqlusers_noti);



        // if($res_noti->num_rows > 0){
        //     $sqlusers_rooms = "UPDATE
        //     users_rooms users_roms    
        //     JOIN notifications notificationss ON `users_roms`.`id` = `notificationss`.`users_rooms_id`
        //     SET
        //     `users_roms`.`rank` = 0,
        //     `notificationss`.`readed` = 0
        //     WHERE
        //     `users_roms`.`user_id` = $user_id AND `users_roms`.`room_id` = $room_id ";
        //     $result_rank = $conn->query($sqlusers_rooms);
        // }else{
            $sqlusers_rooms = "INSERT INTO users_rooms (`user_id`,`room_id`,`rank`) VALUES ($user_id,$room_id,0)";

            if ($conn->query($sqlusers_rooms) === true) {
                $last_id = $conn->insert_id;
            }
    
            $sqlnotifications = "INSERT INTO notifications (`notification`,`from_user_id`,`to_user_id`,`users_rooms_id`,for_admin) VALUES ('$username wants to join to $room_name','$user_id','$admin_id','$last_id',1)";
    
            $conn->query($sqlnotifications);
        // }



        print_r(json_encode(["adminid" => $admin_id, 'join' => 'join']));

        $conn->close();
    }
//////////////////////////////////////////////////////////////////////////////////////
    if (isset($_POST["cancel"])) {

        $admin_id = $_POST["adminid"];
        $user_id = $_POST["userid"];
        $room_id = $_POST["roomid"];

        $sqlusers_rooms = "DELETE FROM `users_rooms` WHERE `users_rooms`.`user_id` = $user_id and `users_rooms`.`room_id` = $room_id ";
        
        $result_rank = $conn->query($sqlusers_rooms);
        // $sqlusers_rooms = "UPDATE
        // users_rooms users_roms    
        // JOIN notifications notificationss ON `users_roms`.`id` = `notificationss`.`users_rooms_id`
        // SET
        // `users_roms`.`rank` = -1,
        // `notificationss`.`readed` = -1
        // WHERE
        // `users_roms`.`user_id` = $user_id AND `users_roms`.`room_id` = $room_id AND `users_roms`.`rank` = 0";


        print_r(json_encode(["adminid" => $admin_id, "cancel" => "cancel"]));

        $conn->close();
    }
    ////////////////////////////////////////////accept_to_user///////////////////decline_to_user////////////////////////////////////////////////////////





    if (isset($_POST["accept_to_user"]) && $_POST["accept_to_user"] == "accept_to_user") {

        $admin_id = $_POST["adminid"];
        $admin_name = $_POST["adminname"];
        $room_id = $_POST["roomid"];
        $user_id = $_POST["userid"];
        $room_name = $_POST["roomname"];


        $concat = 'you accepted to join to ' . $room_name;


        $select_user_room_id =" SELECT
        `users_rooms`.`id`
        FROM
        `users_rooms`
        WHERE
        `users_rooms`.`user_id` = $user_id
        and
        `users_rooms`.`room_id` = $room_id";
  

        $select_user_room_id =  $conn->query($select_user_room_id);

        $user_room_id =  $select_user_room_id->fetch_assoc()["id"];


        $sqlnotifications = "INSERT INTO notifications (`notification`,`from_user_id`,`to_user_id`,`readed`,`users_rooms_id`)
                                            VALUES ('$concat' ,'$admin_id','$user_id',0,'$user_room_id')";

        $conn->query($sqlnotifications);
     
        print_r(json_encode(['accept_to_user' => 'accept_to_user']));


        $conn->close();
    }
        ///////////////////////////////////////////////////////////////decline_to_user///////////////////////////////////////////////////

    if (isset($_POST["decline_to_user"]) && $_POST["decline_to_user"] == "decline_to_user") {

        $admin_id = $_POST["adminid"];
        $admin_name = $_POST["adminname"];
        $room_id = $_POST["roomid"];
        $user_id = $_POST["userid"];
        $room_name = $_POST["roomname"];


            $concat = 'you declined to join to ' . $room_name;
        


    $sqlnotifications = "INSERT INTO notifications (`notification`,`from_user_id`,`to_user_id`,`readed`,`users_rooms_id`)
                                            VALUES ('$concat' ,'$admin_id','$user_id',0,null)";

    $conn->query($sqlnotifications);

     
        print_r(json_encode(['decline_to_user' => 'decline_to_user']));
    

    $conn->close();


     }


     //////////////////////////////////////////////////////////bann////////////////////////////////////////////////////////////////////
    if (isset($_POST["bann"])) {

        $room_id = $_POST["roomid"];
        $user_id = $_POST["userid"];
        $admin_id = $_POST["adminid"];


        $sqlusers_rooms_bann = "UPDATE `users_rooms` 
                                SET `users_rooms`.`bann` = 1 
                                where `users_rooms`.`user_id` = $user_id and `users_rooms`.`room_id` = $room_id ";

        $conn->query($sqlusers_rooms_bann);
        

        $room_name = "SELECT `rooms`.`name` FROM `rooms` WHERE `rooms`.`id` = $room_id";
        $room_name = $conn->query($room_name);

        $room_name = $room_name->fetch_assoc()["name"];

        $concat = 'you have been banned from ' . $room_name;

        
    $sqlbann = "INSERT INTO notifications (`notification`,`from_user_id`,`to_user_id`,`readed`,`users_rooms_id`)
                                            VALUES ('$concat' ,'$admin_id','$user_id',0,null)";

    $conn->query($sqlbann);

     
        print_r(json_encode(['userid'=>$user_id ,'roomid'=>$room_id,'bann'=>'bann']));
    

    $conn->close();


     }
     //////////////////////////////////////////////////////////banned////////////////////////////////////////////////////////////////////
    if (isset($_POST["banned"])) {

        $room_id = $_POST["roomid"];
        $user_id = $_POST["userid"];
        $admin_id = $_POST["adminid"];


        $sqlusers_rooms_bann = "UPDATE `users_rooms` 
                                SET `users_rooms`.`bann` = 0 
                                where `users_rooms`.`user_id` = $user_id and `users_rooms`.`room_id` = $room_id ";

        $conn->query($sqlusers_rooms_bann);
        

        $room_name = "SELECT `rooms`.`name` FROM `rooms` WHERE `rooms`.`id` = $room_id";
        $room_name = $conn->query($room_name);

        $room_name = $room_name->fetch_assoc()["name"];

        $concat = 'bann removed from ' . $room_name ;


    $sqlbann = "INSERT INTO notifications (`notification`,`from_user_id`,`to_user_id`,`readed`,`users_rooms_id`)
                                            VALUES ('$concat' ,'$admin_id','$user_id',0,null)";

    $conn->query($sqlbann);

     
        print_r(json_encode(['userid'=>$user_id ,'roomid'=>$room_id,'banned'=>'banned']));
    

    $conn->close();


     }
     //////////////////////////////////////////////////////////removed////////////////////////////////////////////////////////////////////
    if (isset($_POST["remove"])) {

        $room_id = $_POST["roomid"];
        $user_id = $_POST["userid"];

        $admin_id = $_POST["adminid"];

        $sqlusers_rooms = "DELETE FROM `users_rooms` WHERE `users_rooms`.`user_id` = $user_id and `users_rooms`.`room_id` = $room_id ";
        
        $sqlusers_rooms = $conn->query($sqlusers_rooms);
        

        $room_name = "SELECT `rooms`.`name` FROM `rooms` WHERE `rooms`.`id` = $room_id";
        $room_name = $conn->query($room_name);

        $room_name = $room_name->fetch_assoc()["name"];

        $concat = 'you removed from ' . $room_name ;


    $sqlremove = "INSERT INTO notifications (`notification`,`from_user_id`,`to_user_id`,`readed`,`users_rooms_id`)
                                            VALUES ('$concat' ,'$admin_id','$user_id',0,null)";

    $conn->query($sqlremove);

     
        print_r(json_encode(['userid'=>$user_id,'roomid'=>$room_id ,'removed'=>'removed']));
    

    $conn->close();


     }
     //////////////////////////////////////////////////////////search_rooms////////////////////////////////////////////////////////////////////
    if (isset($_POST["search_rooms"])) {

        $val = $_POST["val"];
        $user_id = $_POST["user_id"];

        $sqlroom_name = "SELECT
        `rooms`.`id`,
        `rooms`.`name`,
        `rooms`.`photo`
    FROM
        `rooms`
    JOIN `users_rooms` ON `rooms`.`id` = `users_rooms`.`room_id`
    WHERE
        `users_rooms`.`user_id` = '$user_id' AND `rooms`.`name` LIKE '%$val%';";
        
        $sqlroom_name = $conn->query($sqlroom_name);

        $sqlroom_name = $sqlroom_name->fetch_all();
        
     
        print_r(json_encode($sqlroom_name));
    

    $conn->close();


     }
}

?>

