<?php
include_once PAD.'subs/diffs/_lib/binary.php';
return ['compare'=>'padDiffBinary','render'=>fn($a,$b,$o)=>padDiffRecordsHtml(padDiffBinary($a,$b),FALSE,$o['from'],$o['to'])];
