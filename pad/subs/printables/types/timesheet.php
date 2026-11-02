<?php
include_once PAD.'subs/printables/_lib/timesheet.php';
try{return padPrintableTimesheetPlan();}catch(Exception $e){padError($e->getMessage());return [];}
