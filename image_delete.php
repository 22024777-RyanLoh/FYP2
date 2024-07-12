<?php
  //Database Connection
  $conn = mysqli_connect("localhost","root","","fyp_test");
  
  //Delete image record from database
  $sql = "delete from domains where domain_id = {$_GET["id"]}";
  if($conn->query($sql)){
    
    //delete image from server
    unlink("/xampp/htdocs/fyp/Domain_picture/{$_GET["name"]}");
    
    //redirect to index page with status = 1
    header("location:edit.php?status=1");
  }else{
    
    //redirect to index page with status = 0
    header("location:edit.php?status=0");
  }

?>