<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/schemas.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$tables=['p'=>[['a'=>1,'b'=>'x'],['a'=>'1','b'=>'x']],'c'=>[['a'=>1,'b'=>'x']]];$s=['primary'=>[['table'=>'p','fields'=>['a','b']]],'foreign'=>[['table'=>'c','fields'=>['a','b'],'references'=>['table'=>'p','fields'=>['a','b']]]]];check(padSchema('relation',$tables,$s)['valid']);$tables['p'][]=$tables['p'][0];$r=padSchema('relation',$tables,$s);check($r['issues'][0]['code']==='unique'&&$r['issues'][0]['path']==='$["p"][2]');array_pop($tables['p']);$tables['c'][0]['a']=2;check(padSchema('relation',$tables,$s)['issues'][0]['code']==='foreign');$tables['c'][0]['a']=NULL;check(!padSchema('relation',$tables,$s)['valid']);$s['foreign'][0]['null']='skip';check(padSchema('relation',$tables,$s)['valid']);
$tables['c'][0]=['a'=>NULL];check(padSchema('relation',$tables,$s)['issues'][0]['code']==='missing');$s['primary'][0]['fields']=[['bad']];check(!padSchema('relation',$tables,$s)['valid']);
echo "schemas:relation fixtures passed\n";
