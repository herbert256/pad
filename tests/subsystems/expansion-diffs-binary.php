<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/diff.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
function padWidgetOnce($s){return FALSE;}function padUnprotectQuotes($s){return $s;}
$r=padDiffCompare('binary',"\x00AB\xff","\x00CB\xffx");check(count($r)===2);check($r[0]['offset']===1&&$r[0]['before']==='41'&&$r[0]['after']==='43');check($r[1]['offset']===4&&$r[1]['beforeLength']===0&&$r[1]['after']==='78');check(padDiffCompare('binary',"\xff\0","\xff\0")===[]);$r=padDiffCompare('binary',str_repeat('a',100),str_repeat('b',100),['preview'=>2]);check($r[0]['length']===100&&$r[0]['before']==='6161'&&$r[0]['previewTruncated']);rejects(fn()=>padDiffCompare('binary','aaaa','bbbb',['maxBytes'=>4]));
rejects(fn()=>padDiffCompare('binary','axbxc','aybyc',['maxRanges'=>1]));
echo "diffs:binary fixtures passed\n";
