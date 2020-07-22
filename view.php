<?php
  session_start();
  require_once 'pdo.php';
  require_once 'utils.php';
  require_once 'head.php';
  $stmt=$pdo->prepare('SELECT * FROM profile where profile_id=:id');
  $stmt->execute(array(
    ':id'=>$_GET['id']
  ));
  $row=$stmt->fetch(PDO::FETCH_ASSOC);
  $fname=$row['first_name'];
  $lname=$row['last_name'];
  $email=$row['email'];
  $hdline=$row['headline'];
  $summ=$row['summary'];

  $positions=loadPos($pdo,$_GET['id']);
  $educations=loadEdu($pdo,$_GET['id']);
 ?>
 <html>
 <title>vikramjit singh Resume Viewer</title>
 <body style="font-family:arial;padding:3em;">
   <h1> Profile Information </h1>
   <div>
     <p>
       First Name: <?=$fname?>
     </p><p>
       Last Name: <?=$lname?>
     </p><p>
       Email: <?=$email?>
     </p><p>
     Headline:</br>
     <?=$hdline?>
   </p><p>Summary</br>
     <?=$summ?>
   </p><p>
     <?php

      $e=0;
      if($educations)
      echo ('<p>Education :<ul>');
      foreach($educations as $edu){
        $e++;
        echo('<li>'.$edu['year'].': '.$edu['name'].'</li>');
      }
      echo('</ul></p>');
      $pos=0;
      if($positions)
      echo ('<p>Position :<ul>');
      foreach($positions as $position){
        $pos++;
        echo('<li>'.$position['year'].': '.$position['description'].'</li>');
      }
      echo('</ul></p>');
      ?>
     <a href="index.php">Done</a>
   </div>
 </body>
 </html>
