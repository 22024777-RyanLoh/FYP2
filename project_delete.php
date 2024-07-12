<?php
  //Database Connection
  $conn = mysqli_connect("localhost","root","","fyp_test");
  
  //Delete image record from database
  $sql = "delete from project where Project_ID = {$_GET["id"]};";

  if($conn->query($sql)){
    
    //delete image from server
    unlink("/xampp/htdocs/fyp/Project_title/{$_GET["name"]}");
    
    //redirect to index page with status = 1
    header("location:upload.php?status=1");
  }else{
    
    //redirect to index page with status = 0
    header("location:upload.php?status=0");
  }

?>