<?php
include "objects.php";

// create new database
$db = new Database();


//3. Test: Create new slots for given lecturer id ($booker,$duration,$start_time,$end_time,$lecturer)
$slot = new ConsultationSlot(null,60,"2023-12-22 11:30:00","2023-12-21 12:30:00",4);
$db->writeSlotToDB($slot);