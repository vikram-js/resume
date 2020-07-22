<?php
  session_start();
  require_once 'pdo.php';
  if(isset($_POST['cancel']))
  {
    header("Location: index.php");
    return;
  }
  if(isset($_POST['email']) && isset($_POST['pass']))
{
  if(strlen($_POST['email'])<1|| strlen($_POST['pass'])<1)
  $_SESSION['error']="Please Enter username and Password";
  else if(strpos($_POST['email'],'@')===false)
  $_SESSION['error']="username must conatin @ symbol";

  else
  {
      unset($_SESSION['username']);
      $stmt=$pdo->prepare('SELECT * FROM users WHERE email=:id');
      $stmt->execute(array(
        ':id'=>$_POST['email']
      ));
      $row=$stmt->fetch(PDO::FETCH_ASSOC);
      if(!$row)
      $_SESSION['error']='User Not Found';
      else {
        $chk=$row['password'];
        $salt='XyZzy12*_';
        $key=hash('md5',$salt.$_POST['pass']);
        if($key===$chk)
        {
          error_log("Login pass ".$_POST['email']." $chk");
          $_SESSION['username']=$_POST['email'];
          $_SESSION['user_id']=$row['user_id'];
          $_SESSION['success']='Login successful';
          header("Location: index.php");
        return;
      }
      else {
        $_SESSION['error']='Incorrect Password';
        error_log("Login fail ".$_POST['email']." $chk");
      }
    }
      }

  if(isset($_SESSION['error']))
  {
  header("Location: login.php");
  return;
  }
}
 ?>
 <html>
 <title> vikramjit singh's auto login page </title>
 <body style="padding:1em 2em; font-family:arial;">
   <?php
    if(isset($_SESSION['error']))
    {
      echo ('<p style="color:red;">'.$_SESSION['error'].'</p>');
      unset($_SESSION['error']);
    }
    ?>
   <div>
     <h1>Please Log In</h1>
     <form method='post' action='login.php'>
       <p>Username:
       <input type='text' name='email' id='email'>
     </p><p>Password:
       <input type='password' name='pass' id='pass'>
     </p><p>
       <input type='submit' value='Log In' onclick='return validate()'>
       <input type='submit' value='Cancel' name='cancel'>
     </form>
   </div>
  <script>
  function validate()
  {
    console.log('Validating...');
  try {
      addr = document.getElementById('email').value;
      pw = document.getElementById('pass').value;
      console.log("Validating addr="+addr+" pw="+pw);
      if (addr == null || addr == "" || pw == null || pw == "") {
          alert("Both fields must be filled out");
          return false;
      }
      if ( addr.indexOf('@') == -1 ) {
          alert("Invalid email address");
          return false;
      }
      return true;
  } catch(e) {
      return false;
  }
  return false;
}
</script>
