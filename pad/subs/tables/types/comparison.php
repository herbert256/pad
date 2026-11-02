<?php
include_once PAD.'subs/tables/_lib/structured.php';
try { return padTableStructuredTag('comparison'); } catch(Exception $e) {padError($e->getMessage());return '';}
?>
