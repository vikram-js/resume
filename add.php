
<?php
//start the session
  session_start();
  //include files
  require_once 'pdo.php';
  require_once 'utils.php';
  require_once 'head.php';
  //check if user is logged in
  if(!isset($_SESSION['username']))
  die("ACCESS DENIED");
  //check if user hasnt cancelled
  if(isset($_POST['cancel']))
  {
    header("Location:index.php");
    return;
  }
  //check if user has submitted the form
  if(isset($_POST['add']))
  {
    //check the validity of fields
    $msg=validateProfile();
    if(is_string($msg)){
      $_SESSION['error']=$msg;
      header("Location:add.php");
      return;
    }
//check the validity of position edu_fields
    $msg=validatePos();
    if(is_string($msg)){
      $_SESSION['error']=$msg;
      header("Location:add.php");
      return;
    }

    //check the validity of education fields
    $msg=validateEdu();
    if(is_string($msg)){
      $_SESSION['error']=$msg;
      header("Location:add.php");
      return;
    }
    //insert into profile table
      $stmt=$pdo->prepare('INSERT INTO profile (user_id,first_name,last_name,email,headline,summary) VALUES ( :id, :first_name, :last_name, :email,:headline,:summary)');
      $stmt->execute(array(
        ':id'=>$_SESSION['user_id'],
        ':first_name'=>$_POST['first_name'],
        ':last_name'=>$_POST['last_name'],
        'email'=>$_POST['email'],
        ':headline'=>$_POST['headline'],
        ':summary'=>$_POST['summary']
      ));
      //check utils.php
      //used to insert positions into position table
      $profile_id=$pdo->lastInsertId();
      InsertPositions($pdo,$profile_id);

      //used to add education into education table
      InsertEducations($pdo,$profile_id);

      $_SESSION['success']='added';
      header("Location:index.php");
      return;
  }

 ?>

 <html>
 <title>vikramjit singh add to database</title>
 <body style="padding: 2em; font-family:arial;">
   <h1>PLease enter the automobile data</h1>
   <?php
    flashMessages();
    ?>
   <div>
     <form method='post'>
       <p>First Name:
       <input type='text' name='first_name' >
     </p><p>Last Name:
       <input type='text' name='last_name' >
     </p><p>Email:
       <input type='text' name='email' >
     </p><p>Headline:
       <input type='text' name='headline'>
     </p><p>summary:</br>
       <textarea name="summary" rows="8" cols="80"></textarea>
     <p>Education:
       <input type='submit' value='+' id='addEdu'>
       <div id="edu_fields">
       </div>
     </p><p>Position:
       <input type='submit' value='+' id='addPos'>
       <div id="position_fields">
       </div>
     </p><p>
       <input type='submit' value='Add' name='add'>
       <input type='submit' value='Cancel' name='cancel'>
     </p>
   </form>
   <script>
   countPos=0;
   countEdu=0;
   $(document).ready(function(){
     console.log('Document ready called');
     $('#addPos').click(function(event){
       event.preventDefault();
       if(countPos>=9){
         alert("Maximum of nine position entries exceeded");
         return;
       }
       countPos++;
       console.log("Adding position"+countPos);
       $('#position_fields').append(
         '<div id="position'+countPos+'">\
         <p>Year:<input type="text" name="year'+countPos+'" value=""/>\
         <input type= "button" value="-"\
         onclick="$(\'#position'+countPos+'\').remove();return false;"></p>\
         <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
         </div>'
       );
     });
          $('#addEdu').click(function(event){
            event.preventDefault();
            if(countEdu>=9){
              alert("Maximum of nine position entries exceeded");
              return;
            }
            countEdu++;
            console.log("Adding position"+countEdu);
            var source=$("#edu-template").html();
            $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));
           $('.school').autocomplete({source:"school.php"});
          });
    
   });
   </script>

   <script id="edu-template" type="text">
   <div id="edu@COUNT@">
   <p>Year: <input type="text" name="edu_year@COUNT@" value=""/>
   <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
   <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value=""/>
   </p>
   </div>
   </script>

 </div>
</body>
</html>
