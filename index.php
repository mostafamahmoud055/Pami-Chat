<?php
$pageTitle = "Pami";
include "includes/header.php";

if (!isset($_SESSION["username"])) {
    header("location:login.php");
}

if (isset($_COOKIE["auth"])) {
    $username = $_COOKIE["auth"];
    $conn = new mysqli('localhost', 'root', '', 'pami');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $sql = "SELECT * FROM `users` WHERE `username` = '$username'";
        $result = $conn->query($sql);
        if ((!empty($result) && $result->num_rows > 0)) {
            $_SESSION["id"] = $result->fetch_assoc()["id"];
            $_SESSION["email"] = $result->fetch_assoc()["email"];
            $_SESSION["username"] = $result->fetch_assoc()["username"];
            $_SESSION["password"] = $result->fetch_assoc()["password"];
            $_SESSION["photo"] = $result->fetch_assoc()["photo"];
            $_SESSION["admin"] = $result->fetch_assoc()["admin"];


            header("Location:index.php");
        }
    }
}

if (isset($_POST["createroom"])) {

    if (empty($_POST["name"]) || !preg_match("#^[a-zA-Z0-9]+$#", $_POST["name"])) {
        $_SESSION["createroom-validation"]["name"] = "<div class='alert alert-danger m-0 error' role='alert'>
          Name is required
          </div>";
    }
    if (empty($_POST["details"])) {
        $_SESSION["createroom-validation"]["details"] = "<div class='alert alert-danger m-0 error' role='alert'>
          Details is required
          </div>";
    }
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {

        if (in_array(explode(".", $_FILES["photo"]["name"])[1], ["jpg", "png", "jpeg"])) {

            $_FILES["photo"]["name"] = time() . $_SESSION["id"] . "." . explode(".", $_FILES["photo"]["name"])[1];
            $path = getcwd() . "/imgs/";
            move_uploaded_file($_FILES["photo"]["tmp_name"], $path . $_FILES["photo"]["name"]);

        } else {
            $_SESSION["createroom-validation"]["photo"] = "<div class='alert alert-danger m-0 error' role='alert'>
          photo must be jpg or png or jpeg
          </div>";

            header("location:index.php");
        }
    }
    
    if (!empty($_SESSION["createroom-validation"])) {
        
        header("location:index.php");
    } else {
        $name = $_POST["name"];
        $details = $_POST["details"];
        $photo = $_FILES["photo"]["name"];
        unset($_POST["createroom"]);
        $conn = new mysqli('localhost', 'root', '', 'pami');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            
            $sql = "INSERT INTO `rooms` (`rooms`.`name`,`rooms`.`details`,`rooms`.`photo`) VALUES ('$name','$details','$photo')";
            $conn->query($sql);         
            
            $user_id = $_SESSION["id"];
            $room_id =  $conn->insert_id;
 
            $sql = "INSERT INTO `users_rooms` (`users_rooms`.`user_id`,`users_rooms`.`room_id`,`users_rooms`.`rank`) VALUES ('$user_id','$room_id',2)";
            $conn->query($sql);

            $conn->close();
            header("location:index.php");
        }

    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
$rooms_length = '';
$rooms = '';
$conn = new mysqli('localhost', 'root', '', 'pami');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {


    $sql = "SELECT rooms.*, users_rooms.rank, users_rooms.user_id 
    FROM `rooms` 
    join `users_rooms` 
    on rooms.id = users_rooms.room_id 
    where users_rooms.rank = 2";
    $result = $conn->query($sql);
    $rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $rooms_length = count($rooms);

    


    $conn->close();
}

?>

<div class="row p-2 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
 <?php for ($i = 0; $i < $rooms_length; $i++) {?>


  <div class="col">
    <div class="card h-100">
      <div class="d-flex flex-column mt-auto mb-auto">
      <div class="img-fluid text-center">
      <img src="<?=empty($rooms[$i]['photo']) || !file_exists("./imgs/" . $rooms[$i]['photo']) ? './imgs/pami_room.png' : './imgs/' . $rooms[$i]['photo']?>" alt="" class="card-img-top room-img " >
      </div>
      </div>
      <div class="card-body text-center position-relative mb-0">
        <h5 class="card-title"><?=$rooms[$i]['name']?></h5>
        <p class="card-text"><?=$rooms[$i]['details']?></p>
   
      </div>

      <div class="card-footer text-center">
        <?php
        
        
        $conn = new mysqli('localhost', 'root', '', 'pami');
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        } else {
        $user_id = $_SESSION["id"];
        $room_id = $rooms[$i]['id'];

        $sql = "SELECT rank FROM `users_rooms` WHERE `user_id` = $user_id and `room_id`= $room_id ";
        $result_rank = $conn->query($sql);

    
        if ((!empty($result_rank) && $result_rank->num_rows > 0)) {
        $rank = $result_rank->fetch_assoc()["rank"];

        if( $rank == 1 || $rank == 2 ){ ?>
            <a href="myrooms.php?id=<?=$user_id?>&room_id=<?=$room_id?>">
                <input type="submit" value="view" class="btn-color-view w-50"></input> 
            </a>        
        
        <?php } else if( $rank == 0){ ?>
            <form action="" method="post" id="cancel_room" class="cancel_room">
                <input type="hidden" name="user_id_cancel" id="user_id_cancel" value="<?=$_SESSION["id"]?>">
                <input type="hidden" name="room_id_cancel" id="room_id_cancel" value="<?=$rooms[$i]['id']?>">
                <input type="hidden" name="admin_id_cancel" id="admin_id_cancel" value="<?=$rooms[$i]['user_id']?>">
                <input type="submit" name="cancel" value="wait" class="btn-color-wait w-50"></input>
            </form>

<?php } else if($rank == -1 ) { ?>
        

        <form class="join_room" id="join_room">
            <input type="hidden" name="user_id_join" id="user_id_join" value="<?=$_SESSION["id"]?>">
            <input type="hidden" name="room_id_join" id="room_id_join" value="<?=$rooms[$i]['id']?>">
            <input type="hidden" name="room_name_join" id="room_name_join" value="<?=$rooms[$i]['name']?>">
            <input type="hidden" name="admin_id_join" id="admin_id_join" value="<?=$rooms[$i]['user_id']?>">
            <input type="submit" value="join" class="btn-color-join w-50"></input>
        </form> 

        <?php  }} else { ?>
        

        <form class="join_room" id="join_room">
            <input type="hidden" name="user_id_join" id="user_id_join" value="<?=$_SESSION["id"]?>">
            <input type="hidden" name="room_id_join" id="room_id_join" value="<?=$rooms[$i]['id']?>">
            <input type="hidden" name="room_name_join" id="room_name_join" value="<?=$rooms[$i]['name']?>">
            <input type="hidden" name="admin_id_join" id="admin_id_join" value="<?=$rooms[$i]['user_id']?>">
            <input type="submit" value="join" class="btn-color-join w-50"></input>
        </form>

      <?php }?>
      </div>

    </div>
  </div>

  <?php }}?>

</div>



 <?php

include "includes/footer.php";


?>