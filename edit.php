
<?php
session_start();
require_once 'pdo.php';
require_once 'utils.php';
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
if(isset($_POST['cancel']))
{
    header("Location:index.php");
    return;
}
$first_name=htmlentities($row['first_name']);
$last_name=htmlentities($row['last_name']);
$email=htmlentities($row['email']);
$headline=htmlentities($row['headline']);
$summary=htmlentities($row['summary']);

if(isset($_POST['edit']))
{
    $msg=validateProfile();
    if(is_string($msg)){
      $_SESSION['error']=$msg;
      header("Location:edit.php?id=".$_GET['id']);
      return;
    }

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
  $s=$pdo->prepare("UPDATE profile SET first_name=:first_name, last_name=:last_name, email=:email, headline=:headline, summary=:summary WHERE profile_id=:id");
  $s->execute(array(
    ':first_name'=>$_POST['first_name'],
    ':last_name'=>$_POST['last_name'],
    ':email'=>$_POST['email'],
    ':headline'=>$_POST['headline'],
    ':summary'=>$_POST['summary'],
    ':id'=>$_GET['id']
  ));
    $profile_id=$_GET['id'];
  $stmt=$pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
  $stmt->execute(array(
    ':pid'=>$_GET['id']
  ));

  InsertPositions($pdo,$profile_id);

  $stmt=$pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
  $stmt->execute(array(':pid'=>$_GET['id']));

  InsertEducations($pdo,$profile_id);


  $_SESSION['success']='Record edited';
  header("Location: index.php");
  return;
}
$positions=loadPos($pdo,$_GET['id']);
$educations=loadEdu($pdo,$_GET['id']);
 ?>
 <html>
 <title>vikramjit singh</title>
 <body style="font-family:arial;padding:3em;">
   <h2>Edit your data here:</h2>
   <?php
    flashMessages();
    ?>
   <form method='post'>
     <p>First name:
       <input type='text' name='first_name' value='<?=$first_name?>'>
     </p><p>Last name:
       <input type='text' name='last_name' value='<?=$last_name?>'>
     </p><p>Email:
       <input type='text' name='email' value='<?=$email?>'>
     </p><p>Headline:
       <input type='text' name='headline' value='<?=$headline?>'>
     </p><p>summary</br>
       <textarea name="summary" rows="8" cols="80"><?=$summary?></textarea>
       <?php
       $e=0;
       echo('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
       echo('<div id="edu_fields">'."\n");
       foreach($educations as $edu){
         $e++;
         echo('<div id="edu'.$e.'">'."\n");
         echo('<p>Year: <input type="text" name="edu_year'.$e.'"');
         echo('value="'.$edu['year'].'"/>'."\n");
         echo('<input type="button" value="-"');
         echo('onclick="$(\'#edu'.$e.'\').remove();return false;">'."\n");
         echo("</p>\n".'School: </br>');
         echo('<input type="text" size="80" name="edu_school'.$e.'" class="school" value="'.$edu['name'].'"/>'."\n");
         echo("\n</div>\n");
       }
       echo("</div></p>\n");
        $pos=0;
        echo('<p>Position:<input type="submit" id="addPos" value="+">'."\n");
        echo('<div id="position_fields">'."\n");
        foreach($positions as $position){
          $pos++;
          echo('<div id="position'.$pos.'">'."\n");
          echo('<p>Year: <input type="text" name="year'.$pos.'"');
          echo('value="'.$position['year'].'"/>'."\n");
          echo('<input type="button" value="-"');
          echo('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
          echo("</p>\n".'Description:</br>');
          echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
          echo(htmlentities($position['description'])."\n");
          echo("\n</textarea>\n</div>\n");
        }
        echo("</div></p>\n");
        ?>
        <p>
       <input type='submit' value='Save' name='edit'>
       <input type='submit' value='Cancel' name='cancel'>
     </p>
   </form>

   <script>
   countPos=<?=$pos?>;
   countEdu=<?=$e?>;
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
          $('.school').autocomplete({source:"school.php"});
   });
   </script>
   <script id="edu-template" type="text">
   <div id="edu@COUNT@">
   <p>Year: <input type="text" name="edu_year@COUNT@" value=""/>
   <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
   <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value=""/>
   </p><p></p>
   </div>
   </script>
 </body>
 </html>
