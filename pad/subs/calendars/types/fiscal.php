<?php
include_once PAD.'subs/calendars/_lib/fiscal.php';
$padFiscalValue=function($v){if(is_string($v)&&padArrayCheck($v))return padArrayValue($v);return $v;};
try{$rows=padCalendarFiscal($padFiscalValue(padTagParm('periods',[])),padTagParm('from',NULL),padTagParm('to',NULL));$h='<table class="pad-fiscal"><caption>'.padCalendarEscape((string)padTagParm('label','Fiscal periods')).'</caption><thead><tr><th>Period</th><th>Start</th><th>End (exclusive)</th></tr></thead><tbody>';foreach($rows as $r)$h.='<tr><th scope="row">'.padCalendarEscape($r['label']).'</th><td><time datetime="'.$r['start'].'">'.$r['start'].'</time></td><td><time datetime="'.$r['end'].'">'.$r['end'].'</time></td></tr>';return $h.'</tbody></table>';}catch(Exception $e){padError($e->getMessage());return '';}
