<?php
// Run: php tests/subsystems/application-calendars-example.php
if(!defined('PAD')){define('PAD',dirname(__DIR__,2).'/pad/');function padDirList($p){return scandir($p);}require PAD.'lib/subsystem.php';require PAD.'lib/recurrence.php';}
echo json_encode(padOccurrences('businessday',['start'=>'2026-12-24','holidays'=>['2026-12-25'],'count'=>4],'2026-12-01','2027-01-01'),JSON_PRETTY_PRINT),"\n";
echo json_encode(padOccurrences('rrule',['start'=>'2026-01-30','rrule'=>'FREQ=MONTHLY;BYDAY=FR;BYSETPOS=-1;COUNT=3'],'2026-01-01','2026-05-01'),JSON_PRETTY_PRINT),"\n";
?>
