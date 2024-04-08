<?php
include "objects.php";

// create new database
$db = new Database();

//4. Test: Show all slots of a lecturer; Take a ID which is given in your Database
$slots = $db->getSlotsByLecId(1);
foreach($slots as $slot){
    $slot->show();
}
