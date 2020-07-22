<?php // line 1 added to trigger color syntax highlight
function validateProfile(){
  if(strlen($_POST['first_name'])==0 || strlen($_POST['last_name'])==0 || strlen($_POST['email'])==0 || strlen($_POST['headline'])==0 || strlen($_POST['summary'])==0)
  return 'All fields are required';

  if(strpos($_POST['email'],'@')===false)
  return 'Email address must contain @';

  return true;
}
function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    if($year<'1900' || $year>'2020')
     return "Bad Year";
    }
  }
  return true;
}
function validateEdu() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $year = $_POST['edu_year'.$i];
    $desc = $_POST['edu_school'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    if($year<'1900' || $year>'2020')
     return "Bad Year";
    }
  }
  return true;
}
function InsertPositions($pdo,$profile_id)
{
  $rank=1;
  for($i=1;$i<=9;$i++)
  {
    if(!isset($_POST['year'.$i])) continue;
    if(!isset($_POST['desc'.$i])) continue;
    $year=$_POST['year'.$i];
    $desc=$_POST['desc'.$i];

    $stmt=$pdo->prepare('INSERT INTO Position (profile_id,rank,year,description) VALUES (:pid,:rank,:year,:desc)');
    $stmt->execute(array(
      ':pid'=>$profile_id,
      ':rank'=>$rank,
      ':year'=>$year,
      ':desc'=>$desc
    ));
    $rank++;
  }
}
function InsertEducations($pdo,$profile_id)
{
  $rank=1;
  for($i=1;$i<=9;$i++)
  {
    if(!isset($_POST['edu_year'.$i])) continue;
    if(!isset($_POST['edu_school'.$i])) continue;
    $year=$_POST['edu_year'.$i];
    $school=$_POST['edu_school'.$i];

    $institution_id=false;
    $stmt=$pdo->prepare('SELECT institution_id FROM Institution WHERE name=:name');
    $stmt->execute(array(':name'=>$school));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if(!$row)
    {
      $stmt=$pdo->prepare('INSERT INTO Institution(name) VALUES (:name)');
      $stmt->execute(array(':name'=>$school));
      $institution_id=$pdo->lastInsertId();
    }
    else {
      $institution_id=$row['institution_id'];
    }
    $stmt=$pdo->prepare('INSERT INTO Education (profile_id,institution_id,rank,year) VALUES (:pid,:uid,:rank,:year)');
    $stmt->execute(array(
      ':pid'=>$profile_id,
      ':uid'=>$institution_id,
      ':rank'=>$rank,
      ':year'=>$year
    ));
    $rank++;
  }
}
function flashMessages(){
  if(isset($_SESSION['error']))
  {
    echo ('<p style="color:red">'.$_SESSION['error'].'</p>');
    unset($_SESSION['error']);
  }
}
function loadPos($pdo,$profile_id){
  $stmt=$pdo->prepare('SELECT * FROM Position WHERE profile_id=:prof ORDER BY rank');
  $stmt->execute(array(
    ':prof'=>$profile_id
  ));
  $positions=array();
  while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
    $positions[]=$row;
  }
  return $positions;
}
function loadEdu($pdo,$profile_id){
  $stmt=$pdo->prepare('SELECT year,name FROM Education JOIN Institution
    ON Education.institution_id=Institution.institution_id WHERE profile_id=:prof ORDER BY rank');
  $stmt->execute(array(
    ':prof'=>$profile_id
  ));
  $educations=$stmt->fetchAll(PDO::FETCH_ASSOC);
  return $educations;
}
