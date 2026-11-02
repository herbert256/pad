<?php
include_once PAD.'subs/timelines/_lib/audit.php';try{return padTimelineAudit((array)$padTimelineRows,(string)padTagParm('timezone','UTC'),(string)padTagParm('title','Audit history'));}catch(Exception $e){padError($e->getMessage());return '';}
