<?php
include "objects.php";

// create new database
$db = new Database();


//5. Test: Book slot (slotID, bookerID)
if($db->reserveSlot(1,4)){
    echo "Slot has been successfully reserved!";
}