
<?php
include "objects.php";

// create new database
$db = new Database();

// 1. Test: create  student object $firstname,$lastname,$matrnr,$email,$pw
$student = new Student("Tim","Mustermann",617772,"Tim@gmail.com","passwort13");
//write student to database if password ist valid (must contain at least one upper case letter and the length is at least 6)
if($student->pw_isvalid()){
    $db->writeStudentToDB($student);
}else{
    echo "Password is not valid!";
}
?>