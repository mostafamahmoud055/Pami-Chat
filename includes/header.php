<?php
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $myid = $_SESSION['id'];

    $conn = new mysqli('localhost', 'root', '', 'pami');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $sql = "SELECT *
      FROM `notifications`
      where `notifications`.to_user_id = $myid and readed = 0";
        $note = $conn->query($sql);
        $res = $note->num_rows;
        // print_r($note->fetch_all());die;
        $sql = "SELECT  `users_rooms`.`room_id`,
        `rooms`.`name`,
        DATE_FORMAT( `users_rooms`.`date`, '%Y-%m-%d %h:%i %p')
      FROM `users_rooms`
      join `rooms`
      on `users_rooms`.`room_id` = `rooms`.`id`
      where `users_rooms`.`readed` = 0 and `users_rooms`.`user_id` = $myid and `users_rooms`.`rank`<> 0";
        $note2 = $conn->query($sql);
        $res2 = $note2->num_rows;

        if(!isset($res)){
          $res = 0;
        }

        if(!isset($res2)){
          $res2 = 0;
        }
        $result_noti = $res + $res2;

        $conn->close();
    }

}
ob_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="./vendor/mervick/emojionearea/dist/emojionearea.css">
 <link rel="stylesheet" href="./layout/css/bootstrap.min.css">
 <link rel="stylesheet" href="./layout/css/all.min.css">
 <link rel="stylesheet" href="./layout/css/main.css">
 <link rel = "icon" href ="./imgs/pami.png" type = "image/x-icon">
 <title class="title"> <?= !isset($result_noti ) || $result_noti == 0 ? $pageTitle : '('.$result_noti.') '.$pageTitle ?></title>
 <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&display=swap" rel="stylesheet">

<script src="./layout/js/jquery-3.6.0.min.js"></script>


<script type="text/javascript" src="./vendor/mervick/emojionearea/dist/emojionearea.js"></script>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">


  <div class=" head-nav">
     <a style="margin-right:15%;" class="navbar-brand" href="./index.php">
      <h1>Pami</h1>
    </a>
    <?php if (isset($_GET['id']) && isset($_GET['room_id'])) {?>
    <h4 class="headTyping" style="display:none; flex: 1;color:#eee;flex-wrap: wrap;font-size: 1.2rem;">
      <div class="usertyping"></div>
      <div class="typing d-flex align-items-baseline">
        <span>
      &nbsp;typing
      </span>
          <span class="dots"></span>
          <span class="dots"></span>
          <span class="dots"></span>
      </div>
    </h4>
    <?php }?>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">


        <?php if (isset($_SESSION["username"]) && $_SESSION["admin"] == 1) {?>
        <li class="nav-item " style="padding:0px 5px ;">
        <a type="button" class="nav-link c-room w-lg-100" data-bs-toggle="modal" data-bs-target="#form-modal">
          Create Room
        </a>

        <!-- Modal -->
        <div class="modal fade" id="form-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form id="roomform" action="./index.php" method="post" class="form-control p-0" enctype="multipart/form-data" style="border:0px">
              <div class="modal-body">
                  <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Room Name</label>
                    <input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="EX::Pami Room">
                    <?=isset($_SESSION["createroom-validation"]["name"]) ? $_SESSION["createroom-validation"]["name"] : ""?>
                  </div>
                  <div class="mb-3">
                    <label for="formFile" class="form-label">Room Image</label>
                    <input class="form-control" name="photo" type="file" id="formFile">
                    <?=isset($_SESSION["createroom-validation"]["photo"]) ? $_SESSION["createroom-validation"]["photo"] : ""?>
                  </div>
                  <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">Room Details</label>
                    <textarea style="resize: none;" name="details" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    <?=isset($_SESSION["createroom-validation"]["details"]) ? $_SESSION["createroom-validation"]["details"] : ""?>
                  </div>
                </div>
              </form>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button form="roomform" type="submit" name="createroom" class="btn btn-primary">Create</button>
                </div>
            </div>
          </div>
        </div>

        </li>

<?php }?>
        <?php if (isset($_SESSION["username"])) {?>

        <li class="nav-item dropdown ">
          <a class="nav-link dropdown-toggle d-lg-block d-none" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?=$_SESSION["username"]?>
          </a>
          <ul class="dropdown-menu nave flex-column " aria-labelledby="navbarDropdownMenuLink">
            <?php if (!isset($_GET['id'])) {?>
            <li><a class="dropdown-item" href="myrooms.php?id=<?=$_SESSION["id"]?>">My Rooms</a></li>
          <?php }?>

            <li><a class="dropdown-item" href="./logout.php">Logout</a></li>
          </ul>
        </li>

       <?php } else {
    if ($pageTitle == "Register") {
        echo "<a class='nav-link text-end' href='./login.php' id='navbarDropdownMenuLink'>login</a>";
    }
    if ($pageTitle == "login") {
        echo " <a class='nav-link text-end' href='./register.php' id='navbarDropdownMenuLink'>Register</a>";
    }
    ?>
    <script>
    $('.navbar-toggler').css('display','none');
    $('#navbarNavDropdown').removeClass("collapse navbar-collapse");



  </script>

    <?php }?>
      </ul>


    </div>

    </div>
  <?php if (isset($_SESSION["username"])) {?>
    <ul class=" icons" >
      <?php if (!isset($_GET["room_id"]) && !isset($_GET["id"])) {?>

    <li class="nav-item dropdown notify">
    <a class="nav-link dropdown-toggle " href="#" id="navbarDropdownMenuLink2" role="button" data-bs-toggle="dropdown" aria-expanded="false">

    <label class="notifymsg" for="notifymsg" onclick="notifymsg()"> <i id="notifymsg" class="fa-regular fa-envelope" ></i></label>

    <span class="notify-msg" style="display:<?=$res2 > 0 ? "block" : ''?> ;"><?=(int) $res2?></span>

    </a>
    <ul class="dropdown-menu nave messages flex-column " aria-labelledby="navbarDropdownMenuLink2">


          </ul>
    </li>
      <?php }?>

    <li class="nav-item dropdown notify">
    <a class="nav-link dropdown-toggle " href="#" id="navbarDropdownMenuLink2" role="button" data-bs-toggle="dropdown" aria-expanded="false">


        <label class="notifyLabel" for="notifyLabel" onclick="notifyLabel()"> <i id="notifyLabel" class="fa-regular fa-bell "></i></label>

          <span class="notify-num" style="display:<?=$res > 0 ? "block" : ''?> ;"><?=(int) $res?></span>

    </a>
    <ul class="dropdown-menu nave notifications flex-column " aria-labelledby="navbarDropdownMenuLink2">



          </ul>
    </li>

    </ul>
    <?php }?>

</nav>



<?php
if (isset($_SESSION["createroom-validation"])) {?>

 <script>
$(document).ready(function(){
  // Show the Modal on load
  $("#form-modal").modal("show");

});
 </script>
<?php }
if (isset($_SESSION["createroom-validation"])) {
    unset($_SESSION["createroom-validation"]);
}

//  print_r($_SESSION);
?>


