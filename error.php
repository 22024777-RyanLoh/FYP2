<?php

$username = null;
$username_error = null;
$password = null;
$password_error = null;

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(empty(trim($username))){
        $username_error = "Field username is empty";
    }else{
        if(empty(trim($password))){
        $password_error = "Password field is empty";
        }
    }
}

?>


<?php
        if($username_error != null){
            ?> <style>.username-error{display: block}</style> <?php
        }
        if($password_error != null){
            ?> <style>.password-error{display: block}</style> <?php
        }
    ?>



//<div class="input-box">
//           <input type="password"id="password" name="password" value="<?php echo $password ?>" placeholder="Password">
//          <p class="error password-error">
//              <?php echo $password_error?>
//          </p>
//<div/>