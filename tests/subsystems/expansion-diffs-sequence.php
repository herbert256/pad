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
$r=padDiffCompare('sequence',['a','b','c'],['b','a','c']);check(count($r)===1&&$r[0]['op']==='move');check(padDiffCompare('sequence',[1,'1'],[1,'1'])===[]);$r=padDiffCompare('sequence',['a','b'],['x','a','b']);check(count($r)===1&&$r[0]['op']==='add');$r=padDiffCompare('sequence',[['id'=>1,'v'=>2]],[['id'=>1,'v'=>3]],['key'=>'id']);check($r[0]['op']==='replace'&&$r[0]['before']===2&&$r[0]['after']===3);rejects(fn()=>padDiffCompare('sequence',[1,1],[]));$r=padDiffCompare('sequence',['<script>'],[]);check(!str_contains(padDiffRender($r),'<script>'));
function padUnprotect($s){return $s;}require PAD.'subs/diffs/_lib/structured.php';$type=include PAD.'subs/diffs/types/sequence.php';check(str_contains($type['render']('["a","b"]','["b","a"]',['key'=>FALSE,'from'=>'Before','to'=>'After']),'move /'));check(str_contains($type['render']('[{"id":1,"v":2}]','[{"id":1,"v":3}]',['key'=>'id','from'=>'Before','to'=>'After']),'/0/v'));
echo "diffs:sequence fixtures passed\n";
