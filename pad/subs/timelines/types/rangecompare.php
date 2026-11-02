<?php
include_once PAD.'subs/timelines/_lib/rangecompare.php';try{return padTimelineRangeCompare((array)$padTimelineRows,(string)padTagParm('timezone','UTC'),(string)padTagParm('title','Planned and actual'));}catch(Exception $e){padError($e->getMessage());return '';}
