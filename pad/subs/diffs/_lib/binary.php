<?php
include_once PAD.'subs/diffs/_lib/compare.php';
function padDiffBinary($a,$b,$options=[]){
 if(array_diff(array_keys($options),['maxBytes','preview','maxRanges']))throw new InvalidArgumentException('binary supports maxBytes and preview');$ranges=$options['maxRanges']??10000;if(!is_int($ranges)||$ranges<1||$ranges>10000)throw new InvalidArgumentException('maxRanges must be 1..10000');$max=$options['maxBytes']??1048576;$preview=$options['preview']??32;
 if(!is_string($a)||!is_string($b)||!is_int($max)||$max<1||$max>1048576||strlen($a)+strlen($b)>$max||!is_int($preview)||$preview<0||$preview>64)throw new InvalidArgumentException('binary requires strings within maxBytes (1..1048576 combined) and preview 0..64');
 $out=[];$n=max(strlen($a),strlen($b));for($i=0;$i<$n;){if(isset($a[$i],$b[$i])&&$a[$i]===$b[$i]){$i++;continue;}$start=$i;do{$i++;}while($i<$n&&(!isset($a[$i],$b[$i])||$a[$i]!==$b[$i]));$old=max(0,min($i,strlen($a))-$start);$new=max(0,min($i,strlen($b))-$start);if(count($out)>=$ranges)throw new InvalidArgumentException('binary output exceeds maxRanges');$out[]=['op'=>$old&&$new?'replace':($old?'remove':'add'),'path'=>'/bytes/'.$start,'offset'=>$start,'length'=>$i-$start,'beforeLength'=>$old,'afterLength'=>$new,'before'=>bin2hex(substr($a,$start,min($old,$preview))),'after'=>bin2hex(substr($b,$start,min($new,$preview))),'previewTruncated'=>$old>$preview||$new>$preview];}
 return $out;
}
