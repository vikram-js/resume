<?php
    session_start();
    require_once 'pdo.php';
    require_once 'head.php';
    if(!isset($_SESSION['username']))
    die("ACCESS DENIED");

    $stmt=$pdo->prepare("SELECT * FROM profile WHERE profile_id= :id");
    $stmt->execute(array(
      ':id'=>$_GET['id']
    ));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if($row===false)
    {
      $_SESSION['error']='Bad id';
      header("Location:index.php");
      return;
    }
    $fname=htmlentities($row['first_name']);
    $lname=htmlentities($row['last_name']);
    $email=htmlentities($row['email']);

    if(isset($_POST['delete']))
    {
      $s=$pdo->prepare('DELETE FROM profile WHERE profile_id= :xyz');
      $s->execute(array(
        ':xyz'=>$_POST['id']
      ));
      $_SESSION['success']='Record deleted';
      header("Location: index.php");
      return;
    }
 ?>
 <html>
 <title>vikramjit singh delete page</title>
 <body style="font-family:arial;padding:2em;">
   <h2>Are you sure you want to delete <?=$fname.' '.$lname.' '.$email?>?</h2>
   <div>
     <form method='post'>
       <input type='hidden' value="<?=$_GET['id']?>" name='id'>
       <input type='submit' value='Delete' name='delete'>
       <a href='index.php'>Cancel</a>
     </form>
   </div>
 </body>
 </html>
