<?php
  session_start();
  require_once 'pdo.php';
 ?>
 <html>
 <title> vikramjit singh's Resume Registry </title>
 <body style="padding:2em; font-family:Arial;">
   <div>
   <h1>Welcome to the Resume Registry</h1>
   <p>
     <?php
        if(!isset($_SESSION['username']))
        {
          echo("<p><a href='login.php'> Please log in </a></p><p>");
        }
          if(isset($_SESSION['success']))
          {
            echo ('<p style="color:green;">'.$_SESSION['success'].'</p>');
            unset($_SESSION['success']);
          }
          if(isset($_SESSION['error']))
          {
            echo ('<p style="color:red;">'.$_SESSION['error'].'</p>');
            unset($_SESSION['error']);
          }
          $stmt=$pdo->query('SELECT * FROM profile');
          if(isset($_SESSION['username']))
            echo('<p><a href="logout.php"> Logout </a></p>');
            $s=$pdo->query('SELECT * FROM profile');
            $r=$s->fetch(PDO::FETCH_ASSOC);
           if($r)
          echo('<table border="1" style="width:26em;"><tr><th>Name</th><th>Headline</th>');
          if(isset($_SESSION['username']) && $r)
          echo ('<th>Action</th>');
          echo '</tr>';
          while($row=$stmt->fetch(PDO::FETCH_ASSOC))
          {
            echo ('<tr><td>'.'<a href="view.php?id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
            echo '</td><td>'.htmlentities($row['headline']);
            if(isset($_SESSION['username']))
            echo ('</td><td>'.'<a href="edit.php?id='.$row['profile_id'].'"> edit </a> /'.'<a href="delete.php?id='.$row['profile_id'].'">delete</a>');;
            echo '</td></tr>';
          }
          echo '</table>';
          if(isset($_SESSION['username']))
        echo ('<p><a href="add.php"> Add New Entry </a></p>');
      ?>
    </p>
  </div>
  </body>
  </html>
