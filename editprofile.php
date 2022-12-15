
 <?php
$pageTitle = "Edit Profile";

include "includes/header.php";
if (!isset($_SESSION["username"])) {
    header("location:register.php");
}

if (isset($_POST["upload"])) {
    $id = $_POST['id'];
    if (!preg_match("#^[a-zA-Z0-9]+$#", $_POST["username"])) {
        $_SESSION["error"]["username"] = "<div class='alert alert-danger m-0 error' role='alert'>
     username is empty
     </div>";
    }

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"]["email"] = "<div class='alert alert-danger m-0 error' role='alert'>
        email is empty
        </div>";

    }

    if (isset($_SESSION["error"]) && !empty($_SESSION["error"])) {
        header("location:" . $_SERVER['HTTP_REFERER']);
    } else {
        $id = $_POST["id"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $photo_name = $_SESSION['photo'];

        if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {

            if (in_array(explode(".", $_FILES["photo"]["name"])[1], ["jpg", "png", "jpeg"])) {
                $_FILES["photo"]["name"] = time() . $_SESSION["id"] . "." . explode(".", $_FILES["photo"]["name"])[1];
                $path = getcwd() . "/imgs/";
                move_uploaded_file($_FILES["photo"]["tmp_name"], $path . $_FILES["photo"]["name"]);
                $photo_name = $_FILES["photo"]["name"];

            } else {
                $_SESSION["error"]["photo"] = "<div class='alert alert-danger m-0 error' role='alert'>
                photo must be jpg or png or jpeg
                </div>";
                header("location:" . $_SERVER['HTTP_REFERER']);
            }
        }

        if(isset($_POST["new-password"]) && !preg_match("#^[a-zA-Z0-9]+$#", $_POST["new-password"])){

         $_SESSION["error"]["new-password"] = "<div class='alert alert-danger m-0 error' role='alert'>
         Password must be letters and numbers
         </div>";
         header("location:" . $_SERVER['HTTP_REFERER']);
        }
        if(isset($_POST["new-password"])){
         $password =sha1( $_POST["new-password"]);

        }

        $conn = new mysqli('localhost', 'root', '', 'pami');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            $sql = " UPDATE users
         SET `photo` = '$photo_name ',
          `username` = '$username',
          `email` = '$email',
          `password` = '$password'
         WHERE  `id` = '$id' ";
            $result = $conn->query($sql);
            if ($result) {

                $conn->close();
                header("location:" . $_SERVER['HTTP_REFERER']);
            }

        }
    }

}else{
 header("location:myrooms.php");

}

?>


 <?php
include "includes/footer.php";
?>