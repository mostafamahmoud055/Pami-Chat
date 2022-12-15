<?php
$pageTitle = "My Rooms";

include "includes/header.php";

$rooms_length = '';
$rooms = '';
$filename = null;
if (!isset($_SESSION['username'])) {
  header("location:register.php");
}
if (!isset($_GET['id'])) {
  header("location:index.php");
}

if (isset($_GET['room_id'])) {
  $room_id = $_GET['room_id'];
}

if (isset($_GET['id'])) {
  $id = $_GET['id'];
}

$user_id = $_SESSION['id'];

// if (isset($_POST["submit_file"])) {

// }

$conn = new mysqli('localhost', 'root', '', 'pami');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $sql = "SELECT * FROM `users` WHERE `id` = '$user_id'";
  $result = $conn->query($sql);
  $DB_return = $result->fetch_assoc();

  //print_r( $DB_return);die;

  if ($result->num_rows > 0) {
    $_SESSION["id"] = $DB_return["id"];
    $_SESSION["username"] = $DB_return["username"];
    $_SESSION["password"] = $DB_return["password"];
    $_SESSION["email"] = $DB_return["email"];
    $_SESSION['photo'] = $DB_return["photo"];
  }

  $sql_rooms = "SELECT rooms.id , rooms.name , rooms.photo , rooms.details ,users_rooms.rank,users_rooms.bann
                   FROM `rooms`
                  JOIN `users_rooms`
                  ON  rooms.id =  users_rooms.room_id
                  WHERE users_rooms.user_id = $user_id  AND users_rooms.rank > 0";
  $result_rooms = $conn->query($sql_rooms);
  $rooms = $result_rooms->fetch_all();
  
  // echo "<pre>";
  // print_r($rooms);die;
  // echo "</pre>";
  $rooms_length = count($rooms);


  $conn->close();
}
?>


<!-- ///////////////////////////***********************************************///////////////////// */ -->


<!-- Modal -->
<div class="modal fade" id="members-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Settings Room</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="roomform" action="./index.php" method="post" class="form-control p-0" enctype="multipart/form-data" style="border:0px">

        <!-- <div class="col-12">
          <input type="text" class="form-control " placeholder="Member Search" aria-label="Username" aria-describedby="basic-addon1" style=" font-size: 1rem; background-color: #ddd;">
        </div> -->

        <div class="modal-body px-4">

        </div>
      </form>
    </div>
  </div>
</div>

<!-- ///////////////////////////***********************************************///////////////////// */ -->
<div class="profile">
  <div class="profile-title"><i class="fa-solid fa-arrow-left"></i> <span>Profile</span></div>
  <form class="d-flex justify-content-center flex-column m-auto " id="myform" action="editprofile.php" method="post" enctype="multipart/form-data">
    <div class="text-center img-round position-relative">
      <img class="img-fluid rounded " src="<?= !empty($_SESSION['photo']) && file_exists("./imgs/" . $_SESSION['photo']) ? "./imgs/" . $_SESSION['photo'] : 'imgs/avatar.png' ?>" alt="">

      <div class="file">
        <label for="img-prof"> <i id="img-prof" class="fa fa-2x fa-camera" style=" font-size: 1rem;"></i></label>
        <input form="myform" id="inputTag" type="file" name="photo" />
        <?= isset($_SESSION["error"]["photo"]) ? $_SESSION["error"]["photo"] : "" ?>
      </div>

    </div>
    <input type="hidden" name="id" placeholder="Username" value="<?= $_GET['id'] ?>">
    <input type="text" name="username" placeholder="Username" value="<?= $_SESSION['username'] ?>">
    <?= isset($_SESSION["error"]["username"]) ? $_SESSION["error"]["username"] : "" ?>
    <input type="email" name="email" placeholder="Email" value="<?= $_SESSION['email'] ?>">
    <?= isset($_SESSION["error"]["email"]) ? $_SESSION["error"]["email"] : "" ?>
    <input type="hidden" name="password" placeholder="password" value="<?= $_SESSION['password'] ?>">
    <input type="password" name="new-password" placeholder="New password">
    <?= isset($_SESSION["error"]["new-password"]) ? $_SESSION["error"]["new-password"] : "" ?>


    <input type="submit" name="upload" class="btn btn-outline-secondary">
  </form>
</div>


<div class="rooms d-flex">

  <!-- ////////////////////////////////////////////////////////////////////////////////////////////////////// -->
  <div class="sidebar">
    <div class="sidebar-title row">
      <div class="col-2 px-0">
        <img class="img-fluid open-profile" src="<?= !empty($_SESSION['photo']) && file_exists("./imgs/" . $_SESSION['photo']) ? "./imgs/" . $_SESSION['photo'] : 'imgs/avatar.png' ?>" alt="">
      </div>
      <div class="col-10">
        <input class="Room-Search form-control " type="text" placeholder="Room Search" aria-label="Username" aria-describedby="basic-addon1">
      </div>
    </div>
    <span class="open-close">
      <i class="fa-solid fa-bars open-close"></i>
    </span>
   
    <div class="sql_search">
    <?php for ($i = 0; $i < $rooms_length; $i++) { ?>

      <div class="row py-4 px-1 room-chat align-items-start" data-id=<?=$rooms[$i]['0']?>>
        <a class="col-7 " style="padding-right: 0;position:relative" href='?id=<?= $id ?>&room_id=<?= $rooms[$i]['0'] ?> '>

          <img src="<?= empty($rooms[$i]['2']) || !file_exists("./imgs/" . $rooms[$i]['2']) ? './imgs/pami_room.png' : './imgs/' . $rooms[$i]['2'] ?>" style="vertical-align: sub;" class="m-0 pr-0 room-img ">

          <span class="room-name" style="position:relative ;" class="mx-md-1"><?= $rooms[$i]['1'] ?>
          </span>

          <span style="display: none;" class="room-count <?= $rooms[$i]['1'] ?> col-md-2 col-3">0</span>
        </a>


        <div class=" col-1 text-center" style="padding: 0;flex: 1;align-self: center;">
          <a type="button" class="nav-link members" data-roomid="<?= $rooms[$i]['0'] ?>" data-bs-toggle="modal" data-bs-target="#members-modal">
            <i class="fa-solid fa-users members-icon"></i>
          </a>
        </div>
        <div class=" col-1 text-center leave-icon" style="padding-left: 0;flex: 1;">

          <div class="dropdown">
            <button class="dropdown-toggle leave-room" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="leave">.</span><br>
          <span class="leave">.</span><br>
          <span class="leave">.</span>
            </button>
            <ul class="dropdown-menu left-room">
              <li><a class="dropdown-item leave-btn" href="#">Leave</a></li>

            </ul>
          </div>
        </div>

      </div>

      <hr class="w-75 my-0" style="margin-left:auto">
      
      <?php } ?>
    </div>

  </div>


  <div class="chat" style=" background: url(imgs/LiveChat.png) no-repeat; background-size: contain;  background-position: center;">
    <div class="d-flex text-secondary text-center justify-content-center align-items-center w-sm-75 p-5 h-25 fs-2 position-absolute">No chat selected. Select one from <br> the queue and start chatting</div>
    <?php if (isset($room_id) && !empty($room_id)) { ?>
      <div class="chat-area" style="z-index: 2;">
      <div class="loader-container" style="width: 100%; height: 100%;background-color: rgba(255, 255, 255, .3);display: flex;justify-content: center; align-items: center;">
        <img src="imgs/loader.gif" style="width: 150px;height: 150px; border-radius: 50%">
      </div>
      

      </div>


      <div class="text-box d-flex">
   
      <?php for ($i = 0; $i < $rooms_length; $i++) { 
        if( $rooms[$i][5] == 0 && $rooms[$i][0] == $_GET['room_id'] ){ ?>
        <div class="fileChat">
          <label for="attach"><i class="attach fa-solid fa-paperclip"></i></label>
          <input form="submit_file_form" id="attach" type="file" name="attach" onchange="readURL(this);" />
        </div>
        <div class="d-flex" style="flex:1; align-items: center;">
          <form id="chat_form" action="" method="post">
            <input id="userid" type="hidden" name="userid" value="<?= $_GET['id'] ?>" />
            <input id="roomid" type="hidden" name="roomid" value="<?= $_GET['room_id'] ?>" />


            <textarea id="message" placeholder="Type Message" onkeydown="pressed(event)"></textarea>

          </form>
          <div class="send">
            <label for="submit"><i class="submitlabel fa-regular fa-paper-plane"></i></label>
            <input form="chat_form" id="submit" type="submit" name="submit" />
          </div>

        </div>

        <?php } } ?>
      </div>
  </div>
<?php } ?>
</div>


</div>



<div class="modal" id="image_preview" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body preview text-center">

      </div>
      <div class="modal-footer">
        <form id="submit_file_form" action="" method="post" enctype="multipart/form-data">

          <input id="userid" type="hidden" name="userid-file" value="<?= $_GET['id'] ?>" />
          <input id="roomid" type="hidden" name="roomid-file" value="<?= $_GET['room_id'] ?>" />

          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" id="submit_file" name="submit_file" class="btn btn-primary">Send</button>
        </form>
      </div>

    </div>
  </div>
</div>
<div class="modal" id="open_photo" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body preview_photo text-center">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

      </div>

    </div>
  </div>
</div>


<?php
unset($_SESSION["error"]);

?>





<?php
include "includes/footer.php";
?>