<?php
include_once PAD.'subs/printables/_lib/checklist.php';
try{return padPrintableChecklistPlan();}catch(Exception $e){padError($e->getMessage());return [];}
