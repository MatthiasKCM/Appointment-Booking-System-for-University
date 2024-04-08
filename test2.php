<?php
include "objects.php";

// create new database
$db = new Database();

 


//2. Test: Create a new Lectuerer 
$lecturer = new Lecturer("Bernd","Muster","Wasswort3212");
if($lecturer->pw_isvalid()){
    $db->writeLecturerToDB($lecturer);
}else{
    echo "Password is not valid!";
}