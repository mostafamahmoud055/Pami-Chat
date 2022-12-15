<?php
$pageTitle = "Register";
include "includes/header.php";
if(isset($_SESSION["username"])){
    header("location:index.php");
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
        $_SESSION["username"] = $username;
        $_SESSION["password"] = $password;
        $conn->close();
   
   
        header("Location:index.php");
   }
   }
   }
$errors = [];
if (isset($_POST["register"])) {
    if (empty($_POST["username"])||!preg_match("#^[a-zA-Z0-9]+$#", $_POST["username"])) {
        $errors["username"] = "<div class='alert alert-danger m-0 error' role='alert'>
  username not valid
  </div>";
    }

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "<div class='alert alert-danger m-0 error' role='alert'>
         email not valid
      </div>";
    }

    if (empty($_POST["password"])) {
        $errors["password"] = "<div class='alert alert-danger m-0 error' role='alert'>
        password is required
  </div>";
    }

    if (empty($errors)) {

        $username = $_POST["username"];
        $password = sha1($_POST["password"]);
        $email = $_POST["email"];
        $conn = new mysqli('localhost', 'root', '', 'pami');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            $sql = "SELECT * FROM users WHERE username = '$username'";
            $result = $conn->query($sql);
            if ((!empty($result) && $result->num_rows > 0)) {
                $error['conn'] = "<div class='error-con alert alert-danger m-0 error' role='alert'>
                        user is found
                </div>";
            } else {
             
             $sql = "INSERT INTO users (`username`,`password`,`email`) VALUES('$username','$password','$email')";
            
             $res = $conn->query($sql);
            //  $last_id =  $conn->insert_id;

            $conn->close();
            header("Location:login.php");


            }

        }
    }
}

?>



<h2 class="text-center h2-reg">Register</h3>
<form class="d-flex justify-content-center flex-column m-auto register" action="" method="post" >
 <input type="text" name="username" placeholder="Username" >
  <?=isset($error['conn']) ? $error["conn"] : ""?>
  <?=isset($errors["username"]) ? $errors["username"] : ""?>
  <input type="text" name="email" placeholder="Email"  >
  <?=isset($errors["email"]) ? $errors["email"] : ""?>
  <input type="password" name="password" placeholder="Password">
  <?=isset($errors["password"]) ? $errors["password"] : ""?>
  <button type="submit" name="register" class="btn btn-outline-secondary">Register</button>
 </form>

 <?php
$errors = [];
include "includes/footer.php";
?>


