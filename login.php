
 <?php
$pageTitle = "login";
include "includes/header.php";
$token = '';
if (isset($_SESSION["username"])) {
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
     $_SESSION["username"] = $result->fetch_assoc()["username"];
     $_SESSION["password"] =  $result->fetch_assoc()["password"];
     $_SESSION["photo"] = $result->fetch_assoc()["photo"];
     $_SESSION["admin"] = $result->fetch_assoc()["admin"];

     $conn->close();
     
     
    //  header("Location:index.php");
    }
}
}
$errors = [];
if (isset($_POST["login"])) {
    
    
    if (empty($_POST["username"])) {
        $errors["username"] = "<div class='alert alert-danger m-0 error' role='alert'>
        username is required
        </div>";
    }
    
    if (empty($_POST["password"])) {
        $errors["password"] = "<div class='alert alert-danger m-0 error' role='alert'>
        password is required
        </div>";
    }
    
    if (empty($errors)) {
        
        $username = $_POST["username"];
        $password =sha1($_POST["password"]);
        
        $conn = new mysqli('localhost', 'root', '', 'pami');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            $sql = "SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password'";
            $result = $conn->query($sql);

            if ($result->num_rows >0) {
                
                $token =time().$_SESSION["id"];
                $sqlI = " UPDATE users
                SET `token` = '$token '
                WHERE  `username` = '$username'  AND `password` = '$password' ";
                
                $res = $conn->query($sqlI);
            }
            $sql = "SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password'";
            $result = $conn->query($sql);
                      
            $result= $result->fetch_assoc();
            if ((!empty($result))) {

                $res = $conn->query($sql);
                $_SESSION["id"] = $result['id'];
                $_SESSION["email"] = $result['email'];
                $_SESSION["username"] = $result['username'];
                $_SESSION["password"] = $result['password'];
                $_SESSION["admin"] = $result['admin'];
                $_SESSION["token"] = $result['token'];

                $sqlI = " UPDATE `users`
                SET `online` = 1   WHERE  `username` = '$username' ";
                
                $conn->query($sqlI);

                $conn->close();
                if(isset($_POST['remember'])){                 
                 setcookie("auth", $username, time() + (86400 * 30), "/"); // 86400 = 1 day
                }

                header("Location:index.php");

            } else {

                $error['conn'] = "<div class='error-con alert alert-danger m-0 error' role='alert'>
               incorrect username or password
             </div>";

            }

        }
    }
}

?>



<h2 class="text-center h2-reg">Login</h3>
<form class="d-flex justify-content-center flex-column m-auto login" action="" method="post" >
 <?=isset($error['conn']) ? $error["conn"] : ""?>
 <input type="text" name="username" placeholder="Username" >
  <?=isset($errors["username"]) ? $errors["username"] : ""?>

  <input type="password" name="password" placeholder="Password">
  <?=isset($errors["password"]) ? $errors["password"] : ""?>
  <div class="d-flex flex-row">
<input id="remember" name="remember" type="checkbox" > <label for="remember">Remember me</label>
</div>
  <button type="submit" name="login" class="btn btn-outline-secondary">Login</button>

 </form>

 <?php
 $errors = [];
include "includes/footer.php";
?>